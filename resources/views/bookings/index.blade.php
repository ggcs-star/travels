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

            {{-- SEARCH & FILTER --}}
            <form
                method="GET"
                action="{{ route('bookings.index') }}"
                class="tour-search-card"
            >

                <div class="tour-search-field">

                    <label for="search">
                        Search
                    </label>

                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Booking number or tour name"
                    >

                </div>


                <div class="tour-search-field">

                    <label for="status">
                        Booking status
                    </label>

                    <select
                        id="status"
                        name="status"
                    >

                        <option value="">
                            All
                        </option>

                        @foreach([
                            'pending_payment',
                            'confirmed',
                            'cancelled',
                            'expired',
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(request('status') === $status)
                            >
                                {{ Str::headline($status) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="tour-search-field">

                    <label for="payment_status">
                        Payment status
                    </label>

                    <select
                        id="payment_status"
                        name="payment_status"
                    >

                        <option value="">
                            All
                        </option>

                        @foreach([
                            'unpaid',
                            'paid',
                            'failed',
                            'refunded',
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(request('payment_status') === $status)
                            >
                                {{ Str::headline($status) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <button
                    type="submit"
                    class="storefront-button"
                >
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'payment_status']))
                    <a
                        href="{{ route('bookings.index') }}"
                        class="my-bookings-explore-btn"
                    >
                        Reset
                    </a>
                @endif

            </form>


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

                                @if($booking->tripCoverImageUrl())

                                    <img
                                        src="{{ $booking->tripCoverImageUrl() }}"
                                        alt="{{ $booking->tripName() }}"
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
                                    {{ $booking->tripName() }}
                                </h2>

                                <div class="my-booking-card__meta">

                                    <span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M7 2v3M17 2v3M3 9h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                                        </svg>

                                        {{ $booking->tripDepartureDate()->format('d M Y') }}
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

                    @if(request()->hasAny(['search', 'status', 'payment_status']))

                        <h2>
                            No bookings matched
                        </h2>

                        <p>
                            Try a different search term, or reset the
                            filters to see all your bookings.
                        </p>

                        <a
                            href="{{ route('bookings.index') }}"
                            class="my-bookings-explore-btn"
                        >
                            Reset filters
                        </a>

                    @else

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

                    @endif

                </div>

            @endif

        </div>

    </section>

</div>

@endsection