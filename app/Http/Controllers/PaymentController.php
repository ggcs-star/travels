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
    ) {
    }
    public function checkout(
        Booking $booking,
        Request $request
    ): View|RedirectResponse {
        $this->authorizeBooking($booking, $request);
        try {
            $booking = $this->bookingService->expireIfPastDue($booking);
            if (! $booking->isPayable()) {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('error', 'This booking is no longer payable.');
            }
            if (! $this->razorpay->isConfigured()) {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'error',
                        'Online payments are temporarily unavailable. Please contact us to complete your booking.'
                    );
            }
            $payableAmount = (float) $booking->payableAmount();
            if ($payableAmount <= 0) {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'error',
                        'The booking amount cannot be paid online because the payable amount is zero.'
                    );
            }
            $expectedAmount = (int) round($payableAmount * 100);
            $expectedCurrency = strtoupper(
                trim((string) ($booking->currency ?: 'INR'))
            );
            $payment = $booking->payments()
                ->where('provider', 'razorpay')
                ->where('status', Payment::STATUS_CREATED)
                ->whereNotNull('provider_order_id')
                ->latest('id')
                ->first();
            if ($payment) {
                $localAmount = (int) round(((float) $payment->amount) * 100);
                $localCurrency = strtoupper(
                    trim((string) ($payment->currency ?: 'INR'))
                );
                if (
                    $localAmount === $expectedAmount
                    && $localCurrency === $expectedCurrency
                ) {
                    try {
                        $existingOrder = $this->razorpay->fetchOrder(
                            (string) $payment->provider_order_id
                        );
                        $providerAmount = isset($existingOrder['amount'])
                            ? (int) $existingOrder['amount']
                            : null;
                        $providerCurrency = strtoupper(
                            trim((string) ($existingOrder['currency'] ?? ''))
                        );
                        $providerStatus = strtolower(
                            trim((string) ($existingOrder['status'] ?? ''))
                        );
                        if (
                            ($existingOrder['id'] ?? null) === $payment->provider_order_id
                            && $providerAmount === $expectedAmount
                            && $providerCurrency === $expectedCurrency
                            && $providerStatus === 'created'
                        ) {
                            $payment->update([
                                'metadata' => array_merge(
                                    (array) $payment->metadata,
                                    [
                                        'provider_status' => $providerStatus,
                                        'provider_amount' => $providerAmount,
                                        'provider_currency' => $providerCurrency,
                                        'payable_amount' => (string) $payableAmount,
                                        'reused_at' => now()->toIso8601String(),
                                    ]
                                ),
                            ]);
                            return view(
                                'payments.razorpay',
                                [
                                    'booking' => $booking,
                                    'payment' => $payment,
                                    'order' => $existingOrder,
                                ]
                            );
                        }
                    } catch (\Throwable $exception) {
                        Log::warning(
                            'Existing Razorpay order could not be reused.',
                            [
                                'booking_id' => $booking->id,
                                'payment_id' => $payment->id,
                                'provider_order_id' => $payment->provider_order_id,
                                'error' => $exception->getMessage(),
                            ]
                        );
                    }
                }
            }
            $order = $this->razorpay->createOrder($booking);
            if (
                empty($order['id'])
                || ! is_string($order['id'])
            ) {
                Log::error(
                    'Razorpay order response did not contain a valid order ID.',
                    [
                        'booking_id' => $booking->id,
                        'response' => $order,
                    ]
                );
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'error',
                        'We could not create a valid payment order. Please try again.'
                    );
            }
            $providerAmount = isset($order['amount'])
                ? (int) $order['amount']
                : null;
            $providerCurrency = strtoupper(
                trim((string) ($order['currency'] ?? ''))
            );
            if (
                $providerAmount !== $expectedAmount
                || $providerCurrency !== $expectedCurrency
            ) {
                Log::error(
                    'Razorpay order amount/currency mismatch.',
                    [
                        'booking_id' => $booking->id,
                        'expected_amount' => $expectedAmount,
                        'provider_amount' => $providerAmount,
                        'expected_currency' => $expectedCurrency,
                        'provider_currency' => $providerCurrency,
                        'provider_order_id' => $order['id'],
                    ]
                );
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'error',
                        'The payment order could not be validated. Please try again.'
                    );
            }
            $payment = $booking->payments()->create([
                'provider' => 'razorpay',
                'provider_order_id' => $order['id'],
                'amount' => $payableAmount,
                'currency' => $expectedCurrency,
                'status' => Payment::STATUS_CREATED,
                'metadata' => [
                    'provider_status' => $order['status'] ?? null,
                    'provider_amount' => $providerAmount,
                    'provider_currency' => $providerCurrency,
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
        } catch (\Throwable $exception) {
            report($exception);
            Log::error(
                'Razorpay checkout failed.',
                [
                    'booking_id' => $booking->id,
                    'user_id' => $request->user()?->id,
                    'error' => $exception->getMessage(),
                ]
            );
            return redirect()
                ->route('bookings.show', $booking)
                ->with(
                    'error',
                    'We could not start the payment. Please try again shortly.'
                );
        }
    }
    public function applyPoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking(
            $booking,
            $request
        );
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
                ->withErrors(
                    $exception->errors()
                )
                ->withInput();
        } catch (\Throwable $exception) {
            report($exception);
            return back()
                ->with(
                    'error',
                    'We could not apply points right now. Please try again.'
                );
        }
        return redirect()
            ->route(
                'payments.checkout',
                $booking
            )
            ->with(
                'success',
                'Points applied successfully.'
            );
    }
    public function removePoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking(
            $booking,
            $request
        );
        try {
            $this->bookingService->removePoints(
                booking: $booking,
                user: $request->user(),
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors(
                    $exception->errors()
                )
                ->withInput();
        } catch (\Throwable $exception) {
            report($exception);
            return back()
                ->with(
                    'error',
                    'We could not remove points right now. Please try again.'
                );
        }
        return redirect()
            ->route(
                'payments.checkout',
                $booking
            )
            ->with(
                'success',
                'Applied points have been removed.'
            );
    }
    public function verify(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $this->authorizeBooking(
            $booking,
            $request
        );
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
            ->where(
                'provider',
                'razorpay'
            )
            ->where(
                'provider_order_id',
                $data['razorpay_order_id']
            )
            ->latest('id')
            ->first();
        if (! $payment) {
            Log::warning(
                'Razorpay payment verification attempted with an unknown order.',
                [
                    'booking_id' =>
                        $booking->id,
                    'razorpay_order_id' =>
                        $data['razorpay_order_id'],
                    'razorpay_payment_id' =>
                        $data['razorpay_payment_id'],
                ]
            );
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->with(
                    'error',
                    'This payment order does not belong to this booking.'
                );
        }
        if (
            $payment->status === Payment::STATUS_PAID
        ) {
            return redirect()
                ->route(
                    'bookings.index'
                )
                ->with(
                    'success',
                    'Payment was already received. Your booking is confirmed.'
                );
        }
        if (! $this->razorpay->verifyPaymentSignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        )) {
            $payment->update([
                'status' =>
                    Payment::STATUS_FAILED,
                'metadata' => array_merge(
                    (array) $payment->metadata,
                    [
                        'verification_failure' =>
                            'invalid_signature',
                        'failed_at' =>
                            now()->toIso8601String(),
                    ]
                ),
            ]);
            Log::warning(
                'Razorpay checkout signature verification failed.',
                [
                    'booking_id' =>
                        $booking->id,
                    'payment_id' =>
                        $payment->id,
                    'provider_order_id' =>
                        $data['razorpay_order_id'],
                    'provider_payment_id' =>
                        $data['razorpay_payment_id'],
                ]
            );
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->with(
                    'error',
                    'Payment verification failed. Please try the payment again.'
                );
        }
        try {
            $providerPayment =
                $this->razorpay->fetchPayment(
                    $data['razorpay_payment_id']
                );
        } catch (\Throwable $exception) {
            report($exception);
            Log::warning(
                'Unable to fetch Razorpay payment for verification.',
                [
                    'booking_id' =>
                        $booking->id,
                    'payment_id' =>
                        $payment->id,
                    'provider_payment_id' =>
                        $data['razorpay_payment_id'],
                ]
            );
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->with(
                    'error',
                    'We could not verify the payment with Razorpay. Please try again.'
                );
        }
        if (
            ($providerPayment['id'] ?? null)
            !== $data['razorpay_payment_id']
        ) {
            return $this->failProviderVerification(
                $payment,
                $booking,
                'payment_id_mismatch',
                'The Razorpay payment could not be verified.'
            );
        }
        if (
            ($providerPayment['order_id'] ?? null)
            !== $payment->provider_order_id
            || ($providerPayment['order_id'] ?? null)
            !== $data['razorpay_order_id']
        ) {
            return $this->failProviderVerification(
                $payment,
                $booking,
                'order_id_mismatch',
                'The Razorpay payment order does not match this booking.'
            );
        }
        $expectedAmount = (int) round(
            ((float) $payment->amount) * 100
        );
        $providerAmount = isset(
            $providerPayment['amount']
        )
            ? (int) $providerPayment['amount']
            : null;
        if (
            $providerAmount === null
            || $providerAmount !== $expectedAmount
        ) {
            Log::warning(
                'Razorpay payment amount mismatch.',
                [
                    'booking_id' =>
                        $booking->id,
                    'payment_id' =>
                        $payment->id,
                    'expected_amount' =>
                        $expectedAmount,
                    'provider_amount' =>
                        $providerAmount,
                    'provider_payment_id' =>
                        $data['razorpay_payment_id'],
                ]
            );
            return $this->failProviderVerification(
                $payment,
                $booking,
                'amount_mismatch',
                'The Razorpay payment amount does not match the booking amount.'
            );
        }
        $expectedCurrency = strtoupper(
            trim(
                (string) (
                    $payment->currency
                    ?: $booking->currency
                    ?: 'INR'
                )
            )
        );
        $providerCurrency = strtoupper(
            trim(
                (string) (
                    $providerPayment['currency']
                    ?? ''
                )
            )
        );
        if (
            $providerCurrency === ''
            || $providerCurrency !== $expectedCurrency
        ) {
            Log::warning(
                'Razorpay payment currency mismatch.',
                [
                    'booking_id' =>
                        $booking->id,
                    'payment_id' =>
                        $payment->id,
                    'expected_currency' =>
                        $expectedCurrency,
                    'provider_currency' =>
                        $providerCurrency,
                    'provider_payment_id' =>
                        $data['razorpay_payment_id'],
                ]
            );
            return $this->failProviderVerification(
                $payment,
                $booking,
                'currency_mismatch',
                'The Razorpay payment currency does not match the booking currency.'
            );
        }
        $providerStatus = strtolower(
            trim(
                (string) (
                    $providerPayment['status']
                    ?? ''
                )
            )
        );
        if ($providerStatus !== 'captured') {
            Log::warning(
                'Razorpay payment is not captured.',
                [
                    'booking_id' =>
                        $booking->id,
                    'payment_id' =>
                        $payment->id,
                    'provider_payment_id' =>
                        $data['razorpay_payment_id'],
                    'provider_status' =>
                        $providerStatus,
                ]
            );
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->with(
                    'error',
                    'The payment has not been captured by Razorpay yet. Please try again shortly.'
                );
        }
        $verificationMetadata = [
            'verification_source' =>
                'razorpay_api',
            'provider_payment_id' =>
                $providerPayment['id'] ?? null,
            'provider_order_id' =>
                $providerPayment['order_id'] ?? null,
            'provider_amount' =>
                $providerAmount,
            'provider_currency' =>
                $providerCurrency,
            'provider_status' =>
                $providerStatus,
            'method' =>
                $providerPayment['method'] ?? null,
            'captured' =>
                $providerPayment['captured'] ?? null,
            'verified_at' =>
                now()->toIso8601String(),
        ];
        try {
            $this->bookingService->confirmPayment(
                $payment,
                $data['razorpay_payment_id'],
                $data['razorpay_signature'],
                $verificationMetadata
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->withErrors(
                    $exception->errors()
                );
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()
                ->route(
                    'bookings.show',
                    $booking
                )
                ->with(
                    'error',
                    'The payment was verified, but we could not complete the booking confirmation. Please contact support.'
                );
        }
        return redirect()
            ->route(
                'bookings.index'
            )
            ->with(
                'success',
                'Payment received. Your booking is confirmed.'
            );
    }
    public function webhook(
        Request $request
    ): \Illuminate\Http\Response {
        $payload = $request->getContent();
        if (! $this->razorpay->verifyWebhookSignature(
            $payload,
            $request->header(
                'X-Razorpay-Signature'
            )
        )) {
            Log::warning(
                'Invalid Razorpay webhook signature.'
            );
            return response(
                'Invalid signature.',
                400
            );
        }
        $event = $request->json('event');
        $entity = $request->input(
            'payload.payment.entity',
            []
        );
        if (
            $event !== 'payment.captured'
            || empty($entity['id'])
            || empty($entity['order_id'])
        ) {
            return response(
                'Ignored.',
                200
            );
        }
        $providerPaymentId =
            (string) $entity['id'];
        $providerOrderId =
            (string) $entity['order_id'];
        $payment = Payment::query()
            ->where(
                'provider',
                'razorpay'
            )
            ->where(
                'provider_order_id',
                $providerOrderId
            )
            ->latest('id')
            ->first();
        if (! $payment) {
            Log::warning(
                'Razorpay webhook received for an unknown order.',
                [
                    'provider_order_id' =>
                        $providerOrderId,
                    'provider_payment_id' =>
                        $providerPaymentId,
                ]
            );
            return response(
                'OK',
                200
            );
        }
        if (
            $payment->status
            === Payment::STATUS_PAID
        ) {
            return response(
                'OK',
                200
            );
        }
        try {
            $providerPayment =
                $this->razorpay->fetchPayment(
                    $providerPaymentId
                );
        } catch (\Throwable $exception) {
            report($exception);
            Log::error(
                'Unable to fetch Razorpay payment from webhook.',
                [
                    'payment_id' =>
                        $payment->id,
                    'provider_payment_id' =>
                        $providerPaymentId,
                    'provider_order_id' =>
                        $providerOrderId,
                ]
            );
            return response(
                'Unable to verify payment.',
                500
            );
        }
        if (
            ($providerPayment['id'] ?? null)
            !== $providerPaymentId
        ) {
            Log::warning(
                'Razorpay webhook payment ID mismatch.',
                [
                    'payment_id' =>
                        $payment->id,
                ]
            );
            return response(
                'Payment verification failed.',
                400
            );
        }
        if (
            ($providerPayment['order_id'] ?? null)
            !== $payment->provider_order_id
        ) {
            Log::warning(
                'Razorpay webhook order ID mismatch.',
                [
                    'payment_id' =>
                        $payment->id,
                    'expected_order_id' =>
                        $payment->provider_order_id,
                    'provider_order_id' =>
                        $providerPayment['order_id']
                        ?? null,
                ]
            );
            return response(
                'Payment verification failed.',
                400
            );
        }
        $expectedAmount = (int) round(
            ((float) $payment->amount) * 100
        );
        $providerAmount = isset(
            $providerPayment['amount']
        )
            ? (int) $providerPayment['amount']
            : null;
        if (
            $providerAmount === null
            || $providerAmount !== $expectedAmount
        ) {
            Log::warning(
                'Razorpay webhook amount mismatch.',
                [
                    'payment_id' =>
                        $payment->id,
                    'expected_amount' =>
                        $expectedAmount,
                    'provider_amount' =>
                        $providerAmount,
                ]
            );
            return response(
                'Payment verification failed.',
                400
            );
        }
        $expectedCurrency = strtoupper(
            trim(
                (string) (
                    $payment->currency
                    ?: 'INR'
                )
            )
        );
        $providerCurrency = strtoupper(
            trim(
                (string) (
                    $providerPayment['currency']
                    ?? ''
                )
            )
        );
        if (
            $providerCurrency !== $expectedCurrency
        ) {
            Log::warning(
                'Razorpay webhook currency mismatch.',
                [
                    'payment_id' =>
                        $payment->id,
                    'expected_currency' =>
                        $expectedCurrency,
                    'provider_currency' =>
                        $providerCurrency,
                ]
            );
            return response(
                'Payment verification failed.',
                400
            );
        }
        $providerStatus = strtolower(
            trim(
                (string) (
                    $providerPayment['status']
                    ?? ''
                )
            )
        );
        if ($providerStatus !== 'captured') {
            return response(
                'Ignored.',
                200
            );
        }
        try {
            $this->bookingService->confirmPayment(
                $payment,
                $providerPaymentId,
                $request->header(
                    'X-Razorpay-Signature'
                ),
                [
                    'webhook_event' =>
                        $event,
                    'verification_source' =>
                        'razorpay_webhook',
                    'provider_payment_id' =>
                        $providerPayment['id']
                        ?? null,
                    'provider_order_id' =>
                        $providerPayment['order_id']
                        ?? null,
                    'provider_amount' =>
                        $providerAmount,
                    'provider_currency' =>
                        $providerCurrency,
                    'provider_status' =>
                        $providerStatus,
                    'method' =>
                        $providerPayment['method']
                        ?? null,
                    'captured' =>
                        $providerPayment['captured']
                        ?? null,
                    'verified_at' =>
                        now()->toIso8601String(),
                ]
            );
        } catch (ValidationException $exception) {
            Log::warning(
                'Razorpay webhook could not confirm booking.',
                [
                    'payment_id' =>
                        $payment->id,
                    'booking_id' =>
                        $payment->booking_id,
                    'errors' =>
                        $exception->errors(),
                ]
            );
            return response(
                'Booking confirmation failed.',
                500
            );
        } catch (\Throwable $exception) {
            report($exception);
            Log::error(
                'Unexpected Razorpay webhook processing failure.',
                [
                    'payment_id' =>
                        $payment->id,
                    'booking_id' =>
                        $payment->booking_id,
                    'provider_payment_id' =>
                        $providerPaymentId,
                ]
            );
            return response(
                'Webhook processing failed.',
                500
            );
        }
        return response(
            'OK',
            200
        );
    }
    private function failProviderVerification(
        Payment $payment,
        Booking $booking,
        string $reason,
        string $message
    ): RedirectResponse {
        $payment->update([
            'status' =>
                Payment::STATUS_FAILED,
            'metadata' => array_merge(
                (array) $payment->metadata,
                [
                    'verification_failure' =>
                        $reason,
                    'failed_at' =>
                        now()->toIso8601String(),
                ]
            ),
        ]);
        return redirect()
            ->route(
                'bookings.show',
                $booking
            )
            ->with(
                'error',
                $message
            );
    }
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
