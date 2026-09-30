<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Bookings\BookingService;
use App\Services\Payments\PaymentSettingsService;
use App\Services\Payments\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentController extends ApiController
{
    public function __construct(
        protected BookingService $bookingService,
        protected RazorpayService $razorpay,
        protected PaymentSettingsService $paymentSettings,
    ) {
    }

    /**
     * Create Razorpay order for a booking.
     *
     * POST /api/v1/bookings/{booking}/payment/order
     */
    public function createOrder(Request $request, Booking $booking)
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            $authorizationResponse = $this->authorizeBooking(
                $request,
                $booking
            );

            if ($authorizationResponse) {
                return $authorizationResponse;
            }

            /*
            |--------------------------------------------------------------------------
            | Expire Payment Hold If Required
            |--------------------------------------------------------------------------
            */

            $booking = $this->bookingService->expireIfPastDue($booking);

            /*
            |--------------------------------------------------------------------------
            | Verify Booking Is Payable
            |--------------------------------------------------------------------------
            */

            if (! $booking->isPayable()) {
                return $this->error(
                    'This booking is no longer available for payment.',
                    422
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Razorpay Configuration
            |--------------------------------------------------------------------------
            */

            if (! $this->razorpay->isConfigured()) {
                return $this->error(
                    'Online payments are temporarily unavailable. Please try again later.',
                    503
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Payable Amount
            |--------------------------------------------------------------------------
            */

            $payableAmount = $booking->payableAmount();

            if ($payableAmount <= 0) {
                return $this->error(
                    'The payable amount must be greater than zero.',
                    422
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Razorpay Order
            |--------------------------------------------------------------------------
            */

            try {
                $order = $this->razorpay->createOrder($booking);
            } catch (Throwable $exception) {
                Log::error('Razorpay order creation failed.', [
                    'user_id' => $request->user()?->id,
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'amount' => $payableAmount,
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);

                return $this->error(
                    'We could not start the payment. Please try again shortly.',
                    502
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Razorpay Response
            |--------------------------------------------------------------------------
            */

            if (
                ! is_array($order)
                || empty($order['id'])
            ) {
                Log::error('Invalid Razorpay order response.', [
                    'user_id' => $request->user()?->id,
                    'booking_id' => $booking->id,
                ]);

                return $this->error(
                    'We could not start the payment. Please try again shortly.',
                    502
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Store Payment Record
            |--------------------------------------------------------------------------
            */

            try {
                $payment = $booking->payments()->create([
                    'provider' => 'razorpay',

                    'provider_order_id' => $order['id'],

                    'amount' => $payableAmount,

                    'currency' => $booking->currency,

                    'status' => Payment::STATUS_CREATED,

                    'metadata' => [
                        'provider_status' => $order['status'] ?? null,

                        'booking_total_amount' =>
                            (string) $booking->total_amount,

                        'points_redeemed' =>
                            (int) $booking->points_redeemed,

                        'points_discount' =>
                            (string) $booking->points_discount,

                        'payable_amount' =>
                            (string) $payableAmount,
                    ],
                ]);
            } catch (Throwable $exception) {
                Log::error('Payment record creation failed.', [
                    'user_id' => $request->user()?->id,
                    'booking_id' => $booking->id,
                    'razorpay_order_id' => $order['id'],
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);

                return $this->error(
                    'The payment could not be initialized. Please try again.',
                    500
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Payment Response
            |--------------------------------------------------------------------------
            */

            return $this->success(
                [
                    'payment_id' => $payment->id,

                    'order' => $order,

                    'key_id' =>
                        $this->paymentSettings->getKeyId(),

                    'amount' => $payableAmount,

                    'currency' => $booking->currency,

                    'booking_id' => $booking->id,

                    'booking_number' =>
                        $booking->booking_number,

                    'expires_at' =>
                        $booking->expires_at,
                ],
                'Payment order created successfully.',
                201
            );
        } catch (Throwable $exception) {
            Log::error('API payment order error.', [
                'user_id' => $request->user()?->id,
                'booking_id' => $booking->id ?? null,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return $this->error(
                'Unable to create payment order at the moment. Please try again.',
                500
            );
        }
    }

    /**
     * Verify Razorpay payment.
     *
     * POST /api/v1/bookings/{booking}/payment/verify
     */
    public function verify(Request $request, Booking $booking)
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | Authentication + Ownership
            |--------------------------------------------------------------------------
            */

            $authorizationResponse = $this->authorizeBooking(
                $request,
                $booking
            );

            if ($authorizationResponse) {
                return $authorizationResponse;
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Razorpay Response
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Find Payment
            |--------------------------------------------------------------------------
            */

            $payment = $booking->payments()
                ->where('provider', 'razorpay')
                ->where(
                    'provider_order_id',
                    $data['razorpay_order_id']
                )
                ->latest('id')
                ->first();

            if (! $payment) {
                return $this->error(
                    'The payment order could not be found.',
                    404
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Verification Of Already Paid Payment
            |--------------------------------------------------------------------------
            */

            if ($payment->status === Payment::STATUS_PAID) {
                return $this->success(
                    $booking->fresh([
                        'tourPackage',
                        'departure',
                        'travellers',
                        'payments',
                    ]),
                    'Payment has already been verified successfully.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Razorpay Signature
            |--------------------------------------------------------------------------
            */

            try {
                $signatureValid =
                    $this->razorpay->verifyPaymentSignature(
                        $data['razorpay_order_id'],
                        $data['razorpay_payment_id'],
                        $data['razorpay_signature']
                    );
            } catch (Throwable $exception) {
                Log::error('Razorpay signature verification exception.', [
                    'user_id' => $request->user()?->id,
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'razorpay_order_id' =>
                        $data['razorpay_order_id'],
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);

                return $this->error(
                    'Payment verification could not be completed. Please try again.',
                    502
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Invalid Signature
            |--------------------------------------------------------------------------
            */

            if (! $signatureValid) {
                try {
                    $payment->update([
                        'status' => Payment::STATUS_FAILED,
                    ]);
                } catch (Throwable $exception) {
                    Log::error(
                        'Failed to mark payment as failed.',
                        [
                            'payment_id' => $payment->id,
                            'booking_id' => $booking->id,
                            'exception' => get_class($exception),
                            'message' => $exception->getMessage(),
                        ]
                    );
                }

                return $this->error(
                    'Payment verification failed. Please contact support if money was deducted.',
                    422
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Confirm Booking Payment
            |--------------------------------------------------------------------------
            */

            try {
                $confirmedBooking =
                    $this->bookingService->confirmPayment(
                        $payment,
                        $data['razorpay_payment_id'],
                        $data['razorpay_signature']
                    );
            } catch (ValidationException $exception) {
                Log::warning(
                    'Booking payment validation failed.',
                    [
                        'user_id' => $request->user()?->id,
                        'booking_id' => $booking->id,
                        'payment_id' => $payment->id,
                        'errors' => $exception->errors(),
                    ]
                );

                return $this->error(
                    'The payment could not be confirmed.',
                    422,
                    $exception->errors()
                );
            } catch (Throwable $exception) {
                Log::error(
                    'Booking payment confirmation failed.',
                    [
                        'user_id' => $request->user()?->id,
                        'booking_id' => $booking->id,
                        'payment_id' => $payment->id,
                        'exception' => get_class($exception),
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ]
                );

                return $this->error(
                    'Payment was received but booking confirmation could not be completed. Please contact support.',
                    500
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Final Response
            |--------------------------------------------------------------------------
            */

            return $this->success(
                $confirmedBooking->fresh([
                    'tourPackage',
                    'departure',
                    'travellers',
                    'payments',
                ]),
                'Payment received. Your booking is confirmed.'
            );
        } catch (ValidationException $exception) {
            return $this->error(
                'Please check the submitted payment information.',
                422,
                $exception->errors()
            );
        } catch (Throwable $exception) {
            Log::error('API payment verification error.', [
                'user_id' => $request->user()?->id,
                'booking_id' => $booking->id ?? null,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return $this->error(
                'Unable to verify payment at the moment. Please try again.',
                500
            );
        }
    }

    /**
     * Verify authenticated user owns the booking.
     *
     * Returns a JSON error response when authorization fails.
     * Returns null when the user is allowed to continue.
     */
    private function authorizeBooking(
        Request $request,
        Booking $booking
    ) {
        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        if (! $user) {
            return $this->error(
                'Unauthenticated.',
                401
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        if ((int) $booking->user_id !== (int) $user->id) {
            return $this->error(
                'Booking not found.',
                404
            );
        }

        return null;
    }
}   