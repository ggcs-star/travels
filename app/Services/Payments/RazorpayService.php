<?php

namespace App\Services\Payments;

use App\Models\Booking;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RazorpayService
{
    public function __construct(
        protected PaymentSettingsService $settings,
    ) {
    }

    /**
     * Check whether Razorpay is configured.
     */
    public function isConfigured(): bool
    {
        return $this->settings->isConfigured();
    }

    /**
     * Test Razorpay API connection.
     *
     * @throws RequestException
     */
    public function testConnection(): array
    {
        $this->ensureConfigured();

        $this->client()
            ->get('/orders', [
                'count' => 1,
            ])
            ->throw();

        return [
            'message' => 'Razorpay connection successful.',
        ];
    }

    /**
     * Create a Razorpay order.
     *
     * Razorpay receives only the final payable amount.
     *
     * Wallet/points information remains internal to our
     * application and is not sent to Razorpay.
     *
     * @throws RequestException
     */
    public function createOrder(Booking $booking): array
    {
        $this->ensureConfigured();

        /*
         * payableAmount() already represents the final amount
         * after applying any wallet/points discount internally.
         */
        $amount = (float) $booking->payableAmount();

        if ($amount <= 0) {
            throw new \InvalidArgumentException(
                'Payment amount must be greater than zero.'
            );
        }

        $currency = strtoupper(
            trim(
                (string) (
                    $booking->currency ?: 'INR'
                )
            )
        );

        $response = $this->client()
            ->post('/orders', [
                /*
                 * Razorpay expects the amount in the smallest
                 * currency unit.
                 *
                 * Example:
                 * ₹4,500 = 450000 paise
                 */
                'amount' => (int) round(
                    $amount * 100
                ),

                'currency' => $currency,

                /*
                 * Receipt is only our internal booking reference.
                 */
                'receipt' => (string) $booking->booking_number,

                /*
                 * Do NOT send wallet/points information here.
                 *
                 * Razorpay only needs enough information to
                 * identify the order.
                 */
                'notes' => [
                    'booking_number' =>
                        (string) $booking->booking_number,

                    'booking_id' =>
                        (string) $booking->id,
                ],
            ])
            ->throw();

        return $response->json();
    }

    /**
     * Fetch a Razorpay order by provider order ID.
     *
     * Used by the backend to verify that the order exists
     * at Razorpay and matches the local payment.
     *
     * @throws RequestException
     */
    public function fetchOrder(
        string $orderId
    ): array {
        $this->ensureConfigured();

        $orderId = trim($orderId);

        if ($orderId === '') {
            throw new \InvalidArgumentException(
                'Razorpay order ID is required.'
            );
        }

        return $this->client()
            ->get(
                '/orders/' . rawurlencode($orderId)
            )
            ->throw()
            ->json();
    }

    /**
     * Fetch a Razorpay payment by provider payment ID.
     *
     * Used by the backend to verify the actual payment:
     *
     * - payment ID
     * - order ID
     * - amount
     * - currency
     * - payment status
     *
     * @throws RequestException
     */
    public function fetchPayment(
        string $paymentId
    ): array {
        $this->ensureConfigured();

        $paymentId = trim($paymentId);

        if ($paymentId === '') {
            throw new \InvalidArgumentException(
                'Razorpay payment ID is required.'
            );
        }

        return $this->client()
            ->get(
                '/payments/' . rawurlencode($paymentId)
            )
            ->throw()
            ->json();
    }

    /**
     * Verify Razorpay checkout signature.
     *
     * Razorpay generates the checkout signature from:
     *
     * order_id|payment_id
     */
    public function verifyPaymentSignature(
        string $orderId,
        string $paymentId,
        string $signature
    ): bool {
        $this->ensureConfigured();

        $orderId = trim($orderId);
        $paymentId = trim($paymentId);
        $signature = trim($signature);

        if (
            $orderId === ''
            || $paymentId === ''
            || $signature === ''
        ) {
            return false;
        }

        $keySecret = $this->settings->getKeySecret();

        if (! filled($keySecret)) {
            return false;
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $orderId . '|' . $paymentId,
            $keySecret
        );

        return hash_equals(
            $expectedSignature,
            $signature
        );
    }

    /**
     * Verify Razorpay webhook signature.
     *
     * IMPORTANT:
     * The exact raw request body must be used
     * for HMAC verification.
     */
    public function verifyWebhookSignature(
        string $payload,
        ?string $signature
    ): bool {
        $webhookSecret = $this->settings->getWebhookSecret();

        if (
            ! filled($webhookSecret)
            || ! filled($signature)
        ) {
            return false;
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $payload,
            $webhookSecret
        );

        return hash_equals(
            $expectedSignature,
            trim($signature)
        );
    }

    /**
     * Return the Razorpay API client.
     *
     * PaymentSettingsService returns the canonical host/base URL
     * without /v1. We add /v1 exactly once here.
     */
    private function client(): PendingRequest
    {
        $baseUrl = rtrim(
            $this->settings->getBaseUrl(),
            '/'
        );

        /*
         * Razorpay REST API endpoints are under /v1.
         *
         * Example:
         * https://api.razorpay.com/v1
         */
        $baseUrl .= '/v1';

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->asJson()
            ->withBasicAuth(
                $this->settings->getKeyId(),
                $this->settings->getKeySecret()
            )
            ->timeout(15)
            ->connectTimeout(10);
    }

    /**
     * Make sure Razorpay settings are configured.
     */
    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new \LogicException(
                'Razorpay is not configured. Please configure Razorpay from Admin > Settings > Preferences.'
            );
        }
    }
}