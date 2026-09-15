<?php

namespace App\Services\Points;

use App\Models\PointSetting;
use Illuminate\Support\Facades\DB;

class PointSettingService
{
    public function get(string $key): ?PointSetting
    {
        return PointSetting::query()
            ->where('key', $key)
            ->where('enabled', true)
            ->first();
    }

    public function getAny(string $key): ?PointSetting
    {
        return PointSetting::query()
            ->where('key', $key)
            ->first();
    }

    public function getBookingReward(): ?PointSetting
    {
        return $this->get(PointSetting::BOOKING_REWARD);
    }

    public function getRegistrationReward(): ?PointSetting
    {
        return $this->get(PointSetting::REGISTRATION_REWARD);
    }

    public function getReviewReward(): ?PointSetting
    {
        return $this->get(PointSetting::REVIEW_REWARD);
    }

    public function getReferralReward(): ?PointSetting
    {
        return $this->get(PointSetting::REFERRAL_REWARD);
    }

    /**
     * Get the booking redemption settings.
     *
     * Redemption has its own enabled flag, so we do not use
     * the general "enabled" field here.
     */
    public function getBookingRedemption(): ?PointSetting
    {
        return PointSetting::query()
            ->where('key', PointSetting::BOOKING_REWARD)
            ->first();
    }

    /**
     * Calculate booking reward according to admin settings.
     */
    public function calculateBookingPoints(float $amount): int
    {
        $setting = $this->getBookingReward();

        if (! $setting) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Fixed Reward
        |--------------------------------------------------------------------------
        */

        if ($setting->reward_type === PointSetting::REWARD_FIXED) {
            return max(0, (int) $setting->points);
        }

        /*
        |--------------------------------------------------------------------------
        | Amount Based Reward
        |--------------------------------------------------------------------------
        */

        if (
            $setting->amount_unit === null
            || $setting->amount_unit <= 0
        ) {
            return 0;
        }

        if (
            $setting->minimum_amount !== null
            && $amount < $setting->minimum_amount
        ) {
            return 0;
        }

        $points = (int) floor(
            $amount / $setting->amount_unit
        ) * (int) $setting->points;

        /*
        |--------------------------------------------------------------------------
        | Maximum Points Per Booking
        |--------------------------------------------------------------------------
        */

        if (
            $setting->max_points_per_booking !== null
            && $setting->max_points_per_booking >= 0
        ) {
            $points = min(
                $points,
                (int) $setting->max_points_per_booking
            );
        }

        return max(0, $points);
    }

    /**
     * Get the booking reward amount basis.
     */
    public function bookingCalculationBasis(): string
    {
        return $this->getBookingReward()?->calculation_basis
            ?? PointSetting::BASIS_FINAL_PAID;
    }

