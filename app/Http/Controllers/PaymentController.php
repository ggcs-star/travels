<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Bookings\BookingService;
use App\Services\Payments\RazorpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly RazorpayService $razorpay
    ) {}

    public function checkout(Booking $booking, Request $request): View|RedirectResponse
    {
        $this->authorizeBooking($booking, $request);
        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return redirect()->route('bookings.show', $booking);
        }

        if (! $this->razorpay->isConfigured()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Online payments are temporarily unavailable. Please contact us to complete your booking.');
        }

        try {
            $order = $this->razorpay->createOrder($booking);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'We could not start the payment. Please try again shortly.');
        }

        $payment = $booking->payments()->create([
            'provider' => 'razorpay',
            'provider_order_id' => $order['id'],
            'amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'status' => Payment::STATUS_CREATED,
            'metadata' => [
                'provider_status' => $order['status'] ?? null,
            ],
        ]);

        return view('payments.razorpay', compact('booking', 'payment', 'order'));
    }

    public function verify(Booking $booking, Request $request): RedirectResponse
    {
        $this->authorizeBooking($booking, $request);

        $data = $request->validate([
            'razorpay_payment_id' => ['required', 'string', 'max:100'],
            'razorpay_order_id' => ['required', 'string', 'max:100'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $payment = $booking->payments()
            ->where('provider', 'razorpay')
            ->where('provider_order_id', $data['razorpay_order_id'])
            ->latest('id')
            ->firstOrFail();

        if (! $this->razorpay->verifyPaymentSignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        )) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Payment verification failed. No money was captured by this site.');
        }

        $this->bookingService->confirmPayment(
            $payment,
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        );

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Payment received. Your booking is confirmed.');
    }

    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->getContent();

        if (! $this->razorpay->verifyWebhookSignature(
            $payload,
            $request->header('X-Razorpay-Signature')
        )) {
            return response('Invalid signature.', 400);
        }

        $event = $request->json('event');
        $entity = $request->input('payload.payment.entity', []);

        if ($event !== 'payment.captured' || empty($entity['order_id'])) {
            return response('Ignored.', 200);
        }

        $payment = Payment::query()
            ->where('provider', 'razorpay')
            ->where('provider_order_id', $entity['order_id'])
            ->latest('id')
            ->first();

        if (! $payment || $payment->status === Payment::STATUS_PAID) {
            return response('OK', 200);
        }

        try {
            $this->bookingService->confirmPayment(
                $payment,
                $entity['id'],
                $request->header('X-Razorpay-Signature'),
                ['webhook_event' => $event]
            );
        } catch (ValidationException $exception) {
            Log::warning('Razorpay webhook ignored for non-payable booking.', [
                'payment_id' => $payment->id,
            ]);
        }

        return response('OK', 200);
    }

    private function authorizeBooking(Booking $booking, Request $request): void
    {
        abort_unless(
            $booking->user_id === $request->user()->id || $request->user()->isAdmin(),
            403
        );
    }
}
