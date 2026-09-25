<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingTraveller;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Models\User;
use App\Services\Bookings\BookingService;
use App\Services\Points\PointSettingService;
use App\Services\Points\PointWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly PointWalletService $pointWalletService,
        private readonly PointSettingService $pointSettingService
    ) {}

    /**
     * Show the form to create a booking on behalf of a customer.
     */
    public function create(): View
    {
        $users = User::query()
            ->where('role', 'user')
            ->with('pointWallet')
            ->orderBy('name')
            ->get();

        $tours = TourPackage::query()
            ->where('status', TourPackage::STATUS_PUBLISHED)
            ->with([
                'departures' => fn ($query) => $query
                    ->bookable()
                    ->withSum([
                        'bookings as reserved_seats' =>
                            fn ($query) => $query->reserving(),
                    ], 'traveller_count')
                    ->orderBy('departure_date'),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (TourPackage $tour) {
                $tour->setRelation(
                    'departures',
                    $tour->departures
                        ->filter(
                            fn ($departure) => $departure->available_seats > 0
                        )
                        ->values()
                );

                return $tour;
            })
            ->filter(fn (TourPackage $tour) => $tour->departures->isNotEmpty())
            ->values();

        return view(
            'admin.bookings.create',
            compact('users', 'tours')
        );
    }

    /**
     * Store a booking created by the admin on behalf of a customer.
     *
     * Seats are reserved immediately, exactly like a customer-created
     * booking. The admin then confirms payment on the next screen,
     * where points can also be applied on the customer's behalf.
     */
    public function store(
        AdminStoreBookingRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $departure = TourDeparture::query()
            ->findOrFail($data['departure_id']);

        $tour = $departure->tourPackage;

        $customer = User::query()
            ->findOrFail($data['user_id']);

        try {
            $booking = $this->bookingService->create(
                $tour,
                $customer,
                $data
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()
            ->route('admin.bookings.checkout', $booking)
            ->with(
                'success',
                'Booking created. Apply points if needed, then confirm payment.'
            );
    }

    /**
     * Show the payment/points confirmation screen for an
     * admin-created booking that is still awaiting payment.
     */
    public function checkout(Booking $booking): View|RedirectResponse
    {
        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with(
                    'error',
                    'This booking hold is no longer payable.'
                );
        }

        $booking->load([
            'user.pointWallet',
            'tourPackage',
            'departure',
            'travellers',
        ]);

        $availablePoints = (int) (
            $booking->user?->pointWallet?->balance ?? 0
        );

        $redemption = $this->pointSettingService->calculateRedemption(
            availablePoints: $availablePoints,
            bookingAmount: (float) $booking->total_amount
        );

        return view(
            'admin.bookings.checkout',
            compact('booking', 'availablePoints', 'redemption')
        );
    }

    /**
     * Apply the customer's points to the booking on their behalf.
     */
    public function applyPoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'points' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        if (! $booking->user) {
            return back()->with(
                'error',
                'This booking has no linked customer account.'
            );
        }

        try {
            $this->bookingService->applyPoints(
                booking: $booking,
                user: $booking->user,
                points: (int) $data['points'],
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()
            ->route('admin.bookings.checkout', $booking)
            ->with('success', 'Points applied successfully.');
    }

    /**
     * Remove previously applied points from the booking.
     */
    public function removePoints(Booking $booking): RedirectResponse
    {
        if ($booking->user) {
            $this->bookingService->removePoints(
                booking: $booking,
                user: $booking->user,
            );
        }

        return redirect()
            ->route('admin.bookings.checkout', $booking)
            ->with('success', 'Applied points have been removed.');
    }

    /**
     * Confirm the booking as paid without an online payment gateway.
     *
     * This finalizes any applied points discount (debiting the
     * customer's wallet) and awards the booking's reward points.
     */
    public function confirm(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        try {
            $this->bookingService->confirmOfflinePayment(
                booking: $booking,
                paymentMethod: 'admin_offline',
                metadata: [
                    'admin_user_id' => $request->user()->id,
                    'admin_user_name' => $request->user()->name,
                    'note' => 'Booked by admin on behalf of the customer.',
                ],
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.bookings.checkout', $booking)
                ->with(
                    'error',
                    collect($exception->errors())->flatten()->first()
                        ?? 'This booking could not be confirmed.'
                );
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with(
                'success',
                'Booking confirmed and marked as paid.'
            );
    }

    /**
     * Soft delete a booking.
     *
     * The underlying payment and traveller records are left intact;
     * only the booking is hidden from default queries and can be
     * restored later if needed.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Manually correct a booking's payment status.
     *
     * This only updates the flag itself — it does not create a
     * Payment record, touch the booking's confirmation status, or
     * process points. Use the checkout/confirm flow for a proper
     * offline payment; use this for corrections (e.g. marking a
     * booking refunded or failed after the fact).
     */
    public function updatePaymentStatus(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'payment_status' => [
                'required',
                Rule::in([
                    Booking::PAYMENT_UNPAID,
                    Booking::PAYMENT_PAID,
                    Booking::PAYMENT_FAILED,
                    Booking::PAYMENT_REFUNDED,
                ]),
            ],
        ]);

        $booking->update([
            'payment_status' => $data['payment_status'],
        ]);

        return back()->with(
            'success',
            'Payment status updated to '
                . Str::headline($data['payment_status'])
                . '.'
        );
    }

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