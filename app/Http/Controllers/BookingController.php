<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Services\Bookings\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function create(TourPackage $tour, Request $request): View|RedirectResponse
    {
        abort_unless($tour->status === TourPackage::STATUS_PUBLISHED, 404);

        $departures = $tour->departures()
            ->bookable()
            ->withSum([
                'bookings as reserved_seats' => fn ($query) => $query->reserving(),
            ], 'traveller_count')
            ->orderBy('departure_date')
            ->get()
            ->filter(fn ($departure) => $departure->available_seats > 0)
            ->values();

        if ($departures->isEmpty()) {
            return redirect()
                ->route('tours.show', $tour)
                ->with('error', 'There are no seats available for this tour right now.');
        }

        $selectedDeparture = $departures->firstWhere(
            'id',
            $request->integer('departure')
        ) ?? $departures->first();

        return view('bookings.create', compact(
            'tour',
            'departures',
            'selectedDeparture'
        ));
    }

    public function store(
        StoreBookingRequest $request,
        TourPackage $tour
    ): RedirectResponse {
        abort_unless($tour->status === TourPackage::STATUS_PUBLISHED, 404);

        $booking = $this->bookingService->create(
            $tour,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Your seats are on hold. Complete payment to confirm your booking.');
    }

    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['tourPackage:id,name,slug', 'departure:id,tour_package_id,departure_date,return_date'])
            ->latest('id')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking, Request $request): View
    {
        $this->authorizeBooking($booking, $request);

        $booking = $this->bookingService->expireIfPastDue($booking);
        $booking->load([
            'tourPackage',
            'departure',
            'travellers',
            'payments' => fn ($query) => $query->latest('id'),
        ]);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking, Request $request): RedirectResponse
    {
        $this->authorizeBooking($booking, $request);
        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return back()->with('error', 'Only unpaid booking holds can be cancelled online.');
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Your booking hold was cancelled.');
    }

    private function authorizeBooking(Booking $booking, Request $request): void
    {
        abort_unless(
            $booking->user_id === $request->user()->id || $request->user()->isAdmin(),
            403
        );
    }
}
