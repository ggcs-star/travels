<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use App\Models\Booking;



class AdminController extends Controller
{

public function dashboard()
{
    $tourPackagesCount = TourPackage::count();

    $publishedToursCount = TourPackage::published()->count();

    $pendingBookingsCount = Booking::where('status', Booking::STATUS_PENDING_PAYMENT)->count();

    $confirmedBookingsCount = Booking::where('status', Booking::STATUS_CONFIRMED)->count();

    return view('admin.dashboard', compact(
        'tourPackagesCount',
        'publishedToursCount',
        'pendingBookingsCount',
        'confirmedBookingsCount'
    ));
}
}
