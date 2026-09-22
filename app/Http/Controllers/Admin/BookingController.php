<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingTraveller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    /**
     * Display all bookings.
     */
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with([
                'user:id,username,email',
                'tourPackage:id,name,slug',
                'departure:id,tour_package_id,departure_date,return_date',
            ])

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = trim(
                        (string) $request->input('search')
                    );

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'booking_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_phone',
                                'like',
                                "%{$search}%"
                            );

                    });
                }
            )

            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->input('status')
                )
            )

            ->when(
                $request->filled('payment_status'),
                fn ($query) => $query->where(
                    'payment_status',
                    $request->input('payment_status')
                )
            )

            ->when(
                $request->filled('tour_package_id'),
                fn ($query) => $query->where(
                    'tour_package_id',
                    $request->integer('tour_package_id')
                )
            )

            ->latest('id')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Booking Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' => Booking::query()->count(),

            'pending_payment' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_PENDING_PAYMENT
                )
                ->count(),

            'confirmed' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_CONFIRMED
                )
                ->count(),

            'cancelled' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_CANCELLED
                )
                ->count(),

            'expired' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_EXPIRED
                )
                ->count(),

            'paid' => Booking::query()
                ->where(
                    'payment_status',
                    Booking::PAYMENT_PAID
                )
                ->count(),

            'unpaid' => Booking::query()
                ->where(
                    'payment_status',
                    Booking::PAYMENT_UNPAID
                )
                ->count(),
        ];


        return view(
            'admin.bookings.index',
            compact(
                'bookings',
                'stats'
            )
        );
    }


    /**
     * Display a single booking.
     */
    public function show(
        Booking $booking
    ): View {

        $booking->load([
            'user',
            'tourPackage',
            'departure',

            'travellers',

            'payments' => fn ($query) =>
                $query->latest('id'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Booking Statistics
        |--------------------------------------------------------------------------
        */

        $reservedSeats = Booking::query()
            ->reserving()
            ->where(
                'tour_departure_id',
                $booking->tour_departure_id
            )
            ->sum('traveller_count');


        /*
        |--------------------------------------------------------------------------
        | Available Seats
        |--------------------------------------------------------------------------
        */

        $departureCapacity =
            (int) $booking->departure->capacity;

        $availableSeats = max(
            0,
            $departureCapacity - $reservedSeats
        );


        return view(
            'admin.bookings.show',
            compact(
                'booking',
                'reservedSeats',
                'availableSeats'
            )
        );
    }


    /**
     * Securely view an individual traveller's ID proof.
     *
     * ID proof files must never be exposed through
     * a public storage URL.
     */
    public function idProof(
        Booking $booking,
        BookingTraveller $traveller
    ): StreamedResponse {

        /*
        |--------------------------------------------------------------------------
        | Make sure traveller belongs to this booking
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $traveller->booking_id === (int) $booking->id,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure document exists
        |--------------------------------------------------------------------------
        */

        abort_unless(
            filled($traveller->id_proof_document),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure private file exists
        |--------------------------------------------------------------------------
        */

        $disk = Storage::disk('private');

        abort_unless(
            $disk->exists(
                $traveller->id_proof_document
            ),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Stream File
        |--------------------------------------------------------------------------
        */

        return $disk->response(
            $traveller->id_proof_document
        );
    }
}