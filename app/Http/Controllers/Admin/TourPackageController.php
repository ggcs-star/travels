<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPackageRequest;
use App\Http\Requests\Admin\UpdateTourPackageRequest;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Services\Tour\TourPackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourPackageController extends Controller
{
    public function __construct(
        private readonly TourPackageService $tourPackageService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $categories = TourCategory::query()
            ->active()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tours = TourPackage::query()
            ->with([
                'category:id,name,slug',
            ])
            ->withCount([
                'images',
                'departures',
                'bookings',
            ])
            ->search(
                $request->input('search')
            )
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status',
                        $request->input('status')
                    )
            )
            ->when(
                $request->filled('category_id'),
                fn ($query) =>
                    $query->where(
                        'category_id',
                        $request->integer('category_id')
                    )
            )
            ->when(
                $request->filled('destination'),
                fn ($query) =>
                    $query->where(
                        'destination',
                        'like',
                        '%' . trim(
                            $request->input('destination')
                        ) . '%'
                    )
            )
            ->when(
                $request->filled('featured'),
                fn ($query) =>
                    $query->where(
                        'featured',
                        $request->boolean('featured')
                    )
            )
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.tours.index',
            compact(
                'tours',
                'categories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $categories = TourCategory::query()
            ->active()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return view(
            'admin.tours.create',
            compact('categories')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreTourPackageRequest $request
    ): RedirectResponse {
        $tour = $this->tourPackageService->create(
            $request->validated(),
            (int) auth()->id()
        );

        return redirect()
            ->route(
                'admin.tours.edit',
                $tour
            )
            ->with(
                'success',
                'Tour package created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        TourPackage $tour
    ): View {
        $tour->load([
            'category',
            'creator',
            'images',
            'departures',
        ]);

        return view(
            'admin.tours.show',
            compact('tour')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        TourPackage $tour
    ): View {
        $categories = TourCategory::query()
            ->active()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tour->load([
            'category',
            'creator',
            'images',
            'departures',
        ]);

        return view(
            'admin.tours.edit',
            compact(
                'tour',
                'categories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateTourPackageRequest $request,
        TourPackage $tour
    ): RedirectResponse {
        $this->tourPackageService->update(
            $tour,
            $request->validated()
        );

        return redirect()
            ->route(
                'admin.tours.edit',
                $tour
            )
            ->with(
                'success',
                'Tour package updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        TourPackage $tour
    ): RedirectResponse {
        $copy = $this->tourPackageService->duplicate(
            $tour
        );

        return redirect()
            ->route(
                'admin.tours.edit',
                $copy
            )
            ->with(
                'success',
                'Tour package duplicated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        TourPackage $tour
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', [
                    TourPackage::STATUS_DRAFT,
                    TourPackage::STATUS_PUBLISHED,
                    TourPackage::STATUS_INACTIVE,
                ]),
            ],
        ]);

        $this->tourPackageService->changeStatus(
            $tour,
            $validated['status']
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Tour package status updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        TourPackage $tour
    ): RedirectResponse {
        $tour->delete();

        return redirect()
            ->route(
                'admin.tours.index'
            )
            ->with(
                'success',
                'Tour package deleted successfully.'
            );
    }
}