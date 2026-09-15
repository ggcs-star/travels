<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Services\Bookings\BookingService;
use Illuminate\Http\Request;

class BookingController extends ApiController
{
    public function __construct(
        protected BookingService $bookingService,
    ) {
    }

    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'tourPackage:id,name,slug,cover_image',
                'departure:id,tour_package_id,departure_date,return_date,meeting_point,currency',
            ])
            ->latest('id')
            ->paginate(
                min(50, max(1, $request->integer('per_page', 15)))
            );

        return $this->success($bookings, 'Bookings retrieved successfully.');
    }

    public function show(Request $request, Booking $booking)
    {
        abort_unless(
            (int) $booking->user_id === (int) $request->user()->id,
            404
        );

        $booking->load([
            'tourPackage',
            'departure',
            'travellers',
            'payments' => fn ($q) => $q->latest('id'),
        ]);

        return $this->success($booking, 'Booking details retrieved successfully.');
    }

    public function store(
        StoreBookingRequest $request,
        TourPackage $tour,
    ) {
        if (! $tour->isAvailable()) {
            return $this->error(
                'This tour is no longer available for booking.',
                422
            );
        }

        $booking = $this->bookingService->create(
            $tour,
            $request->user(),
            $request->validated()
        );

        return $this->success(
            $booking->load(['tourPackage', 'departure', 'travellers']),
            'Booking created successfully.',
            201
        );
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_unless(
            (int) $booking->user_id === (int) $request->user()->id,
            404
        );

        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return $this->error(
                'Only unpaid booking holds can be cancelled online.',
                422
            );
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
        ]);

        return $this->success(
            $booking->fresh(),
            'Booking hold cancelled successfully.'
        );
    }
}
