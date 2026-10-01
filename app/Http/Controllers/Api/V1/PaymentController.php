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
public function createOrder(Request $request, Booking $booking)
{
try {
$authorizationResponse = $this->authorizeBooking(
$request,
$booking
);
if ($authorizationResponse) {
return $authorizationResponse;
}
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
$payableAmount = (float) $booking->payableAmount();
if ($payableAmount <= 0) {
return $this->error(
'The payable amount must be greater than zero.',
422
);
}
$currency = strtoupper(
(string) ($booking->currency ?: 'INR')
);
try {
$order = $this->razorpay->createOrder($booking);
} catch (Throwable $exception) {
Log::error('Razorpay order creation failed.', [
'user_id' => $request->user()?->id,
'booking_id' => $booking->id,
'booking_number' => $booking->booking_number,
'amount' => $payableAmount,
'currency' => $currency,
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
if (
! is_array($order)
|| empty($order['id'])
) {
Log::error('Invalid Razorpay order response.', [
'user_id' => $request->user()?->id,
'booking_id' => $booking->id,
'response' => $order,
]);
return $this->error(
'We could not start the payment. Please try again shortly.',
502
);
}
$expectedAmountPaise = (int) round(
$payableAmount * 100
);
$providerAmountPaise = isset($order['amount'])
? (int) $order['amount']
: null;
$providerCurrency = strtoupper(
(string) ($order['currency'] ?? '')
);
if (
$providerAmountPaise !== $expectedAmountPaise
|| $providerCurrency !== $currency
) {
Log::error('Razorpay order amount/currency mismatch.', [
'booking_id' => $booking->id,
'razorpay_order_id' => $order['id'],
'expected_amount_paise' => $expectedAmountPaise,
'provider_amount_paise' => $providerAmountPaise,
'expected_currency' => $currency,
'provider_currency' => $providerCurrency,
]);
return $this->error(
'The payment amount could not be validated. Please try again.',
502
);
}
try {
$payment = $booking->payments()->create([
'provider' => 'razorpay',
'provider_order_id' => $order['id'],
'amount' => $payableAmount,
'currency' => $currency,
'status' => Payment::STATUS_CREATED,
'metadata' => [
'provider_status' => $order['status'] ?? null,
'provider_amount_paise' =>
$providerAmountPaise,
'provider_currency' =>
$providerCurrency,
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
return $this->success(
[
'payment_id' => $payment->id,
'order' => $order,
'key_id' => $this->paymentSettings->getKeyId(),
'amount' => $payableAmount,
'amount_paise' => $expectedAmountPaise,
'currency' => $currency,
'booking_id' => $booking->id,
'booking_number' => $booking->booking_number,
'expires_at' => $booking->expires_at,
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
public function verify(Request $request, Booking $booking)
{
try {
$authorizationResponse = $this->authorizeBooking(
$request,
$booking
);
if ($authorizationResponse) {
return $authorizationResponse;
}
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
->first();
if (! $payment) {
return $this->error(
'The payment order could not be found.',
404
);
}
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
try {
$signatureValid =
$this->razorpay->verifyPaymentSignature(
$data['razorpay_order_id'],
$data['razorpay_payment_id'],
$data['razorpay_signature']
);
} catch (Throwable $exception) {
Log::error(
'Razorpay signature verification exception.',
[
'user_id' => $request->user()?->id,
'booking_id' => $booking->id,
'payment_id' => $payment->id,
'razorpay_order_id' =>
$data['razorpay_order_id'],
'exception' => get_class($exception),
'message' => $exception->getMessage(),
]
);
return $this->error(
'Payment verification could not be completed. Please try again.',
502
);
}
if (! $signatureValid) {
$this->markPaymentFailed(
$payment,
'invalid_checkout_signature'
);
return $this->error(
'Payment verification failed. Please contact support if money was deducted.',
422
);
}
try {
$providerPayment =
$this->razorpay->fetchPayment(
$data['razorpay_payment_id']
);
} catch (Throwable $exception) {
Log::error(
'Razorpay payment fetch failed.',
[
'user_id' => $request->user()?->id,
'booking_id' => $booking->id,
'payment_id' => $payment->id,
'razorpay_payment_id' =>
$data['razorpay_payment_id'],
'razorpay_order_id' =>
$data['razorpay_order_id'],
'exception' => get_class($exception),
'message' => $exception->getMessage(),
]
);
return $this->error(
'Payment was received from Razorpay but could not be verified right now. Please try again.',
502
);
}
if (
empty($providerPayment['id'])
|| (string) $providerPayment['id']
!== (string) $data['razorpay_payment_id']
) {
$this->markPaymentFailed(
$payment,
'provider_payment_id_mismatch'
);
return $this->error(
'The payment could not be validated.',
422
);
}
if (
empty($providerPayment['order_id'])
|| (string) $providerPayment['order_id']
!== (string) $data['razorpay_order_id']
) {
$this->markPaymentFailed(
$payment,
'provider_order_id_mismatch'
);
Log::warning(
'Razorpay payment order mismatch.',
[
'booking_id' => $booking->id,
'payment_id' => $payment->id,
'expected_order_id' =>
$data['razorpay_order_id'],
'provider_order_id' =>
$providerPayment['order_id'] ?? null,
]
);
return $this->error(
'The payment order could not be validated.',
422
);
}
$expectedAmountPaise = (int) round(
((float) $payment->amount) * 100
);
$providerAmountPaise = isset($providerPayment['amount'])
? (int) $providerPayment['amount']
: null;
if (
$providerAmountPaise === null
|| $providerAmountPaise !== $expectedAmountPaise
) {
$this->markPaymentFailed(
$payment,
'provider_amount_mismatch',
[
'expected_amount_paise' =>
$expectedAmountPaise,
'provider_amount_paise' =>
$providerAmountPaise,
]
);
Log::warning(
'Razorpay payment amount mismatch.',
[
'booking_id' => $booking->id,
'payment_id' => $payment->id,
'expected_amount_paise' =>
$expectedAmountPaise,
'provider_amount_paise' =>
$providerAmountPaise,
]
);
return $this->error(
'The payment amount could not be validated. Please contact support if money was deducted.',
422
);
}
$expectedCurrency = strtoupper(
(string) ($payment->currency ?: 'INR')
);
$providerCurrency = strtoupper(
(string) ($providerPayment['currency'] ?? '')
);
if ($providerCurrency !== $expectedCurrency) {
$this->markPaymentFailed(
$payment,
'provider_currency_mismatch',
[
'expected_currency' =>
$expectedCurrency,
'provider_currency' =>
$providerCurrency,
]
);
return $this->error(
'The payment currency could not be validated.',
422
);
}
$providerStatus = strtolower(
(string) ($providerPayment['status'] ?? '')
);
if ($providerStatus !== 'captured') {
Log::warning(
'Razorpay payment is not captured.',
[
'booking_id' => $booking->id,
'payment_id' => $payment->id,
'razorpay_payment_id' =>
$data['razorpay_payment_id'],
'status' => $providerStatus,
]
);
return $this->error(
'The payment has not been captured yet. Please try again after the payment is completed.',
422
);
}
try {
$confirmedBooking =
$this->bookingService->confirmPayment(
$payment,
$data['razorpay_payment_id'],
$data['razorpay_signature'],
[
'source' => 'api_verify',
'provider_status' =>
$providerStatus,
'provider_order_id' =>
$providerPayment['order_id'] ?? null,
'provider_payment_id' =>
$providerPayment['id'] ?? null,
'provider_amount_paise' =>
$providerAmountPaise,
'provider_currency' =>
$providerCurrency,
'provider_method' =>
$providerPayment['method'] ?? null,
'provider_captured' =>
(bool) (
$providerPayment['captured']
?? false
),
'provider_created_at' =>
$providerPayment['created_at']
?? null,
]
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
private function markPaymentFailed(
Payment $payment,
string $reason,
array $extraMetadata = []
): void {
try {
$metadata = is_array($payment->metadata)
? $payment->metadata
: [];
$metadata['verification_failure'] = [
'reason' => $reason,
'at' => now()->toIso8601String(),
];
if ($extraMetadata !== []) {
$metadata['verification_failure'] = array_merge(
$metadata['verification_failure'],
$extraMetadata
);
}
$payment->update([
'status' => Payment::STATUS_FAILED,
'metadata' => $metadata,
]);
} catch (Throwable $exception) {
Log::error(
'Failed to mark Razorpay payment as failed.',
[
'payment_id' => $payment->id,
'booking_id' => $payment->booking_id,
'reason' => $reason,
'exception' => get_class($exception),
'message' => $exception->getMessage(),
]
);
}
}
private function authorizeBooking(
Request $request,
Booking $booking
) {
$user = $request->user();
if (! $user) {
return $this->error(
'Unauthenticated.',
401
);
}
if ((int) $booking->user_id !== (int) $user->id) {
return $this->error(
'Booking not found.',
404
);
}
return null;
}
}