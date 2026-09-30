<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PointsRedemptionOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public int $points,
        public int $expiresInMinutes,
        public ?string $customerName = null,
        public ?array $summary = null,
    ) {}

    public function build()
    {
        return $this
            ->subject("Confirm Your {$this->points} Points Redemption for Your Travel Booking")
            ->view('emails.points-redemption-otp');
    }
}
