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

    /**
     * Show payment checkout page.
     */
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
                ->with(
                    'error',
                    'Online payments are temporarily unavailable. Please contact us to complete your booking.'
                );
        }

        /*
         * Important:
         * payableAmount() already includes any approved points discount.
         */
        $payableAmount = $booking->payableAmount();

        /*
         * Razorpay does not support a zero-value order.
         *
         * If 100% points redemption is enabled later, a separate
         * "fully paid by points" confirmation flow should be added.
         */
        if ($payableAmount <= 0) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with(
                    'error',
                    'The booking amount cannot be paid online because the payable amount is zero.'
                );
        }

        try {
            $order = $this->razorpay->createOrder($booking);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('bookings.show', $booking)
                ->with(
                    'error',
                    'We could not start the payment. Please try again shortly.'
                );
        }

        /*
         * Payment amount MUST match the Razorpay order amount.
         *
         * Previously this was:
         * 'amount' => $booking->total_amount,
         *
         * which would be wrong after points redemption.
         */
        $payment = $booking->payments()->create([
            'provider' => 'razorpay',
            'provider_order_id' => $order['id'],
            'amount' => $payableAmount,
            'currency' => $booking->currency,
            'status' => Payment::STATUS_CREATED,
            'metadata' => [
                'provider_status' => $order['status'] ?? null,
                'booking_total_amount' => (string) $booking->total_amount,
                'points_redeemed' => (int) $booking->points_redeemed,
                'points_discount' => (string) $booking->points_discount,
                'payable_amount' => (string) $payableAmount,
            ],
        ]);

        return view(
            'payments.razorpay',
            compact('booking', 'payment', 'order')
        );
    }

    /**
     * Apply points to the booking.
     */
    public function applyPoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking($booking, $request);

        $data = $request->validate([
            'points' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $this->bookingService->applyPoints(
                booking: $booking,
                user: $request->user(),
                points: (int) $data['points'],
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->with(
                    'error',
                    'We could not apply points right now. Please try again.'
                );
        }

        /*
         * Redirecting to checkout is intentional.
         *
         * It creates a NEW Razorpay order using the updated payable amount.
         * This prevents an old Razorpay order from being used after the
         * points discount has changed.
         */
        return redirect()
            ->route('payments.checkout', $booking)
            ->with('success', 'Points applied successfully.');
    }

    /**
     * Remove applied points from the booking.
     */
    public function removePoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking($booking, $request);

        try {
            $this->bookingService->removePoints(
                booking: $booking,
                user: $request->user(),
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->with(
                    'error',
                    'We could not remove points right now. Please try again.'
                );
        }

        /*
         * Fresh checkout = fresh Razorpay order with the original amount.
         */
        return redirect()
            ->route('payments.checkout', $booking)
            ->with('success', 'Applied points have been removed.');
    }

    /**
     * Verify Razorpay payment from the browser.
     */
    public function verify(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking($booking, $request);

        $data = $request->validate([
            'razorpay_payment_id' => [
                'required',
                'string',
                'max:100',
            ],
            'razorpay_order_id' => [
                'required',
                'string',
                'max:100',
            ],
            'razorpay_signature' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $payment = $booking->payments()
            ->where('provider', 'razorpay')
            ->where(
                'provider_order_id',
                $data['razorpay_order_id']
            )
            ->latest('id')
            ->firstOrFail();

        /*
         * Signature verification is mandatory.
         */
        if (! $this->razorpay->verifyPaymentSignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        )) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);

            return redirect()
                ->route('bookings.show', $booking)
                ->with(
                    'error',
                    'Payment verification failed. No money was captured by this site.'
                );
        }

        /*
         * confirmPayment() handles:
         *
         * - payment locking
         * - booking locking
         * - payment status
         * - booking confirmation
         * - point redemption
         * - booking reward points
         */
        $this->bookingService->confirmPayment(
            $payment,
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        );

        return redirect()
            ->route('bookings.show', $booking)
            ->with(
                'success',
                'Payment received. Your booking is confirmed.'
            );
    }

    /**
     * Razorpay webhook.
     */
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

        $entity = $request->input(
            'payload.payment.entity',
            []
        );

        /*
         * Currently we process successful captured payments only.
         */
        if (
            $event !== 'payment.captured'
            || empty($entity['order_id'])
        ) {
            return response('Ignored.', 200);
        }

        $payment = Payment::query()
            ->where('provider', 'razorpay')
            ->where(
                'provider_order_id',
                $entity['order_id']
            )
            ->latest('id')
            ->first();

        /*
         * Idempotency:
         * If webhook arrives more than once after payment is already paid,
         * do nothing.
         */
        if (
            ! $payment
            || $payment->status === Payment::STATUS_PAID
        ) {
            return response('OK', 200);
        }

        try {
            $this->bookingService->confirmPayment(
                $payment,
                $entity['id'],
                $request->header('X-Razorpay-Signature'),
                [
                    'webhook_event' => $event,
                ]
            );
        } catch (ValidationException $exception) {
            Log::warning(
                'Razorpay webhook ignored for non-payable booking.',
                [
                    'payment_id' => $payment->id,
                ]
            );
        }

        return response('OK', 200);
    }

    /**
     * Authorize booking access.
     */
    private function authorizeBooking(
        Booking $booking,
        Request $request
    ): void {
        abort_unless(
            $booking->user_id === $request->user()->id
            || $request->user()->isAdmin(),
            403
        );
    }
}