<?php

namespace App\Services\Payments;

use App\Models\Booking;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RazorpayService
{
    public function isConfigured(): bool
    {
        return filled(config('services.razorpay.key_id'))
            && filled(config('services.razorpay.key_secret'));
    }

    /**
     * @throws RequestException
     */
    public function createOrder(Booking $booking): array
    {
        $this->ensureConfigured();

        return $this->client()->post('/orders', [
            'amount' => (int) round(((float) $booking->total_amount) * 100),
            'currency' => $booking->currency,
            'receipt' => $booking->booking_number,
            'notes' => [
                'booking_number' => $booking->booking_number,
                'booking_id' => (string) $booking->id,
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
            config('services.razorpay.key_secret')
        );

        return hash_equals($expectedSignature, $signature);
    }

    public function verifyWebhookSignature(
        string $payload,
        ?string $signature
    ): bool {
        $webhookSecret = config('services.razorpay.webhook_secret');

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
            config('services.razorpay.base_url'),
            '/'
        ))->acceptJson()->withBasicAuth(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new \LogicException(
                'Razorpay is not configured. Add RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.'
            );
        }
    }
}
