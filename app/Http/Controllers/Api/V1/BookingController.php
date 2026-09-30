<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Services\Bookings\BookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookingController extends ApiController
{
    public function __construct(
        protected BookingService $bookingService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Booking List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return $this->error(
                    'Unauthenticated.',
                    401
                );
            }

            $perPage = min(
                50,
                max(
                    1,
                    $request->integer('per_page', 15)
                )
            );

            $bookings = Booking::query()
                ->where('user_id', $user->id)
                ->with([
                    'tourPackage',
                    'departure',
                    'travellers',
                    'payments' => function ($query) {
                        $query->latest('id');
                    },
                ])
                ->latest('id')
                ->paginate($perPage);

            $bookings->through(
                function (Booking $booking) {
                    return $this->bookingPayload($booking);
                }
            );

            return $this->success(
                $bookings,
                'Bookings retrieved successfully.'
            );

        } catch (Throwable $e) {

            Log::error(
                'API Booking List Error',
                [
                    'user_id' => optional($request->user())->id,
                    'per_page' => $request->integer('per_page', 15),
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return $this->error(
                'Unable to retrieve bookings at the moment.',
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Booking Details
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Booking $booking
    ) {
        try {
            $user = $request->user();

            if (! $user) {
                return $this->error(
                    'Unauthenticated.',
                    401
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            |
            | A user can only access their own booking.
            |
            */

            if (
                (int) $booking->user_id !==
                (int) $user->id
            ) {
                return $this->error(
                    'Booking not found.',
                    404
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Load Booking Relationships
            |--------------------------------------------------------------------------
            */

            $booking->load([
                'tourPackage',
                'departure',
                'travellers',
                'payments' => function ($query) {
                    $query->latest('id');
                },
            ]);

            return $this->success(
                $this->bookingPayload(
                    $booking,
                    true
                ),
                'Booking details retrieved successfully.'
            );

        } catch (Throwable $e) {

            Log::error(
                'API Booking Details Error',
                [
                    'user_id' => optional($request->user())->id,
                    'booking_id' => $booking->id ?? null,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return $this->error(
                'Unable to retrieve booking details at the moment.',
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Booking
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreBookingRequest $request,
        TourPackage $tour
    ) {
        try {
            $user = $request->user();

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            if (! $user) {
                return $this->error(
                    'Unauthenticated.',
                    401
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Check Tour Availability
            |--------------------------------------------------------------------------
            */

            if (! $tour->isAvailable()) {
                return $this->error(
                    'This tour is no longer available for booking.',
                    422
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Booking
            |--------------------------------------------------------------------------
            */

            $booking = $this->bookingService->create(
                $tour,
                $user,
                $request->validated()
            );

            /*
            |--------------------------------------------------------------------------
            | Load Booking Relationships
            |--------------------------------------------------------------------------
            */

            $booking->load([
                'tourPackage',
                'departure',
                'travellers',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            return $this->success(
                $this->bookingPayload(
                    $booking,
                    true
                ),
                'Booking created successfully.',
                201
            );

        } catch (Throwable $e) {

            Log::error(
                'API Booking Creation Error',
                [
                    'user_id' => optional($request->user())->id,
                    'tour_id' => $tour->id ?? null,
                    'request' => $request->except([
                        'password',
                        'password_confirmation',
                        'card_number',
                        'cvv',
                        'card_cvv',
                        'payment_method',
                    ]),
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Do Not Expose Internal Error
            |--------------------------------------------------------------------------
            */

            return $this->error(
                'Unable to create booking at the moment. Please try again.',
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Booking
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        Booking $booking
    ) {
        try {
            $user = $request->user();

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            if (! $user) {
                return $this->error(
                    'Unauthenticated.',
                    401
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */

            if (
                (int) $booking->user_id !==
                (int) $user->id
            ) {
                return $this->error(
                    'Booking not found.',
                    404
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Expire Past Due Booking
            |--------------------------------------------------------------------------
            */

            $booking = $this->bookingService
                ->expireIfPastDue($booking);

            /*
            |--------------------------------------------------------------------------
            | Check Payable Status
            |--------------------------------------------------------------------------
            */

            if (! $booking->isPayable()) {
                return $this->error(
                    'Only unpaid booking holds can be cancelled online.',
                    422
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Cancel Booking
            |--------------------------------------------------------------------------
            */

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Reload Booking
            |--------------------------------------------------------------------------
            */

            $booking->load([
                'tourPackage',
                'departure',
                'travellers',
                'payments' => function ($query) {
                    $query->latest('id');
                },
            ]);

            return $this->success(
                $this->bookingPayload(
                    $booking,
                    true
                ),
                'Booking cancelled successfully.'
            );

        } catch (Throwable $e) {

            Log::error(
                'API Booking Cancellation Error',
                [
                    'user_id' => optional($request->user())->id,
                    'booking_id' => $booking->id ?? null,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return $this->error(
                'Unable to cancel booking at the moment. Please try again.',
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Booking API Payload
    |--------------------------------------------------------------------------
    */

    private function bookingPayload(
        Booking $booking,
        bool $details = false
    ): array {
        $payload = [
            'id' => $booking->id,

            'booking_reference' =>
                $booking->booking_reference ?? null,

            'user_id' => $booking->user_id,

            'status' => $booking->status,

            'tour' => $booking->relationLoaded('tourPackage')
                ? $booking->tourPackage
                : null,

            'departure' => $booking->relationLoaded('departure')
                ? $booking->departure
                : null,

            'created_at' => $booking->created_at,

            'updated_at' => $booking->updated_at,
        ];

        /*
        |--------------------------------------------------------------------------
        | Optional Booking Amount Fields
        |--------------------------------------------------------------------------
        */

        foreach ([
            'subtotal',
            'discount_amount',
            'points_discount',
            'tax_amount',
            'total_amount',
            'paid_amount',
            'due_amount',
            'currency',
            'payment_status',
        ] as $field) {

            if (
                array_key_exists(
                    $field,
                    $booking->getAttributes()
                )
            ) {
                $payload[$field] = $booking->getAttribute($field);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Travellers
        |--------------------------------------------------------------------------
        */

        if ($booking->relationLoaded('travellers')) {
            $payload['travellers'] =
                $booking->travellers;
        }

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        if (
            $details &&
            $booking->relationLoaded('payments')
        ) {
            $payload['payments'] =
                $booking->payments;
        }

        return $payload;
    }
}