@extends('layouts.app')

@section('title', 'My Bookings | '.config('travels.brand.name'))

@section('content')

<div class="my-bookings-page">

    {{-- PAGE HERO --}}
    <section class="my-bookings-hero">

        <div class="container">

            <div class="my-bookings-hero__content">

                <span class="my-bookings-eyebrow">
                    YOUR ACCOUNT
                </span>

                <h1>
                    My Bookings
                </h1>

                <p>
                    Track your journeys, payment status and travel details
                    in one place.
                </p>

            </div>

            <a
                href="{{ route('tours.index') }}"
                class="my-bookings-explore-btn"
            >
                Explore Tours
                <span>→</span>
            </a>

        </div>

    </section>


    {{-- BOOKINGS --}}
    <section class="my-bookings-section">

        <div class="container">

            @if(session('success'))

                <div class="my-bookings-alert">
                    {{ session('success') }}
                </div>

            @endif


            @if($bookings->count())

                <div class="my-bookings-list">

                    @foreach($bookings as $booking)

                        <article class="my-booking-card">

                            {{-- TOUR IMAGE --}}
                            <a
                                href="{{ route('bookings.show', $booking) }}"
                                class="my-booking-card__image"
                            >

                                @if($booking->tourPackage?->cover_image)

                                    <img
                                        src="{{ $booking->tourPackage->cover_image_url }}"
                                        alt="{{ $booking->tourPackage->name }}"
                                        loading="lazy"
                                    >

                                @else

                                    <div class="my-booking-card__image-placeholder">
                                        <span>TRAVEL</span>
                                    </div>

                                @endif

                            </a>


                            {{-- MAIN INFO --}}
                            <div class="my-booking-card__content">

                                <span class="my-booking-card__number">
                                    {{ $booking->booking_number }}
                                </span>

                                <h2>
                                    {{ $booking->tourPackage->name }}
                                </h2>

                                <div class="my-booking-card__meta">

                                    <span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M7 2v3M17 2v3M3 9h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                                        </svg>

                                        {{ $booking->departure->departure_date->format('d M Y') }}
                                    </span>

                                    <span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="9" cy="8" r="3"/>
                                            <path d="M3 20a6 6 0 0 1 12 0"/>
                                            <path d="M16 11a3 3 0 1 0 0-6"/>
                                            <path d="M18 20a5 5 0 0 0-2-4"/>
                                        </svg>

                                        {{ $booking->traveller_count }}
                                        {{ Str::plural('traveller', $booking->traveller_count) }}
                                    </span>

                                </div>

                            </div>


                            {{-- RIGHT SIDE --}}
                            <div class="my-booking-card__right">

                                <span
                                    class="my-booking-status my-booking-status--{{ $booking->status }}"
                                >
                                    {{ Str::headline($booking->status) }}
                                </span>

                                <div class="my-booking-card__amount">
                                    ₹{{ number_format((float) $booking->total_amount, 0) }}
                                </div>

                                <a
                                    href="{{ route('bookings.show', $booking) }}"
                                    class="my-booking-view-btn"
                                >
                                    View Booking
                                    <span>→</span>
                                </a>

                            </div>

                        </article>

                    @endforeach

                </div>


                @if($bookings->hasPages())

                    <div class="my-bookings-pagination">
                        {{ $bookings->links() }}
                    </div>

                @endif


            @else

                <div class="my-bookings-empty">

                    <div class="my-bookings-empty__icon">
                        ✈
                    </div>

                    <h2>
                        No bookings yet
                    </h2>

                    <p>
                        Your upcoming journeys will appear here after
                        you reserve a departure.
                    </p>

                    <a
                        href="{{ route('tours.index') }}"
                        class="my-bookings-explore-btn"
                    >
                        Explore Tours
                        <span>→</span>
                    </a>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection