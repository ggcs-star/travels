<?php

namespace App\Services\Points;

use App\Mail\PointsRedemptionOtpMail;
use App\Models\PointsRedemptionOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PointsRedemptionOtpService
{
    private const OTP_EXPIRES_MINUTES = 10;

    private const MAX_ATTEMPTS = 5;

    private const TOKEN_VALID_MINUTES = 30;

    public function __construct(
        private readonly PointSettingService $pointSettingService
    ) {}

    /**
     * Email a fresh OTP to the customer, confirming they agree to
     * have the given number of points redeemed on their behalf.
     *
     * @param  array{tour_name: ?string, departure_date: mixed, return_date: mixed, traveller_count: int, subtotal: float, tax: float, total: float, currency: string}|null  $bookingContext
     */
    public function send(
        User $customer,
        int $points,
        User $admin,
        ?array $bookingContext = null
    ): PointsRedemptionOtp {

        if ($points < 1) {
            throw ValidationException::withMessages([
                'points' => 'Please enter a valid number of points.',
            ]);
        }

        if (! $customer->email) {
            throw ValidationException::withMessages([
                'points' => 'This customer has no registered email to send an OTP to.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);

        $record = PointsRedemptionOtp::create([
            'user_id' => $customer->id,
            'admin_id' => $admin->id,
            'points' => $points,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRES_MINUTES),
        ]);

        Mail::to($customer->email)->send(
            new PointsRedemptionOtpMail(
                otp: $otp,
                points: $points,
                expiresInMinutes: self::OTP_EXPIRES_MINUTES,
                customerName: $customer->name,
                summary: $this->buildSummary($bookingContext, $points),
            )
        );

        return $record;
    }

    /**
     * Combine the trip/amount context with the points discount math
     * (reusing the same PointSettingService calculation the booking
     * itself uses) into the flat array the email view expects.
     */
    private function buildSummary(?array $bookingContext, int $points): ?array
    {
        if (! $bookingContext) {
            return null;
        }

        $payable = $this->pointSettingService->calculatePayableAmount(
            bookingAmount: $bookingContext['total'],
            pointsToRedeem: $points
        );

        return array_merge($bookingContext, [
            'points_redeemed' => $payable['points_redeemed'],
            'points_discount' => $payable['points_discount'],
            'payable_amount' => $payable['payable_amount'],
        ]);
    }

    /**
     * Verify a submitted OTP and, on success, issue a one-time
     * verification token the caller can redeem shortly after.
     */
    public function verify(
        int $otpId,
        string $otp,
        User $admin
    ): PointsRedemptionOtp {

        $record = PointsRedemptionOtp::query()
            ->where('id', $otpId)
            ->where('admin_id', $admin->id)
            ->first();

        if (! $record || $record->redeemed_at) {
            throw ValidationException::withMessages([
                'otp' => 'This OTP request is no longer valid. Please request a new one.',
            ]);
        }

        if ($record->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => 'This OTP has expired. Please request a new one.',
            ]);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Please request a new OTP.',
            ]);
        }

        $record->increment('attempts');

        if (! Hash::check($otp, $record->otp_hash)) {
            $remaining = max(0, self::MAX_ATTEMPTS - $record->attempts);

            throw ValidationException::withMessages([
                'otp' => $remaining > 0
                    ? "Incorrect OTP. {$remaining} attempt(s) remaining."
                    : 'Incorrect OTP. Please request a new one.',
            ]);
        }

        $record->update([
            'verified_at' => now(),
            'verification_token' => Str::random(48),
        ]);

        return $record->fresh();
    }

    /**
     * Consume a previously verified token, confirming it matches
     * exactly the customer and points amount about to be redeemed.
     *
     * This is the real security boundary — the frontend also locks
     * the points field around verification, but only this check
     * actually stops a mismatched or reused token from going through.
     */
    public function consume(
        User $customer,
        int $points,
        ?string $token
    ): void {

        $genericError = ValidationException::withMessages([
            'points' => 'Points redemption could not be verified. Please verify the OTP again.',
        ]);

        if (! $token) {
            throw $genericError;
        }

        $record = PointsRedemptionOtp::query()
            ->where('verification_token', $token)
            ->first();

        if (
            ! $record
            || ! $record->verified_at
            || $record->redeemed_at
            || (int) $record->user_id !== (int) $customer->id
            || (int) $record->points !== $points
            || $record->verified_at->diffInMinutes(now()) > self::TOKEN_VALID_MINUTES
        ) {
            throw $genericError;
        }

        $record->update([
            'redeemed_at' => now(),
        ]);
    }
}
