@extends('layouts.app')

@section('title', 'My Profile | ' . config('travels.brand.name', 'Travels'))

@section('content')

@php
    $user = auth()->user();

    $userName = $user->username ?: $user->name ?: 'Traveller';
    $initials = collect(preg_split('/\s+/', trim($userName)))
        ->filter()
        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
        ->take(2)
        ->implode('');

    $initials = $initials ?: strtoupper(substr($userName, 0, 1));

    /*
    |--------------------------------------------------------------------------
    | Static profile data for now
    |--------------------------------------------------------------------------
    */

    $totalTrips = 8;
    $upcomingTrips = 1;
    $completedTrips = 7;
    $rewardPoints = 2450;

    $upcomingTrip = [
        'name' => 'Kerala Escape',
        'location' => 'Munnar · Alleppey · Kochi',
        'date' => '18 Sep 2026',
        'duration' => '5 Days / 4 Nights',
        'travellers' => '2 Travellers',
        'status' => 'Confirmed',
        'image' => asset('images/hero/tour-bg.jpg'),
    ];

    $travelHistory = [
        [
            'name' => 'Rajasthan Heritage Tour',
            'location' => 'Jaipur · Jodhpur · Udaipur',
            'date' => '12 Feb 2026',
            'duration' => '6 Days',
            'image' => asset('images/hero/tour-bg.jpg'),
        ],
        [
            'name' => 'Goa Beach Escape',
            'location' => 'North Goa · South Goa',
            'date' => '20 Nov 2025',
            'duration' => '4 Days',
            'image' => asset('images/hero/tour-bg.jpg'),
        ],
        [
            'name' => 'Himalayan Adventure',
            'location' => 'Manali · Solang Valley',
            'date' => '08 Jun 2025',
            'duration' => '5 Days',
            'image' => asset('images/hero/tour-bg.jpg'),
        ],
    ];
@endphp


