<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointSetting extends Model
{
    use HasFactory;

    public const BOOKING_REWARD = 'booking_reward';

    public const REGISTRATION_REWARD = 'registration_reward';

    public const REVIEW_REWARD = 'review_reward';

    public const REFERRAL_REWARD = 'referral_reward';

    public const REWARD_FIXED = 'fixed';

    public const REWARD_AMOUNT_BASED = 'amount_based';

    public const BASIS_BOOKING_TOTAL = 'booking_total';

    public const BASIS_FINAL_PAID = 'final_paid';

    protected $fillable = [
        'key',
        'name',
        'description',
        'enabled',
        'reward_type',
        'calculation_basis',
        'points',
        'minimum_amount',
        'amount_unit',
        'redemption_enabled',
        'point_value',
        'max_redemption_percent',
        'max_points_per_booking',
        'expiry_enabled',
        'expiry_days',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'redemption_enabled' => 'boolean',
            'points' => 'integer',
            'minimum_amount' => 'integer',
            'amount_unit' => 'integer',
            'point_value' => 'decimal:4',
            'max_redemption_percent' => 'decimal:2',
            'max_points_per_booking' => 'integer',
            'expiry_enabled' => 'boolean',
            'expiry_days' => 'integer',
        ];
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}