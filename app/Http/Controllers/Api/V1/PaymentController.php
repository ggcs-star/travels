<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Bookings\BookingService;
use App\Services\Payments\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends ApiController
{
    public function __construct(
        protected BookingService $bookingService,
        protected RazorpayService $razorpay,
        protected \App\Services\Payments\PaymentSettingsService $paymentSettings,
    ) {
    }

    public function createOrder(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return $this->error(
                'This booking is no longer available for payment.',
                422
            );
        }

        if (! $this->razorpay->isConfigured()) {
            return $this->error(
                'Online payments are temporarily unavailable. Please try again later.',
                503
            );
        }

        $payableAmount = $booking->payableAmount();

        if ($payableAmount <= 0) {
            return $this->error(
                'The payable amount must be greater than zero.',
                422
            );
        }

        try {
            $order = $this->razorpay->createOrder($booking);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->error(
                'We could not start the payment. Please try again shortly.',
                502
            );
        }

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

        return $this->success([
            'payment_id' => $payment->id,
            'order' => $order,
            'key_id' => $this->paymentSettings->getKeyId(),
            'amount' => $payableAmount,
            'currency' => $booking->currency,
        ], 'Payment order created successfully.', 201);
    }

    public function verify(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $data = $request->validate([
            'razorpay_payment_id' => ['required', 'string', 'max:100'],
            'razorpay_order_id' => ['required', 'string', 'max:100'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $payment = $booking->payments()
            ->where('provider', 'razorpay')
            ->where('provider_order_id', $data['razorpay_order_id'])
            ->latest('id')
            ->first();

        if (! $payment) {
            return $this->error(
                'The payment order could not be found.',
                404
            );
        }

        if (! $this->razorpay->verifyPaymentSignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        )) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);

            return $this->error(
                'Payment verification failed. Please contact support if money was deducted.',
                422
            );
        }

        try {
            $this->bookingService->confirmPayment(
                $payment,
                $data['razorpay_payment_id'],
                $data['razorpay_signature']
            );
        } catch (ValidationException $exception) {
            return $this->error(
                'The payment could not be confirmed.',
                422,
                $exception->errors()
            );
        }

        return $this->success(
            $booking->fresh([
                'tourPackage',
                'departure',
                'travellers',
                'payments',
            ]),
            'Payment received. Your booking is confirmed.'
        );
    }

    public function applyPoints(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $data = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $updated = $this->bookingService->applyPoints(
            $booking,
            $request->user(),
            (int) $data['points']
        );

        return $this->success(
            $updated,
            'Points applied successfully.'
        );
    }

    public function removePoints(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $updated = $this->bookingService->removePoints(
            $booking,
            $request->user()
        );

        return $this->success(
            $updated,
            'Applied points have been removed.'
        );
    }

    private function authorizeBooking(
        Request $request,
        Booking $booking,
    ): void {
        abort_unless(
            (int) $booking->user_id === (int) $request->user()->id,
            404
        );
    }
}