<div class="travel-profile-page">

    <div class="travel-profile-container">


        {{-- =====================================================
             TOP ACCOUNT BAR
        ====================================================== --}}

        <div class="travel-profile-topbar">

            <div class="travel-profile-breadcrumb">

                <a href="{{ route('home') }}">
                    Home
                </a>

                <span>•</span>

                <strong>
                    My Account
                </strong>

            </div>


            <a
                href="{{ route('tours.index') }}"
                class="travel-profile-explore"
            >
                Explore journeys
                <span>↗</span>
            </a>

        </div>


        {{-- =====================================================
             MAIN ACCOUNT LAYOUT
        ====================================================== --}}

        <div class="travel-account-layout">


            {{-- =================================================
                 LEFT ACCOUNT NAVIGATION
            ================================================== --}}

            <aside class="travel-account-sidebar">

                <div class="travel-account-user">

                    <div class="travel-account-avatar">
                        {{ $initials }}
                    </div>

                    <div class="travel-account-user-info">

                        <span>WELCOME BACK</span>

                        <strong>
                            {{ $userName }}
                        </strong>

                    </div>

                </div>


                <nav class="travel-account-nav">

                    <span class="travel-account-nav-label">
                        ACCOUNT
                    </span>


                    <a
                        href="{{ route('profile') }}"
                        class="travel-account-nav-item active"
                    >
                        <span class="travel-account-nav-icon">
                            ◉
                        </span>

                        <span>
                            Overview
                        </span>
                    </a>


                    <a
                        href="{{ route('bookings.index') }}"
                        class="travel-account-nav-item"
                    >
                        <span class="travel-account-nav-icon">
                            ✈
                        </span>

                        <span>
                            My Trips
                        </span>
                    </a>


                    <a
                        href="#travel-rewards"
                        class="travel-account-nav-item"
                    >
                        <span class="travel-account-nav-icon">
                            ◆
                        </span>

                        <span>
                            Travel Rewards
                        </span>
                    </a>


                    <a
                        href="#personal-details"
                        class="travel-account-nav-item"
                    >
                        <span class="travel-account-nav-icon">
                            ○
                        </span>

                        <span>
                            Personal Details
                        </span>
                    </a>


                    <span class="travel-account-nav-label travel-account-nav-label--space">
                        DISCOVER
                    </span>


                    <a
                        href="{{ route('tours.index') }}"
                        class="travel-account-nav-item"
                    >
                        <span class="travel-account-nav-icon">
                            ◎
                        </span>

                        <span>
                            Explore Tours
                        </span>
                    </a>


                    <a
                        href="{{ route('blog.index') }}"
                        class="travel-account-nav-item"
                    >
                        <span class="travel-account-nav-icon">
                            ▤
                        </span>

                        <span>
                            Travel Stories
                        </span>
                    </a>

                </nav>


                <div class="travel-account-sidebar-bottom">

                    <div class="travel-account-help">

                        <span class="travel-account-help-icon">
                            ?
                        </span>

                        <div>
                            <strong>
                                Need help?
                            </strong>

                            <small>
                                Our travel team is here.
                            </small>
                        </div>

                    </div>


                    <a
                        href="{{ route('contact') }}"
                        class="travel-account-contact"
                    >
                        Contact us
                        <span>→</span>
                    </a>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="travel-account-logout"
                        >
                            <span>↪</span>
                            Sign out
                        </button>

                    </form>

                </div>

            </aside>


            {{-- =================================================
                 RIGHT CONTENT
            ================================================== --}}

            <main class="travel-account-content">


                {{-- =================================================
                     PROFILE INTRO
                ================================================== --}}

                <section class="travel-profile-intro">

                    <div class="travel-profile-intro-copy">

                        <span class="travel-profile-kicker">
                            YOUR TRAVEL JOURNEY
                        </span>

                        <h1>
                            Hello, {{ $userName }}.
                        </h1>

                        <p>
                            Everything you need for your next adventure,
                            all in one place.
                        </p>

                    </div>


                    <div class="travel-profile-member">

                        <span class="travel-profile-member-dot"></span>

                        <span>
                            Traveller account
                        </span>

                    </div>

                </section>


                {{-- =================================================
                     STATS STRIP
                ================================================== --}}

                <section class="travel-stat-strip">

                    <div class="travel-stat">

                        <span class="travel-stat-number">
                            {{ $totalTrips }}
                        </span>

                        <span class="travel-stat-label">
                            Trips taken
                        </span>

                    </div>


                    <div class="travel-stat">

                        <span class="travel-stat-number">
                            {{ $upcomingTrips }}
                        </span>

                        <span class="travel-stat-label">
                            Upcoming
                        </span>

                    </div>


                    <div class="travel-stat">

                        <span class="travel-stat-number">
                            {{ $completedTrips }}
                        </span>

                        <span class="travel-stat-label">
                            Completed
                        </span>

                    </div>


                    <div class="travel-stat travel-stat--reward">

                        <span class="travel-stat-number">
                            {{ number_format($rewardPoints) }}
                        </span>

                        <span class="travel-stat-label">
                            Reward points
                        </span>

                    </div>

                </section>


                {{-- =================================================
                     UPCOMING JOURNEY
                ================================================== --}}

                <section class="travel-journey-section">

                    <div class="travel-section-heading">

                        <div>

                            <span>
                                NEXT ADVENTURE
                            </span>

                            <h2>
                                Your upcoming journey
                            </h2>

                        </div>

                        <a href="{{ route('bookings.index') }}">
                            View all trips →
                        </a>

                    </div>


                    <article class="travel-next-trip">

                        <div class="travel-next-trip-image">

                            <img
                                src="{{ $upcomingTrip['image'] }}"
                                alt="{{ $upcomingTrip['name'] }}"
                                loading="lazy"
                            >

                            <span class="travel-next-trip-status">
                                {{ $upcomingTrip['status'] }}
                            </span>

                        </div>


                        <div class="travel-next-trip-content">

                            <span class="travel-next-trip-label">
                                YOUR NEXT DESTINATION
                            </span>

                            <h3>
                                {{ $upcomingTrip['name'] }}
                            </h3>

                            <p class="travel-next-trip-location">
                                {{ $upcomingTrip['location'] }}
                            </p>


                            <div class="travel-next-trip-details">

                                <div>
                                    <span>DEPARTURE</span>
                                    <strong>
                                        {{ $upcomingTrip['date'] }}
                                    </strong>
                                </div>

                                <div>
                                    <span>DURATION</span>
                                    <strong>
                                        {{ $upcomingTrip['duration'] }}
                                    </strong>
                                </div>

                                <div>
                                    <span>TRAVELLERS</span>
                                    <strong>
                                        {{ $upcomingTrip['travellers'] }}
                                    </strong>
                                </div>

                            </div>


                            <div class="travel-next-trip-footer">

                                <div>

                                    <span>
                                        Ready for your next adventure?
                                    </span>

                                    <strong>
                                        Your booking is confirmed.
                                    </strong>

                                </div>


                                <a
                                    href="{{ route('bookings.index') }}"
                                    class="travel-trip-button"
                                >
                                    View booking
                                    <span>→</span>
                                </a>

                            </div>

                        </div>

                    </article>

                </section>


                {{-- =================================================
                     TRAVEL HISTORY
                ================================================== --}}

                <section class="travel-history-section">

                    <div class="travel-section-heading">

                        <div>

                            <span>
                                YOUR JOURNEY
                            </span>

                            <h2>
                                Places you've explored
                            </h2>

                        </div>

                        <span class="travel-history-total">
                            {{ $completedTrips }} completed trips
                        </span>

                    </div>


                    <div class="travel-history-list">

                        @foreach($travelHistory as $trip)

                            <article class="travel-history-item">

                                <div class="travel-history-image">

                                    <img
                                        src="{{ $trip['image'] }}"
                                        alt="{{ $trip['name'] }}"
                                        loading="lazy"
                                    >

                                </div>


                                <div class="travel-history-main">

                                    <span class="travel-history-date">
                                        {{ $trip['date'] }}
                                    </span>

                                    <h3>
                                        {{ $trip['name'] }}
                                    </h3>

                                    <p>
                                        {{ $trip['location'] }}
                                    </p>

                                </div>


                                <div class="travel-history-meta">

                                    <span>
                                        {{ $trip['duration'] }}
                                    </span>

                                    <strong>
                                        Completed
                                    </strong>

                                </div>


                                <a
                                    href="{{ route('bookings.index') }}"
                                    class="travel-history-arrow"
                                    aria-label="View trip"
                                >
                                    →
                                </a>

                            </article>

                        @endforeach

                    </div>


                    <div class="travel-history-more">

                        <a
                            href="{{ route('bookings.index') }}"
                            class="travel-view-all"
                        >
                            View complete travel history
                            <span>→</span>
                        </a>

                    </div>

                </section>


                {{-- =================================================
                     BOTTOM GRID
                ================================================== --}}

                <div class="travel-profile-bottom-grid">


                    {{-- REWARDS --}}
                    <section
                        class="travel-reward-card"
                        id="travel-rewards"
                    >

                        <div class="travel-reward-top">

                            <div>

                                <span>
                                    TRAVEL REWARDS
                                </span>

                                <h2>
                                    {{ number_format($rewardPoints) }}
                                    <small>points</small>
                                </h2>

                            </div>

                            <div class="travel-reward-symbol">
                                ◆
                            </div>

                        </div>


                        <p>
                            Keep exploring and earn more points
                            on every journey you complete.
                        </p>


                        <div class="travel-reward-progress">

                            <div
                                class="travel-reward-progress-bar"
                                style="width: 68%;"
                            ></div>

                        </div>


                        <div class="travel-reward-progress-info">

                            <span>
                                2,450 points
                            </span>

                            <span>
                                3,600 next level
                            </span>

                        </div>


                        <a
                            href="#travel-rewards"
                            class="travel-reward-link"
                        >
                            Learn about rewards →
                        </a>

                    </section>


                    {{-- PERSONAL DETAILS --}}
                    <section
                        class="travel-details-card"
                        id="personal-details"
                    >

                        <div class="travel-details-heading">

                            <div>

                                <span>
                                    YOUR ACCOUNT
                                </span>

                                <h2>
                                    Personal details
                                </h2>

                            </div>

                        </div>


                        <div class="travel-detail-row">

                            <span>
                                Full name
                            </span>

                            <strong>
                                {{ $userName }}
                            </strong>

                        </div>


                        <div class="travel-detail-row">

                            <span>
                                Email address
                            </span>

                            <strong>
                                {{ $user->email ?: 'Not added yet' }}
                            </strong>

                        </div>


                        <div class="travel-detail-row">

                            <span>
                                Phone
                            </span>

                            <strong>
                                {{ $user->phone ?? 'Not added yet' }}
                            </strong>

                        </div>


                        <div class="travel-detail-row">

                            <span>
                                Member since
                            </span>

                            <strong>
                                {{ optional($user->created_at)->format('M Y') ?: '2026' }}
                            </strong>

                        </div>


                        <a
                            href="#personal-details"
                            class="travel-details-edit"
                        >
                            Manage personal details
                            <span>→</span>
                        </a>

                    </section>

                </div>


                {{-- =================================================
                     EXPLORE CTA
                ================================================== --}}

                <section class="travel-profile-cta">

                    <div>

                        <span>
                            READY FOR SOMEWHERE NEW?
                        </span>

                        <h2>
                            Your next favourite place
                            could be one trip away.
                        </h2>

                    </div>


                    <a
                        href="{{ route('tours.index') }}"
                        class="travel-profile-cta-button"
                    >
                        Explore tours
                        <span>→</span>
                    </a>

                </section>


            </main>

        </div>

    </div>

</div>

@endsection