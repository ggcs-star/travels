<?php

namespace Database\Seeders;

use App\Models\PointSetting;
use Illuminate\Database\Seeder;

class PointSettingSeeder extends Seeder
{
    public function run(): void
    {
        PointSetting::updateOrCreate(
            ['key' => PointSetting::BOOKING_REWARD],
            [
                'name' => 'Booking Reward',
                'description' => 'Points awarded to users after a successful tour booking payment.',
                'enabled' => true,
                'points' => 10,
                'minimum_amount' => 100,
                'amount_unit' => 100,
            ]
        );

        PointSetting::updateOrCreate(
            ['key' => PointSetting::REGISTRATION_REWARD],
            [
                'name' => 'Registration Reward',
                'description' => 'Points awarded when a new user successfully registers.',
                'enabled' => true,
                'points' => 100,
                'minimum_amount' => null,
                'amount_unit' => null,
            ]
        );

        PointSetting::updateOrCreate(
            ['key' => PointSetting::REVIEW_REWARD],
            [
                'name' => 'Review Reward',
                'description' => 'Points awarded for an eligible tour review.',
                'enabled' => true,
                'points' => 25,
                'minimum_amount' => null,
                'amount_unit' => null,
            ]
        );

        PointSetting::updateOrCreate(
            ['key' => PointSetting::REFERRAL_REWARD],
            [
                'name' => 'Referral Reward',
                'description' => 'Points awarded for a successful eligible referral.',
                'enabled' => true,
                'points' => 500,
                'minimum_amount' => null,
                'amount_unit' => null,
            ]
        );
    }
}