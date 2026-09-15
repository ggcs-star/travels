<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    use HasFactory;

    public const TYPE_EARNED = 'earned';

    public const TYPE_REDEEMED = 'redeemed';

    public const TYPE_EXPIRED = 'expired';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_REVERSAL = 'reversal';

    public const DIRECTION_CREDIT = 'credit';

    public const DIRECTION_DEBIT = 'debit';

    protected $fillable = [
        'user_id',
        'wallet_id',
        'type',
        'direction',
        'points',
        'balance_before',
        'balance_after',
        'source',
        'reference_type',
        'reference_id',
        'reference',
        'description',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'wallet_id' => 'integer',
            'points' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'reference_id' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(
            UserPointWallet::class,
            'wallet_id'
        );
    }

    public function referenceModel()
    {
        return $this->morphTo(
            'reference',
            'reference_type',
            'reference_id'
        );
    }
}