<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDeparture extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tour_package_id',
        'departure_date',
        'return_date',
        'capacity',
        'price',
        'sale_price',
        'currency',
        'meeting_point',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'capacity' => 'integer',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_OPEN)
            ->whereDate('departure_date', '>=', today());
    }

    public function getAvailableSeatsAttribute(): int
    {
        $reservedSeats = $this->getAttribute('reserved_seats');

        if ($reservedSeats === null) {
            $reservedSeats = $this->bookings()->reserving()->sum('traveller_count');
        }

        return max(0, $this->capacity - (int) $reservedSeats);
    }

    public function getEffectivePriceAttribute(): string
    {
        return $this->sale_price ?: $this->price;
    }
}
