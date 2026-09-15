<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPointWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_redeemed',
        'total_expired',
        'total_adjusted',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'balance' => 'integer',
            'total_earned' => 'integer',
            'total_redeemed' => 'integer',
            'total_expired' => 'integer',
            'total_adjusted' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(
            PointTransaction::class,
            'wallet_id'
        );
    }
}