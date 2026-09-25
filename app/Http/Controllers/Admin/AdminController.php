<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Booking;
use App\Models\ContactInquiry;
use App\Models\Page;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\UserPointWallet;

class AdminController extends Controller
{
    public function dashboard()
    {
        $tourPackagesCount = TourPackage::count();

        $publishedToursCount = TourPackage::published()->count();

        $tourCategoriesCount = TourCategory::count();

        $totalBookingsCount = Booking::count();

        $pendingBookingsCount = Booking::where('status', Booking::STATUS_PENDING_PAYMENT)->count();

        $confirmedBookingsCount = Booking::where('status', Booking::STATUS_CONFIRMED)->count();

        $totalRevenue = (float) Booking::query()
            ->where('payment_status', Booking::PAYMENT_PAID)
            ->selectRaw('COALESCE(SUM(total_amount - points_discount), 0) as revenue')
            ->value('revenue');

        $customersCount = User::where('role', 'user')->count();

        $blogPostsCount = Blog::count();

        $publishedBlogCount = Blog::where('status', Blog::STATUS_PUBLISHED)->count();

        $pendingInquiriesCount = ContactInquiry::whereNull('read_at')->count();

        $pagesCount = Page::count();

        $totalPointsIssued = (int) UserPointWallet::sum('balance');

        $topUsersByBookings = User::query()
            ->where('role', 'user')
            ->withCount('bookings')
            ->having('bookings_count', '>', 0)
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        $topUsersByPoints = UserPointWallet::query()
            ->with('user:id,name,email')
            ->whereHas('user', fn ($query) => $query->where('role', 'user'))
            ->where('balance', '>', 0)
            ->orderByDesc('balance')
            ->limit(5)
            ->get();

        $recentBookings = Booking::query()
            ->with([
                'user:id,name,email',
                'tourPackage:id,name',
            ])
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'tourPackagesCount',
            'publishedToursCount',
            'tourCategoriesCount',
            'totalBookingsCount',
            'pendingBookingsCount',
            'confirmedBookingsCount',
            'totalRevenue',
            'customersCount',
            'blogPostsCount',
            'publishedBlogCount',
            'pendingInquiriesCount',
            'pagesCount',
            'totalPointsIssued',
            'topUsersByBookings',
            'topUsersByPoints',
            'recentBookings'
        ));
    }
}
