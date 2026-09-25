@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('description', 'Overview of your travel business.')

@section('content')

<div class="admin-page db-page">

    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section class="tp-hero db-hero">

        <div class="tp-hero__top">

            <div>
                <span class="tp-hero__eyebrow">{{ now()->format('l, d M Y') }}</span>
                <h1>Welcome back, {{ auth()->user()->name ?: auth()->user()->username }}</h1>
                <p>
                    Here's what's happening across your travel business today.
                </p>
            </div>

            <div class="tp-hero__actions">
                <a href="{{ route('admin.bookings.create') }}" class="tp-hero__btn tp-hero__btn--solid">
                    + New Booking
                </a>
                <a href="{{ route('home') }}" target="_blank" class="tp-hero__btn">
                    View Website ↗
                </a>
            </div>

        </div>

    </section>


    {{-- =========================================================
         STAT CARDS
         ========================================================= --}}

    <div class="db-stats">

        <a href="{{ route('admin.tours.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
            </span>
            <div>
                <small>Tour Packages</small>
                <strong>{{ number_format($tourPackagesCount) }}</strong>
                <span class="bd-stat__sub">{{ number_format($publishedToursCount) }} published</span>
            </div>
        </a>

        <a href="{{ route('admin.tour-categories.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            </span>
            <div>
                <small>Tour Categories</small>
                <strong>{{ number_format($tourCategoriesCount) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.bookings.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/></svg>
            </span>
            <div>
                <small>Total Bookings</small>
                <strong>{{ number_format($totalBookingsCount) }}</strong>
                <span class="bd-stat__sub">{{ number_format($confirmedBookingsCount) }} confirmed</span>
            </div>
        </a>

        <a href="{{ route('admin.bookings.index', ['status' => 'pending_payment']) }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
            </span>
            <div>
                <small>Pending Payments</small>
                <strong>{{ number_format($pendingBookingsCount) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.bookings.index', ['payment_status' => 'paid']) }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/><path d="M12 3v18"/></svg>
            </span>
            <div>
                <small>Total Revenue</small>
                <strong>₹{{ number_format($totalRevenue, 0) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.point-wallets.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div>
                <small>Customers</small>
                <strong>{{ number_format($customersCount) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.point-wallets.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
            </span>
            <div>
                <small>Points Issued</small>
                <strong>{{ number_format($totalPointsIssued) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.blog.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z"/><path d="M14 2v6h6"/></svg>
            </span>
            <div>
                <small>Blog Posts</small>
                <strong>{{ number_format($blogPostsCount) }}</strong>
                <span class="bd-stat__sub">{{ number_format($publishedBlogCount) }} published</span>
            </div>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m3 6 9 7 9-7"/></svg>
            </span>
            <div>
                <small>New Inquiries</small>
                <strong>{{ number_format($pendingInquiriesCount) }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.pages.index') }}" class="bd-stat db-stat-link">
            <span class="bd-stat__icon bd-stat__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/></svg>
            </span>
            <div>
                <small>Pages</small>
                <strong>{{ number_format($pagesCount) }}</strong>
            </div>
        </a>

    </div>


    {{-- =========================================================
         ROW 1: TOP CUSTOMERS BY BOOKINGS / TOP CUSTOMERS BY POINTS
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                <h2>Top 5 Customers by Bookings</h2>
            </div>

            @if($topUsersByBookings->isNotEmpty())

                <div class="db-rank-list">

                    @foreach($topUsersByBookings as $index => $user)

                        <a href="{{ route('admin.point-wallets.show', $user) }}" class="db-rank-item">
                            <span class="db-rank-item__pos">{{ $index + 1 }}</span>
                            <span class="db-rank-item__avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                            <span class="db-rank-item__info">
                                <strong>{{ $user->name }}</strong>
                                <small>{{ $user->email }}</small>
                            </span>
                            <span class="db-rank-item__value">{{ $user->bookings_count }} {{ Str::plural('booking', $user->bookings_count) }}</span>
                        </a>

                    @endforeach

                </div>

            @else

                <p class="admin-muted">No bookings yet.</p>

            @endif

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
                <h2>Top 5 Customers by Points</h2>
            </div>

            @if($topUsersByPoints->isNotEmpty())

                <div class="db-rank-list">

                    @foreach($topUsersByPoints as $index => $wallet)

                        <a href="{{ route('admin.point-wallets.show', $wallet->user) }}" class="db-rank-item">
                            <span class="db-rank-item__pos">{{ $index + 1 }}</span>
                            <span class="db-rank-item__avatar">{{ strtoupper(substr($wallet->user->name ?? 'U', 0, 1)) }}</span>
                            <span class="db-rank-item__info">
                                <strong>{{ $wallet->user->name }}</strong>
                                <small>{{ $wallet->user->email }}</small>
                            </span>
                            <span class="db-rank-item__value">{{ number_format($wallet->balance) }} pts</span>
                        </a>

                    @endforeach

                </div>

            @else

                <p class="admin-muted">No points issued yet.</p>

            @endif

        </section>

    </div>


    {{-- =========================================================
         RECENT BOOKINGS (full width)
         ========================================================= --}}

    <section class="bd-card">

        <div class="bd-card__header bd-card__header--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/></svg>
            <h2>Recent Bookings</h2>
            <a href="{{ route('admin.bookings.index') }}" class="bd-card__count bd-card__count--link">
                View all →
            </a>
        </div>

        @if($recentBookings->isNotEmpty())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Tour</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($recentBookings as $booking)

                            <tr>
                                <td>
                                    <strong>{{ $booking->booking_number }}</strong>
                                    <small style="display:block;margin-top:3px;color:var(--admin-text-light);">{{ $booking->created_at->format('d M Y') }}</small>
                                </td>

                                <td>{{ $booking->user->name ?? $booking->contact_name }}</td>

                                <td>{{ $booking->tourPackage->name ?? '—' }}</td>

                                <td>{{ $booking->currency }} {{ number_format((float) $booking->payableAmount(), 2) }}</td>

                                <td>
                                    <span class="booking-pill booking-pill--status-{{ $booking->status }}">
                                        {{ Str::headline($booking->status) }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="admin-table-action" title="View">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="admin-sr-only">View</span>
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <p class="admin-muted">No bookings yet.</p>

        @endif

    </section>


    {{-- =========================================================
         ROW 2: QUICK ACTIONS / ACCOUNT & WEBSITE
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                <h2>Quick Actions</h2>
            </div>

            <div class="admin-actions">

                <a href="{{ route('admin.tours.create') }}" class="admin-action">
                    <span class="admin-action__icon">+</span>
                    <span class="admin-action__content">
                        <strong>Add Tour Package</strong>
                        <small>Create a new tour package</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

                <a href="{{ route('admin.bookings.create') }}" class="admin-action">
                    <span class="admin-action__icon">+</span>
                    <span class="admin-action__content">
                        <strong>Create Booking</strong>
                        <small>Book on behalf of a customer</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

                <a href="{{ route('admin.tour-categories.create') }}" class="admin-action">
                    <span class="admin-action__icon">+</span>
                    <span class="admin-action__content">
                        <strong>Add Tour Category</strong>
                        <small>Organize your packages</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

                <a href="{{ route('admin.blog.create') }}" class="admin-action">
                    <span class="admin-action__icon">+</span>
                    <span class="admin-action__content">
                        <strong>Write Blog</strong>
                        <small>Publish a new article</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

            </div>

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                <h2>Website &amp; Account</h2>
            </div>

            <div class="admin-status">

                <span class="admin-status__dot"></span>

                <div>
                    <strong>Website is Live</strong>
                    <p>Your public website is currently accessible.</p>
                </div>

                <a href="{{ route('home') }}" target="_blank">View</a>

            </div>

            <div class="admin-account" style="margin-top:16px;">

                <div class="admin-account__avatar">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>

                <div class="admin-account__content">
                    <strong>{{ auth()->user()->username }}</strong>
                    <p>{{ auth()->user()->email }}</p>
                </div>

                <a href="{{ route('admin.profile') }}">Edit</a>

            </div>

        </section>

    </div>

</div>


<style>

.db-page {
    max-width: 1360px;
    margin: 0 auto;
}


/* Hero (reuses tp-hero pattern) */

.tp-hero {
    padding: 26px 28px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: linear-gradient(120deg, #0b302b 0%, #14532d 45%, #d97706 130%);
    color: #fff;
}

.tp-hero__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.tp-hero__eyebrow {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, .85);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.tp-hero__top h1 {
    margin: 0;
    color: #fff;
    font-size: 26px;
    font-weight: 800;
}

.tp-hero__top p {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, .9);
    font-size: 13.5px;
}

.tp-hero__actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tp-hero__btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .16);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 650;
    white-space: nowrap;
}

.tp-hero__btn:hover {
    background: rgba(255, 255, 255, .28);
}

.tp-hero__btn--solid {
    background: #fff;
    color: #0b302b;
}

.tp-hero__btn--solid:hover {
    background: #f1f3f5;
}


/* Stat cards (reuses bd-stat pattern, more columns) */

.db-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.bd-stat {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 0;
    padding: 16px;
    border: 1px solid #ece3d6;
    border-radius: 13px;
    background: #fffaf3;
}

.db-stat-link {
    text-decoration: none;
    transition: transform .15s ease, box-shadow .15s ease;
}

.db-stat-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 24, 40, .08);
}

.bd-stat__icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.bd-stat__icon svg {
    width: 18px;
    height: 18px;
}

.bd-stat__icon--blue {
    background: #e8f1ff;
    color: #2563eb;
}

.bd-stat__icon--green {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.bd-stat__icon--orange {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.bd-stat__icon--gold {
    background: #fff8e1;
    color: #b7871a;
}

.bd-stat > div {
    min-width: 0;
}

.bd-stat small {
    display: block;
    color: #8c95a2;
    font-size: 12px;
    font-weight: 650;
}

.bd-stat strong {
    display: block;
    margin-top: 3px;
    color: #202b3e;
    font-size: 18px;
    font-weight: 750;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bd-stat__sub {
    display: block;
    margin-top: 4px;
    color: #9aa2ae;
    font-size: 11px;
}


/* Grid / Card (reuses bd-card pattern) */

.bd-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    margin-bottom: 0;
}

.bd-card {
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
    padding: 20px;
    margin-bottom: 18px;
}

.bd-card__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eef0f2;
}

.bd-card__header svg {
    width: 19px;
    height: 19px;
    flex: 0 0 19px;
}

.bd-card__header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--admin-text, #202b3e);
}

.bd-card__header--orange svg {
    color: var(--admin-primary, #d97706);
}

.bd-card__header--green svg {
    color: var(--admin-success, #15803d);
}

.bd-card__header--gold svg {
    color: #b7871a;
}

.bd-card__count {
    margin-left: auto;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #586273;
    font-size: 11px;
    font-weight: 650;
}

.bd-card__count--link {
    text-decoration: none;
    background: var(--admin-primary, #d97706);
    color: #fff;
}

.bd-card__count--link:hover {
    background: var(--admin-primary-dark, #b45309);
}

.booking-pill {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #4b5666;
    font-size: 12px;
    font-weight: 700;
}

.booking-pill--status-confirmed {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.booking-pill--status-cancelled {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.booking-pill--status-expired,
.booking-pill--status-pending_payment {
    background: #fffbeb;
    color: #b45309;
}


/* Rank lists */

.db-rank-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.db-rank-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 8px;
    border-radius: 9px;
    text-decoration: none;
    transition: background .15s ease;
}

.db-rank-item:hover {
    background: #fafbfc;
}

.db-rank-item__pos {
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f1f3f5;
    color: #586273;
    font-size: 11px;
    font-weight: 750;
}

.db-rank-item__avatar {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--admin-sidebar, #f5faf6);
    color: var(--admin-sidebar-dark, #14532d);
    font-size: 12px;
    font-weight: 750;
}

.db-rank-item__info {
    flex: 1;
    min-width: 0;
}

.db-rank-item__info strong {
    display: block;
    overflow: hidden;
    color: #344054;
    font-size: 13px;
    font-weight: 650;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.db-rank-item__info small {
    display: block;
    margin-top: 2px;
    overflow: hidden;
    color: #9aa2ae;
    font-size: 11px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.db-rank-item__value {
    flex: 0 0 auto;
    color: var(--admin-primary-dark, #b45309);
    font-size: 12.5px;
    font-weight: 700;
    white-space: nowrap;
}


@media (max-width: 1150px) {
    .bd-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .tp-hero__top {
        flex-direction: column;
    }
}

</style>

@endsection
