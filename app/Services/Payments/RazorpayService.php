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
     * Create Razorpay order.
     *
     * @throws RequestException
     */
    public function createOrder(Booking $booking): array
    {
        $this->ensureConfigured();

        $amount = (float) $booking->payableAmount();

        if ($amount <= 0) {
            throw new \InvalidArgumentException(
                'Payment amount must be greater than zero.'
            );
        }

        $response = $this->client()
            ->post('/orders', [
                'amount' => (int) round($amount * 100),
                'currency' => strtoupper($booking->currency ?: 'INR'),
                'receipt' => $booking->booking_number,
                'notes' => [
                    'booking_number' => (string) $booking->booking_number,
                    'booking_id' => (string) $booking->id,
                    'total_amount' => (string) $booking->total_amount,
                    'points_redeemed' => (string) $booking->points_redeemed,
                    'points_discount' => (string) $booking->points_discount,
                    'payable_amount' => (string) $amount,
                ],
            ])
            ->throw();

        return $response->json();
    }

    /**
     * Verify Razorpay checkout signature.
     */
    public function verifyPaymentSignature(
        string $orderId,
        string $paymentId,
        string $signature
    ): bool {
        $this->ensureConfigured();

        $expectedSignature = hash_hmac(
            'sha256',
            $orderId . '|' . $paymentId,
            $this->settings->getKeySecret()
        );

        return hash_equals(
            $expectedSignature,
            $signature
        );
    }

    /**
     * Verify Razorpay webhook signature.
     */
    public function verifyWebhookSignature(
        string $payload,
        ?string $signature
    ): bool {
        $webhookSecret = $this->settings->getWebhookSecret();

        if (! filled($webhookSecret) || ! filled($signature)) {
            return false;
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $payload,
            $webhookSecret
        );

        return hash_equals(
            $expectedSignature,
            $signature
        );
    }

    /**
     * Razorpay API client.
     */
    private function client(): PendingRequest
    {
        $baseUrl = rtrim(
            $this->settings->getBaseUrl(),
            '/'
        );

        /*
         * Razorpay REST API endpoints are under /v1.
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