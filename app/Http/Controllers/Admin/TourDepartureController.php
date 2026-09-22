<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourDepartureRequest;
use App\Http\Requests\Admin\UpdateTourDepartureRequest;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourDepartureController extends Controller
{
    public function index(TourPackage $tour): View
    {
        $departures = $tour->departures()
            ->withSum([
                'bookings as reserved_seats' => fn ($query) => $query->reserving(),
            ], 'traveller_count')
            ->orderBy('departure_date')
            ->paginate(10);

        return view('admin.departures.index', compact('tour', 'departures'));
    }

    public function create(TourPackage $tour): View
    {
        return view('admin.departures.create', compact('tour'));
    }

    public function store(
        StoreTourDepartureRequest $request,
        TourPackage $tour
    ): RedirectResponse {
        $tour->departures()->create($request->validated());

        return redirect()
            ->route('admin.tours.departures.index', $tour)
            ->with('success', 'Departure added successfully.');
    }

    public function edit(
        TourPackage $tour,
        TourDeparture $departure
    ): View {
        $this->ensureDepartureBelongsToTour($tour, $departure);

        return view('admin.departures.edit', compact('tour', 'departure'));
    }

    public function update(
        UpdateTourDepartureRequest $request,
        TourPackage $tour,
        TourDeparture $departure
    ): RedirectResponse {
        $this->ensureDepartureBelongsToTour($tour, $departure);

        $data = $request->validated();
        $reservedSeats = $departure->bookings()->reserving()->sum('traveller_count');

        if ($data['capacity'] < $reservedSeats) {
            return back()
                ->withInput()
                ->withErrors([
                    'capacity' => "Capacity cannot be lower than the {$reservedSeats} reserved seats.",
                ]);
        }

        $departure->update($data);

        return redirect()
            ->route('admin.tours.departures.index', $tour)
            ->with('success', 'Departure updated successfully.');
    }

    public function destroy(
        TourPackage $tour,
        TourDeparture $departure
    ): RedirectResponse {
        $this->ensureDepartureBelongsToTour($tour, $departure);

        if ($departure->bookings()->exists()) {
            return back()->with('error', 'A departure with bookings cannot be deleted. Close it instead.');
        }

        $departure->delete();

        return back()->with('success', 'Departure deleted.');
    }

    private function ensureDepartureBelongsToTour(
        TourPackage $tour,
        TourDeparture $departure
    ): void {
        abort_unless($departure->tour_package_id === $tour->id, 404);
    }
}
