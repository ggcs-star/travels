<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PointsRefundedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public int $pointsRefunded,
        public int $newBalance,
    ) {}

    public function build()
    {
        return $this
            ->subject("{$this->pointsRefunded} Points Refunded to Your Wallet")
            ->view('emails.points-refunded');
    }
}
