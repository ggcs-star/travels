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
