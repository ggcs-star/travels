<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
        'booked_by_admin',
        'tour_package_id',
        'tour_departure_id',
        'trip_snapshot',
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
            'booked_by_admin' => 'boolean',

            'trip_snapshot' => 'array',

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

    /**
     * Build an immutable snapshot of the trip details as they
     * exist right now, to be frozen onto the booking.
     *
     * The customer booked this exact name/duration/dates/price.
     * If the admin edits the tour package or departure later,
     * this booking must keep showing what was actually booked.
     */
    public static function snapshotTrip(
        TourPackage $tour,
        TourDeparture $departure
    ): array {
        return [
            'tour_name' => $tour->name,
            'duration_days' => $tour->duration_days,
            'duration_nights' => $tour->duration_nights,
            'destination' => $tour->destination,
            'starting_city' => $tour->starting_city,
            'ending_city' => $tour->ending_city,
            'cover_image_url' => $tour->cover_image_url,
            'departure_date' => optional($departure->departure_date)->toDateString(),
            'return_date' => optional($departure->return_date)->toDateString(),
            'meeting_point' => $departure->meeting_point,
        ];
    }

    public function tripName(): ?string
    {
        return $this->trip_snapshot['tour_name']
            ?? $this->tourPackage?->name;
    }

    public function tripDurationDays(): ?int
    {
        return $this->trip_snapshot['duration_days']
            ?? $this->tourPackage?->duration_days;
    }

    public function tripDurationNights(): ?int
    {
        return $this->trip_snapshot['duration_nights']
            ?? $this->tourPackage?->duration_nights;
    }

    public function tripDestination(): ?string
    {
        return $this->trip_snapshot['destination']
            ?? $this->tourPackage?->destination;
    }

    public function tripStartingCity(): ?string
    {
        return $this->trip_snapshot['starting_city']
            ?? $this->tourPackage?->starting_city;
    }

    public function tripEndingCity(): ?string
    {
        return $this->trip_snapshot['ending_city']
            ?? $this->tourPackage?->ending_city;
    }

    public function tripCoverImageUrl(): ?string
    {
        return $this->trip_snapshot['cover_image_url']
            ?? $this->tourPackage?->cover_image_url;
    }

    public function tripMeetingPoint(): ?string
    {
        return $this->trip_snapshot['meeting_point']
            ?? $this->departure?->meeting_point;
    }

    public function tripDepartureDate(): ?Carbon
    {
        $date = $this->trip_snapshot['departure_date'] ?? null;

        return $date
            ? Carbon::parse($date)
            : $this->departure?->departure_date;
    }

    public function tripReturnDate(): ?Carbon
    {
        $date = $this->trip_snapshot['return_date'] ?? null;

        return $date
            ? Carbon::parse($date)
            : $this->departure?->return_date;
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