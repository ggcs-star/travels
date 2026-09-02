<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Create a new booking and temporarily reserve seats.
     */
    public function create(
        TourPackage $tour,
        User $user,
        array $data
    ): Booking {
        return DB::transaction(function () use ($tour, $user, $data) {

            /*
            |--------------------------------------------------------------------------
            | Lock Departure
            |--------------------------------------------------------------------------
            |
            | Prevent two simultaneous bookings from consuming the same seats.
            |
            */

            $departure = TourDeparture::query()
                ->lockForUpdate()
                ->findOrFail($data['departure_id']);

            /*
            |--------------------------------------------------------------------------
            | Verify Departure Belongs To Tour
            |--------------------------------------------------------------------------
            */

            if ((int) $departure->tour_package_id !== (int) $tour->id) {
                throw ValidationException::withMessages([
                    'departure_id' =>
                        'Please select a valid departure for this tour.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Departure Is Bookable
            |--------------------------------------------------------------------------
            */

            if (
                $departure->status !== TourDeparture::STATUS_OPEN
                || $departure->departure_date->isPast()
            ) {
                throw ValidationException::withMessages([
                    'departure_id' =>
                        'This departure is no longer available.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Traveller Count
            |--------------------------------------------------------------------------
            |
            | NEVER trust a traveller_count value coming from the browser.
            | The actual count is calculated from the submitted traveller array.
            |
            */

            $travellers = $data['travellers'] ?? [];

            $travellerCount = count($travellers);

            if ($travellerCount < 1) {
                throw ValidationException::withMessages([
                    'travellers' =>
                        'At least one traveller is required.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Current Reserved Seats
            |--------------------------------------------------------------------------
            |
            | Confirmed bookings + active payment holds.
            |
            */

            $reservedSeats = (int) Booking::query()
                ->reserving()
                ->where(
                    'tour_departure_id',
                    $departure->id
                )
                ->sum('traveller_count');

            $availableSeats = max(
                0,
                (int) $departure->capacity - $reservedSeats
            );

            /*
            |--------------------------------------------------------------------------
            | Capacity Protection
            |--------------------------------------------------------------------------
            */

            if ($travellerCount > $availableSeats) {
                throw ValidationException::withMessages([
                    'travellers' =>
                        "Only {$availableSeats} seat(s) are available for this departure.",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Price
            |--------------------------------------------------------------------------
            */

            $priceInPaise = $this->priceInPaise(
                $departure->effective_price
            );

            $subtotalInPaise = $priceInPaise * $travellerCount;

            $taxPercent = (float) config(
                'travels.booking.tax_percent',
                0
            );

            $taxInPaise = (int) round(
                $subtotalInPaise * ($taxPercent / 100)
            );

            $totalInPaise =
                $subtotalInPaise + $taxInPaise;

            /*
            |--------------------------------------------------------------------------
            | Create Booking
            |--------------------------------------------------------------------------
            */

            $booking = Booking::create([
                'booking_number' =>
                    $this->generateBookingNumber(),

                'user_id' =>
                    $user->id,

                'tour_package_id' =>
                    $tour->id,

                'tour_departure_id' =>
                    $departure->id,

                'contact_name' =>
                    $data['contact_name'],

                'contact_email' =>
                    $data['contact_email'],

                'contact_phone' =>
                    $data['contact_phone'],

                'country' =>
                    $data['country'] ?? null,

                'special_requests' =>
                    $data['special_requests'] ?? null,

                'traveller_count' =>
                    $travellerCount,

                'subtotal' =>
                    $this->moneyFromPaise(
                        $subtotalInPaise
                    ),

                'tax_amount' =>
                    $this->moneyFromPaise(
                        $taxInPaise
                    ),

                'total_amount' =>
                    $this->moneyFromPaise(
                        $totalInPaise
                    ),

                'currency' =>
                    $departure->currency,

                'status' =>
                    Booking::STATUS_PENDING_PAYMENT,

                'payment_status' =>
                    Booking::PAYMENT_UNPAID,

                'expires_at' =>
                    now()->addMinutes(
                        (int) config(
                            'travels.booking.payment_hold_minutes',
                            15
                        )
                    ),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Store Individual Traveller Details
            |--------------------------------------------------------------------------
            |
            | Every traveller gets their own database row.
            | Every traveller's ID proof document is stored separately.
            |
            */

            foreach ($travellers as $travellerData) {

                $document = $travellerData['id_proof_document']
                    ?? null;

                /*
                |--------------------------------------------------------------
                | Remove UploadedFile From Raw Data
                |--------------------------------------------------------------
                */

                unset(
                    $travellerData['id_proof_document']
                );

                /*
                |--------------------------------------------------------------
                | Store ID Proof Privately
                |--------------------------------------------------------------
                */

                if ($document instanceof UploadedFile) {
                    $travellerData['id_proof_document'] =
                        $document->store(
                            'booking-documents/' . $booking->id,
                            'private'
                        );
                } else {
                    throw ValidationException::withMessages([
                        'travellers' =>
                            'Every traveller must have a valid ID proof document.',
                    ]);
                }

                /*
                |--------------------------------------------------------------
                | Create Traveller
                |--------------------------------------------------------------
                */

                $booking->travellers()->create(
                    $travellerData
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Return Complete Booking
            |--------------------------------------------------------------------------
            */

            return $booking->load([
                'tourPackage',
                'departure',
                'travellers',
            ]);
        });
    }

    /**
     * Expire a payment hold when its time has passed.
     */
    public function expireIfPastDue(
        Booking $booking
    ): Booking {

        if (
            $booking->status === Booking::STATUS_PENDING_PAYMENT
            && $booking->expires_at?->isPast()
        ) {
            $booking->update([
                'status' =>
                    Booking::STATUS_EXPIRED,
            ]);
        }

        return $booking->fresh();
    }

    /**
     * Confirm a successful Razorpay payment.
     */
    public function confirmPayment(
        Payment $payment,
        string $providerPaymentId,
        string $signature,
        array $metadata = []
    ): Booking {

        return DB::transaction(function () use (
            $payment,
            $providerPaymentId,
            $signature,
            $metadata
        ) {

            /*
            |--------------------------------------------------------------------------
            | Lock Payment
            |--------------------------------------------------------------------------
            */

            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            /*
            |--------------------------------------------------------------------------
            | Lock Booking
            |--------------------------------------------------------------------------
            */

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail(
                    $payment->booking_id
                );

            /*
            |--------------------------------------------------------------------------
            | Already Paid
            |--------------------------------------------------------------------------
            */

            if (
                $payment->status === Payment::STATUS_PAID
            ) {
                return $booking->fresh([
                    'tourPackage',
                    'departure',
                    'travellers',
                    'payments',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Booking Is Still Payable
            |--------------------------------------------------------------------------
            */

            if (! $booking->isPayable()) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'This booking can no longer be paid for.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Mark Payment Paid
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'provider_payment_id' =>
                    $providerPaymentId,

                'signature' =>
                    $signature,

                'status' =>
                    Payment::STATUS_PAID,

                'metadata' =>
                    $metadata,

                'paid_at' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Confirm Booking
            |--------------------------------------------------------------------------
            */

            $booking->update([
                'status' =>
                    Booking::STATUS_CONFIRMED,

                'payment_status' =>
                    Booking::PAYMENT_PAID,

                'paid_at' =>
                    now(),

                'expires_at' =>
                    null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Return Complete Booking
            |--------------------------------------------------------------------------
            */

            return $booking->fresh([
                'tourPackage',
                'departure',
                'travellers',
                'payments',
            ]);
        });
    }

    /**
     * Generate a unique booking number.
     */
    private function generateBookingNumber(): string
    {
        do {
            $bookingNumber =
                'TRV-' .
                now()->format('ymd') .
                '-' .
                Str::upper(
                    Str::random(6)
                );

        } while (
            Booking::query()
                ->where(
                    'booking_number',
                    $bookingNumber
                )
                ->exists()
        );

        return $bookingNumber;
    }

    /**
     * Convert decimal currency amount to paise.
     */
    private function priceInPaise(
        string|float|int $amount
    ): int {
        return (int) round(
            ((float) $amount) * 100
        );
    }

    /**
     * Convert paise back to decimal currency.
     */
    private function moneyFromPaise(
        int $amount
    ): string {
        return number_format(
            $amount / 100,
            2,
            '.',
            ''
        );
    }
}