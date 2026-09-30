<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public int $expiresInMinutes,
        public ?string $userName = null,
    ) {}

    public function build()
    {
        return $this
            ->subject('Your password reset OTP')
            ->view('auth.emails.password-reset-otp');
    }
}