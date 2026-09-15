<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Models\User;
use App\Services\Points\PointWalletService;
use App\Services\Points\PointSettingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly PointWalletService $pointWalletService,
        private readonly PointSettingService $pointSettingService
    ) {
    }

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

                /*
                |--------------------------------------------------------------------------
                | Points Redemption Defaults
                |--------------------------------------------------------------------------
                */

                'points_redeemed' => 0,

                'points_discount' => 0,

                'payable_amount' =>
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
                |--------------------------------------------------------------------------
                | Remove UploadedFile From Raw Data
                |--------------------------------------------------------------------------
                */

                unset(
                    $travellerData['id_proof_document']
                );

                /*
                |--------------------------------------------------------------------------
                | Store ID Proof Privately
                |--------------------------------------------------------------------------
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
                |--------------------------------------------------------------------------
                | Create Traveller
                |--------------------------------------------------------------------------
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
     * Apply travel points to a pending booking.
     *
     * Points are NOT deducted from the wallet here.
     * They are deducted only after successful payment.
     */
    public function applyPoints(
        Booking $booking,
        User $user,
        int $points
    ): Booking {
        return DB::transaction(function () use (
            $booking,
            $user,
            $points
        ) {
            /*
            |--------------------------------------------------------------------------
            | Lock Booking
            |--------------------------------------------------------------------------
            */

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            /*
            |--------------------------------------------------------------------------
            | Verify Ownership
            |--------------------------------------------------------------------------
            */

            if ((int) $booking->user_id !== (int) $user->id) {
                throw ValidationException::withMessages([
                    'points' =>
                        'You cannot use points on this booking.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Booking Is Payable
            |--------------------------------------------------------------------------
            */

            if (! $booking->isPayable()) {
                throw ValidationException::withMessages([
                    'points' =>
                        'This booking is no longer available for payment.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Requested Points
            |--------------------------------------------------------------------------
            */

            if ($points <= 0) {
                throw ValidationException::withMessages([
                    'points' =>
                        'Please enter a valid number of points.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Lock Wallet
            |--------------------------------------------------------------------------
            */

            $wallet = $user->pointWallet()
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                throw ValidationException::withMessages([
                    'points' =>
                        'Points wallet is not available.',
                ]);
            }

            $availablePoints = (int) $wallet->balance;

            if ($availablePoints <= 0) {
                throw ValidationException::withMessages([
                    'points' =>
                        'You do not have any available points.',
                ]);
            }

            if ($points > $availablePoints) {
                throw ValidationException::withMessages([
                    'points' =>
                        "You only have {$availablePoints} point(s) available.",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Maximum Allowed Redemption
            |--------------------------------------------------------------------------
            */

            $calculation = $this->pointSettingService
                ->calculateRedemption(
                    availablePoints: $availablePoints,
                    bookingAmount: (float) $booking->total_amount
                );

            if (! $calculation['enabled']) {
                throw ValidationException::withMessages([
                    'points' =>
                        'Points redemption is currently unavailable.',
                ]);
            }

            $maximumAllowed =
                (int) $calculation['points_to_redeem'];

            if ($maximumAllowed <= 0) {
                throw ValidationException::withMessages([
                    'points' =>
                        'Points cannot be redeemed for this booking.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Browser From Bypassing Limits
            |--------------------------------------------------------------------------
            */

            if ($points > $maximumAllowed) {
                throw ValidationException::withMessages([
                    'points' =>
                        "You can redeem a maximum of {$maximumAllowed} point(s) for this booking.",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Final Payable Amount
            |--------------------------------------------------------------------------
            */

            $payable = $this->pointSettingService
                ->calculatePayableAmount(
                    bookingAmount: (float) $booking->total_amount,
                    pointsToRedeem: $points
                );

            if ($payable['points_redeemed'] <= 0) {
                throw ValidationException::withMessages([
                    'points' =>
                        'The selected points cannot be applied.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Save Redemption On Booking
            |--------------------------------------------------------------------------
            */

            $booking->update([
                'points_redeemed' =>
                    $payable['points_redeemed'],

                'points_discount' =>
                    $payable['points_discount'],

                'payable_amount' =>
                    $payable['payable_amount'],
            ]);

            return $booking->fresh([
                'tourPackage',
                'departure',
                'travellers',
                'payments',
            ]);
        });
    }

    /**
     * Remove applied travel points from a pending booking.
     *
     * The wallet is untouched because points have not yet
     * been deducted.
     */
    public function removePoints(
        Booking $booking,
        User $user
    ): Booking {
        return DB::transaction(function () use (
            $booking,
            $user
        ) {
            /*
            |--------------------------------------------------------------------------
            | Lock Booking
            |--------------------------------------------------------------------------
            */

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            /*
            |--------------------------------------------------------------------------
            | Verify Ownership
            |--------------------------------------------------------------------------
            */

            if ((int) $booking->user_id !== (int) $user->id) {
                throw ValidationException::withMessages([
                    'points' =>
                        'You cannot modify points on this booking.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Booking Is Payable
            |--------------------------------------------------------------------------
            */

            if (! $booking->isPayable()) {
                throw ValidationException::withMessages([
                    'points' =>
                        'This booking can no longer be modified.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Reset Redemption
            |--------------------------------------------------------------------------
            */

            $booking->update([
                'points_redeemed' => 0,
                'points_discount' => 0,
                'payable_amount' => $booking->total_amount,
            ]);

            return $booking->fresh([
                'tourPackage',
                'departure',
                'travellers',
                'payments',
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
            | Finalize Redeemed Points
            |--------------------------------------------------------------------------
            |
            | Points are deducted ONLY after successful payment.
            |
            */

            $this->redeemBookingPoints(
                booking: $booking,
                payment: $payment
            );

            /*
            |--------------------------------------------------------------------------
            | Award Booking Reward
            |--------------------------------------------------------------------------
            */

            $this->awardBookingPoints(
                booking: $booking,
                payment: $payment
            );

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
     * Deduct points used by a successfully paid booking.
     *
     * The unique booking reference prevents duplicate deductions
     * if payment verification and webhook processing both happen.
     */
    private function redeemBookingPoints(
        Booking $booking,
        Payment $payment
    ): void {
        if (! $booking->user_id) {
            return;
        }

        $points = (int) $booking->points_redeemed;

        if ($points <= 0) {
            return;
        }

        $reference = 'BOOKING_REDEMPTION:' . $booking->id;

        /*
        |--------------------------------------------------------------------------
        | Check Existing Redemption
        |--------------------------------------------------------------------------
        */

        $alreadyRedeemed = \App\Models\PointTransaction::query()
            ->where('reference', $reference)
            ->exists();

        if ($alreadyRedeemed) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Debit Wallet
        |--------------------------------------------------------------------------
        */

        $this->pointWalletService->debit(
            user: $booking->user,
            points: $points,
            source: 'booking_redemption',
            description: "Points redeemed for booking {$booking->booking_number}.",
            referenceModel: $booking,
            reference: $reference,
            metadata: [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'provider_order_id' => $payment->provider_order_id,
                'provider_payment_id' => $payment->provider_payment_id,
                'booking_amount' => (string) $booking->total_amount,
                'points_redeemed' => $points,
                'points_discount' => (string) $booking->points_discount,
                'payable_amount' => (string) $booking->payable_amount,
                'currency' => $booking->currency,
            ],
        );
    }

    /**
     * Award points for a successfully paid booking.
     *
     * The reward is calculated from the active admin point setting.
     * The booking reference makes the reward idempotent.
     */
    private function awardBookingPoints(
        Booking $booking,
        Payment $payment
    ): void {
        if (! $booking->user_id) {
            return;
        }

        $points = $this->pointSettingService->calculateBookingPoints(
            (float) $payment->amount
        );

        if ($points <= 0) {
            return;
        }

        $reference = 'BOOKING_REWARD:' . $booking->id;

        $this->pointWalletService->creditOnce(
            user: $booking->user,
            points: $points,
            source: 'booking_payment',
            reference: $reference,
            description: "Points earned for booking {$booking->booking_number}.",
            referenceModel: $booking,
            metadata: [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'provider_order_id' => $payment->provider_order_id,
                'provider_payment_id' => $payment->provider_payment_id,
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'reward_points' => $points,
            ],
        );
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