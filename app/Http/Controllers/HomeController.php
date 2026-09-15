<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the travel website homepage.
     */
    public function index(): View
    {
        $monthlyTours = TourPackage::query()
            ->published()
            ->whereHas('category', function (Builder $query) {
                $query->where('slug', 'monthly-tours');
            })
            ->with([
                'category:id,name,slug',
                'images:id,tour_package_id,image_path,sort_order',
                'departures' => function ($query) {
                    $query
                        ->where('status', 'open')
                        ->whereDate(
                            'departure_date',
                            '>=',
                            now()->toDateString()
                        )
                        ->orderBy('departure_date');
                },
            ])
            ->withMin(
                [
                    'departures as lowest_price' => function ($query) {
                        $query
                            ->where('status', 'open')
                            ->whereDate(
                                'departure_date',
                                '>=',
                                now()->toDateString()
                            );
                    },
                ],
                'price'
            )
            ->latest('id')
            ->limit(10)
            ->get();

        return view(
            'home',
            compact('monthlyTours')
        );
    }
}