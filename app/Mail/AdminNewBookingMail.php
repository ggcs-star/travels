<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Payment $payment,
    ) {}

    public function build()
    {
        return $this
            ->subject("New Booking Received — {$this->booking->booking_number}")
            ->view('emails.admin-new-booking');
    }
}
