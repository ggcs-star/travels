<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'created';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_CASH = 'cash';

    public const METHOD_UPI = 'upi';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const METHOD_RAZORPAY = 'razorpay';

    public const OFFLINE_METHODS = [
        self::METHOD_CASH,
        self::METHOD_UPI,
        self::METHOD_BANK_TRANSFER,
        self::METHOD_RAZORPAY,
    ];

    protected $hidden = [
        'signature',
    ];

    protected $fillable = [
        'booking_id',
        'provider',
        'provider_order_id',
        'provider_payment_id',
        'amount',
        'currency',
        'status',
        'signature',
        'metadata',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
