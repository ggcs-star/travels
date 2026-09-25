<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'booking_number',
        'user_id',
        'tour_package_id',
        'tour_departure_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'country',
        'special_requests',
        'traveller_count',
        'subtotal',
        'tax_amount',
        'total_amount',

        // Points redemption
        'points_redeemed',
        'points_discount',
        'payable_amount',

        'currency',
        'status',
        'payment_status',
        'expires_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',

            // Points redemption
            'points_redeemed' => 'integer',
            'points_discount' => 'decimal:2',
            'payable_amount' => 'decimal:2',

            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function departure()
    {
        return $this->belongsTo(
            TourDeparture::class,
            'tour_departure_id'
        );
    }

    public function travellers()
    {
        return $this->hasMany(BookingTraveller::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeReserving(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where(
                'status',
                self::STATUS_CONFIRMED
            )->orWhere(function (Builder $query) {
                $query->where(
                    'status',
                    self::STATUS_PENDING_PAYMENT
                )->where(
                    'expires_at',
                    '>',
                    now()
                );
            });
        });
    }

    public function isPayable(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT
            && $this->payment_status === self::PAYMENT_UNPAID
            && $this->expires_at?->isFuture();
    }

    /**
     * Get the amount that should actually be paid.
     *
     * Falls back to total_amount for existing bookings
     * created before points redemption was introduced.
     */
    public function payableAmount(): float
    {
        return (float) (
            $this->payable_amount
            ?? $this->total_amount
        );
    }
}