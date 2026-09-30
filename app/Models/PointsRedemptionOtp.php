<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsRedemptionOtp extends Model
{
    use HasFactory;

    protected $table = 'points_redemption_otps';

    protected $fillable = [
        'user_id',
        'admin_id',
        'points',
        'otp_hash',
        'attempts',
        'expires_at',
        'verified_at',
        'verification_token',
        'redeemed_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
