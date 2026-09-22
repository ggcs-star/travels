<?php

namespace App\Services\Payments;

use App\Models\Booking;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

use App\Services\Payments\PaymentSettingsService;

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
     * @throws RequestException
     */
    public function testConnection(): array
    {
        $this->ensureConfigured();

        $this->client()
            ->get('/orders', ['count' => 1])
            ->throw();

        return [
            'message' => 'Razorpay connection successful. Key ID, Key Secret and gateway status are valid.',
        ];
    }

    /**
     * @throws RequestException
     */
public function createOrder(Booking $booking): array
{
    $this->ensureConfigured();

    $payableAmount = $booking->payableAmount();

    return $this->client()->post('/orders', [
        'amount' => (int) round($payableAmount * 100),
        'currency' => $booking->currency,
        'receipt' => $booking->booking_number,
        'notes' => [
            'booking_number' => $booking->booking_number,
            'booking_id' => (string) $booking->id,
            'total_amount' => (string) $booking->total_amount,
            'points_redeemed' => (string) $booking->points_redeemed,
            'points_discount' => (string) $booking->points_discount,
            'payable_amount' => (string) $payableAmount,
        ],
    ])->throw()->json();
}

    public function verifyPaymentSignature(
        string $orderId,
        string $paymentId,
        string $signature
    ): bool {
        $this->ensureConfigured();

        $expectedSignature = hash_hmac(
            'sha256',
            $orderId.'|'.$paymentId,
            $this->settings->getKeySecret()
        );

        return hash_equals($expectedSignature, $signature);
    }

    public function verifyWebhookSignature(
        string $payload,
        ?string $signature
    ): bool {
        $webhookSecret = $this->settings->getWebhookSecret();

        if (! filled($webhookSecret) || ! $signature) {
            return false;
        }

        return hash_equals(
            hash_hmac('sha256', $payload, $webhookSecret),
            $signature
        );
    }

    private function client()
    {
        return Http::baseUrl(rtrim(
            $this->settings->getBaseUrl(),
            '/'
        ))->acceptJson()->withBasicAuth(
            $this->settings->getKeyId(),
            $this->settings->getKeySecret()
        );
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new \LogicException(
                'Razorpay is not configured. Please configure Razorpay from Admin > Settings > Preferences.'
            );
        }
    }
}