    /**
     * Calculate how many points can be redeemed for a booking.
     *
     * This method only calculates the redemption.
     * It does NOT deduct anything from the user's wallet.
     *
     * @return array{
     *     enabled: bool,
     *     available_points: int,
     *     points_to_redeem: int,
     *     point_value: float,
     *     discount: float,
     *     booking_amount: float,
     *     max_discount: float
     * }
     */
    public function calculateRedemption(
        int $availablePoints,
        float $bookingAmount
    ): array {
        $setting = $this->getBookingRedemption();

        $bookingAmount = max(0, round($bookingAmount, 2));
        $availablePoints = max(0, $availablePoints);

        /*
        |--------------------------------------------------------------------------
        | Redemption Disabled
        |--------------------------------------------------------------------------
        */

        if (
            ! $setting
            || ! $setting->redemption_enabled
        ) {
            return [
                'enabled' => false,
                'available_points' => $availablePoints,
                'points_to_redeem' => 0,
                'point_value' => 0,
                'discount' => 0,
                'booking_amount' => $bookingAmount,
                'max_discount' => 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Point Value
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 1 point = ₹1
        | point_value = 1.00
        |
        */

        $pointValue = max(
            0,
            (float) ($setting->point_value ?? 0)
        );

        if (
            $pointValue <= 0
            || $bookingAmount <= 0
            || $availablePoints <= 0
        ) {
            return [
                'enabled' => true,
                'available_points' => $availablePoints,
                'points_to_redeem' => 0,
                'point_value' => $pointValue,
                'discount' => 0,
                'booking_amount' => $bookingAmount,
                'max_discount' => 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Discount By Percentage
        |--------------------------------------------------------------------------
        */

        $maxRedemptionPercent = $setting->max_redemption_percent;

        if ($maxRedemptionPercent === null) {
            $maxDiscount = $bookingAmount;
        } else {
            $maxRedemptionPercent = max(
                0,
                min(100, (float) $maxRedemptionPercent)
            );

            $maxDiscount = round(
                $bookingAmount * ($maxRedemptionPercent / 100),
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Points Per Booking
        |--------------------------------------------------------------------------
        */

        $maximumPoints = $availablePoints;

        if (
            $setting->max_points_per_booking !== null
            && $setting->max_points_per_booking >= 0
        ) {
            $maximumPoints = min(
                $maximumPoints,
                (int) $setting->max_points_per_booking
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Convert Maximum Discount Into Points
        |--------------------------------------------------------------------------
        */

        $pointsAllowedByAmount = (int) floor(
            $maxDiscount / $pointValue
        );

        $pointsToRedeem = min(
            $maximumPoints,
            $pointsAllowedByAmount
        );

        /*
        |--------------------------------------------------------------------------
        | Final Discount
        |--------------------------------------------------------------------------
        */

        $discount = round(
            $pointsToRedeem * $pointValue,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Never Allow Discount Above Booking Amount
        |--------------------------------------------------------------------------
        */

        $discount = min(
            $discount,
            $bookingAmount
        );

        /*
        |--------------------------------------------------------------------------
        | Safety Recalculation
        |--------------------------------------------------------------------------
        |
        | This protects against decimal configuration values.
        |
        */

        $pointsToRedeem = (int) floor(
            $discount / $pointValue
        );

        $discount = round(
            $pointsToRedeem * $pointValue,
            2
        );

        return [
            'enabled' => true,
            'available_points' => $availablePoints,
            'points_to_redeem' => $pointsToRedeem,
            'point_value' => $pointValue,
            'discount' => $discount,
            'booking_amount' => $bookingAmount,
            'max_discount' => $maxDiscount,
        ];
    }

    /**
     * Calculate the final payable amount after points redemption.
     */
    public function calculatePayableAmount(
        float $bookingAmount,
        int $pointsToRedeem
    ): array {
        $bookingAmount = max(
            0,
            round($bookingAmount, 2)
        );

        $pointsToRedeem = max(
            0,
            $pointsToRedeem
        );

        $setting = $this->getBookingRedemption();

        if (
            ! $setting
            || ! $setting->redemption_enabled
            || $pointsToRedeem <= 0
        ) {
            return [
                'points_redeemed' => 0,
                'points_discount' => 0,
                'payable_amount' => $bookingAmount,
            ];
        }

        $pointValue = max(
            0,
            (float) ($setting->point_value ?? 0)
        );

        if ($pointValue <= 0) {
            return [
                'points_redeemed' => 0,
                'points_discount' => 0,
                'payable_amount' => $bookingAmount,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Re-validate Maximum Points
        |--------------------------------------------------------------------------
        */

        if (
            $setting->max_points_per_booking !== null
            && $setting->max_points_per_booking >= 0
        ) {
            $pointsToRedeem = min(
                $pointsToRedeem,
                (int) $setting->max_points_per_booking
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Re-validate Percentage Limit
        |--------------------------------------------------------------------------
        */

        if ($setting->max_redemption_percent !== null) {
            $percent = max(
                0,
                min(100, (float) $setting->max_redemption_percent)
            );

            $maxDiscount = round(
                $bookingAmount * ($percent / 100),
                2
            );

            $pointsAllowedByPercent = (int) floor(
                $maxDiscount / $pointValue
            );

            $pointsToRedeem = min(
                $pointsToRedeem,
                $pointsAllowedByPercent
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Discount
        |--------------------------------------------------------------------------
        */

        $discount = round(
            $pointsToRedeem * $pointValue,
            2
        );

        $discount = min(
            $discount,
            $bookingAmount
        );

        /*
        |--------------------------------------------------------------------------
        | Calculate Final Payable
        |--------------------------------------------------------------------------
        */

        $payableAmount = round(
            $bookingAmount - $discount,
            2
        );

        return [
            'points_redeemed' => $pointsToRedeem,
            'points_discount' => $discount,
            'payable_amount' => max(0, $payableAmount),
        ];
    }

    /**
     * Update a point setting from Admin.
     */
    public function update(
        string $key,
        array $data
    ): PointSetting {
        return DB::transaction(function () use ($key, $data) {
            return PointSetting::query()
                ->updateOrCreate(
                    ['key' => $key],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'] ?? null,

                        'enabled' =>
                            (bool) ($data['enabled'] ?? false),

                        'reward_type' =>
                            $data['reward_type']
                            ?? PointSetting::REWARD_FIXED,

                        'calculation_basis' =>
                            $data['calculation_basis']
                            ?? null,

                        'points' =>
                            (int) ($data['points'] ?? 0),

                        'minimum_amount' =>
                            isset($data['minimum_amount'])
                            ? (int) $data['minimum_amount']
                            : null,

                        'amount_unit' =>
                            isset($data['amount_unit'])
                            ? (int) $data['amount_unit']
                            : null,

                        'redemption_enabled' =>
                            (bool) ($data['redemption_enabled'] ?? false),

                        'point_value' =>
                            isset($data['point_value'])
                            ? (float) $data['point_value']
                            : null,

                        'max_redemption_percent' =>
                            isset($data['max_redemption_percent'])
                            ? (float) $data['max_redemption_percent']
                            : null,

                        'max_points_per_booking' =>
                            isset($data['max_points_per_booking'])
                            ? (int) $data['max_points_per_booking']
                            : null,

                        'expiry_enabled' =>
                            (bool) ($data['expiry_enabled'] ?? false),

                        'expiry_days' =>
                            isset($data['expiry_days'])
                            ? (int) $data['expiry_days']
                            : null,
                    ]
                );
        });
    }
}