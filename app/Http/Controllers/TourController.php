<?php

namespace App\Http\Controllers;

use App\Models\TourCategory;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    /**
     * Display the home page.
     */
    public function home(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Monthly Tours
        |--------------------------------------------------------------------------
        */

        $monthlyTours = TourPackage::query()
            ->available()
            ->whereHas('category', function (Builder $query) {
                $query->where(
                    'slug',
                    'monthly-tours'
                );
            })
            ->with([
                'category:id,name,slug',

                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),

                'departures' => fn ($query) => $query
                    ->bookable()
                    ->orderBy('departure_date')
                    ->limit(1),
            ])
            ->orderByDesc('featured')
            ->latest('id')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Featured Tours
        |--------------------------------------------------------------------------
        */

        $featuredTours = TourPackage::query()
            ->available()
            ->featured()
            ->with([
                'category:id,name,slug',

                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),

                'departures' => fn ($query) => $query
                    ->bookable()
                    ->orderBy('departure_date')
                    ->limit(1),
            ])
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Homepage Categories
        |--------------------------------------------------------------------------
        */

        $categories = TourCategory::query()
            ->active()
            ->withCount([
                'packages' => fn ($query) => $query
                    ->available(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'pages.home',
            compact(
                'monthlyTours',
                'featuredTours',
                'categories'
            )
        );
    }

    /**
     * Display all available tours.
     */
    public function index(Request $request): View
    {
        $tours = TourPackage::query()
            ->available()

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = trim(
                        (string) $request->input('search')
                    );

                    $query->where(
                        function ($query) use ($search) {

                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'destination',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'starting_city',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'ending_city',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'short_description',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('category'),
                function ($query) use ($request) {

                    $category = trim(
                        (string) $request->input('category')
                    );

                    $query->whereHas(
                        'category',
                        function ($categoryQuery) use ($category) {

                            $categoryQuery->where(
                                'slug',
                                $category
                            );
                        }
                    );
                }
            )

            ->with([
                'category:id,name,slug',

                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),

                'departures' => fn ($query) => $query
                    ->bookable()
                    ->orderBy('departure_date')
                    ->limit(1),
            ])

            ->orderByDesc('featured')
            ->latest('id')

            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = TourCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return view(
            'pages.tours.index',
            compact(
                'tours',
                'categories'
            )
        );
    }

    /**
     * Display a single available tour.
     */
    public function show(
        TourPackage $tour
    ): View {

        /*
        |--------------------------------------------------------------------------
        | Availability
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $tour->isAvailable(),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Package Data
        |--------------------------------------------------------------------------
        */

        $tour->load([
            'category',
            'creator',

            'images' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('id'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Bookable Departures
        |--------------------------------------------------------------------------
        */

        $departures = $tour
            ->departures()
            ->bookable()
            ->orderBy('departure_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

        $seo = [
            'title' => $tour->meta_title
                ?: $tour->name,

            'description' => $tour->meta_description
                ?: $tour->short_description,

            'keywords' => $tour->meta_keywords,

            'canonical' => $tour->canonical_url
                ?: route(
                    'tours.show',
                    $tour->slug
                ),

            'robots' => $tour->robots
                ?: 'index,follow',
        ];

        return view(
            'pages.tours.show',
            compact(
                'tour',
                'departures',
                'seo'
            )
        );
    }
}