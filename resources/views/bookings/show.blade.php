@extends('layouts.app')

@section('title', 'Booking '.$booking->booking_number.' | '.config('travels.brand.name'))

@section('content')

@php
    $tour = $booking->tourPackage;
    $departure = $booking->departure;

    $tourImage = $tour?->cover_image_url
        ?: asset('images/hero/tour-bg.jpg');

    $currency = $departure?->currency ?: 'INR';

    $currencySymbol = match (strtoupper($currency)) {
        'INR' => '₹',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        default => strtoupper($currency).' ',
    };

    $paymentStatus = strtolower((string) $booking->payment_status);
    $bookingStatus = strtolower((string) $booking->status);

    $statusClass = match ($bookingStatus) {
        'confirmed', 'completed' => 'success',
        'cancelled', 'canceled', 'expired', 'failed' => 'danger',
        'pending', 'payment_pending', 'on_hold' => 'warning',
        default => 'neutral',
    };

    $paymentClass = match ($paymentStatus) {
        'paid', 'success', 'completed' => 'success',
        'failed', 'cancelled', 'canceled' => 'danger',
        'pending', 'created', 'unpaid' => 'warning',
        default => 'neutral',
    };

    $expiresAt = $booking->expires_at;
@endphp

<section class="storefront-section booking-details-page">

    <div class="container">

        {{-- Back --}}
        <div class="booking-details-back">
            <a
                href="{{ route('bookings.index') }}"
                class="storefront-back"
            >
                ← My bookings
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="storefront-alert storefront-alert--success booking-alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="storefront-alert storefront-alert--error booking-alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="booking-details-header">

            <div>

                <span class="storefront-kicker">
                    Booking {{ $booking->booking_number }}
                </span>

                <h1>
                    {{ $tour?->name ?: 'Tour Booking' }}
                </h1>

                @if($departure)
                    <p class="booking-details-subtitle">
                        {{ $departure->departure_date?->format('D, d M Y') }}

                        @if($departure->return_date)
                            <span>—</span>
                            {{ $departure->return_date->format('D, d M Y') }}
                        @endif
                    </p>
                @endif

            </div>

            <div class="booking-status-group">

                <span class="booking-status booking-status--{{ $statusClass }}">
                    {{ \Illuminate\Support\Str::headline($booking->status) }}
                </span>

                <span class="booking-status booking-status--{{ $paymentClass }}">
                    Payment:
                    {{ \Illuminate\Support\Str::headline($booking->payment_status) }}
                </span>

            </div>

        </div>


        {{-- Main layout --}}
        <div class="booking-details-layout">

            {{-- ==========================================================
                 LEFT
            =========================================================== --}}
            <div class="booking-details-main">

                {{-- Tour card --}}
                <section class="booking-info-card booking-tour-card">

                    <div class="booking-tour-card__image">

                        <img
                            src="{{ $tourImage }}"
                            alt="{{ $tour?->name ?: 'Tour' }}"
                        >

                    </div>

                    <div class="booking-tour-card__content">

                        <span class="booking-card-eyebrow">
                            YOUR TRIP
                        </span>

                        <h2>
                            {{ $tour?->name ?: 'Tour Package' }}
                        </h2>

                        @if($tour?->short_description)
                            <p class="booking-tour-description">
                                {{ $tour->short_description }}
                            </p>
                        @endif

                        <div class="booking-tour-meta">

                            @if($tour?->category)
                                <div class="booking-meta-item">
                                    <span>Category</span>
                                    <strong>
                                        {{ $tour->category->name }}
                                    </strong>
                                </div>
                            @endif

                            @if($tour?->destination)
                                <div class="booking-meta-item">
                                    <span>Destination</span>
                                    <strong>
                                        {{ $tour->destination }}
                                    </strong>
                                </div>
                            @endif

                            @if($tour?->starting_city || $tour?->ending_city)
                                <div class="booking-meta-item">
                                    <span>Route</span>
                                    <strong>
                                        {{ $tour?->starting_city ?: '—' }}
                                        →
                                        {{ $tour?->ending_city ?: '—' }}
                                    </strong>
                                </div>
                            @endif

                            @if($tour?->duration_days)
                                <div class="booking-meta-item">
                                    <span>Duration</span>
                                    <strong>
                                        {{ $tour->duration_days }} days
                                        @if($tour->duration_nights !== null)
                                            / {{ $tour->duration_nights }} nights
                                        @endif
                                    </strong>
                                </div>
                            @endif

                        </div>

                    </div>

                </section>


                {{-- ======================================================
                     TRIP DETAILS
                ======================================================= --}}
                <section class="booking-info-card">

                    <div class="booking-card-header">
                        <div>
                            <span class="booking-card-eyebrow">
                                TRIP INFORMATION
                            </span>

                            <h2>
                                Departure details
                            </h2>
                        </div>
                    </div>

                    <div class="booking-detail-grid">

                        <div class="booking-detail-item">
                            <span>Departure</span>

                            <strong>
                                {{ $departure?->departure_date?->format('D, d M Y') ?: '—' }}
                            </strong>
                        </div>

                        <div class="booking-detail-item">
                            <span>Return</span>

                            <strong>
                                {{ $departure?->return_date?->format('D, d M Y') ?: '—' }}
                            </strong>
                        </div>

                        <div class="booking-detail-item">
                            <span>Travellers</span>

                            <strong>
                                {{ $booking->traveller_count }}
                            </strong>
                        </div>

                        @if($departure?->meeting_point)
                            <div class="booking-detail-item">
                                <span>Meeting point</span>

                                <strong>
                                    {{ $departure->meeting_point }}
                                </strong>
                            </div>
                        @endif

                        @if($departure?->capacity)
                            <div class="booking-detail-item">
                                <span>Total capacity</span>

                                <strong>
                                    {{ $departure->capacity }} seats
                                </strong>
                            </div>
                        @endif

                        @if($departure?->status)
                            <div class="booking-detail-item">
                                <span>Departure status</span>

                                <strong>
                                    {{ \Illuminate\Support\Str::headline($departure->status) }}
                                </strong>
                            </div>
                        @endif

                    </div>

                </section>


                {{-- ======================================================
                     TRAVELLERS
                ======================================================= --}}
                <section class="booking-info-card">

                    <div class="booking-card-header">

                        <div>
                            <span class="booking-card-eyebrow">
                                TRAVELLERS
                            </span>

                            <h2>
                                Traveller details
                            </h2>
                        </div>

                        <span class="booking-count-badge">
                            {{ $booking->travellers->count() }}
                            {{ \Illuminate\Support\Str::plural('traveller', $booking->travellers->count()) }}
                        </span>

                    </div>

                    @if($booking->travellers->isNotEmpty())

                        <div class="booking-travellers">

                            @foreach($booking->travellers as $index => $traveller)

                                <div class="booking-traveller">

                                    <div class="booking-traveller-number">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="booking-traveller-content">

                                        <div class="booking-traveller-heading">

                                            <h3>
                                                {{ $traveller->full_name }}
                                            </h3>

                                            <span>
                                                Traveller {{ $index + 1 }}
                                            </span>

                                        </div>

                                        <div class="booking-traveller-details">

                                            @if($traveller->email)
                                                <div>
                                                    <span>Email</span>
                                                    <strong>
                                                        {{ $traveller->email }}
                                                    </strong>
                                                </div>
                                            @endif

                                            @if($traveller->phone)
                                                <div>
                                                    <span>Phone</span>
                                                    <strong>
                                                        {{ $traveller->phone }}
                                                    </strong>
                                                </div>
                                            @endif

                                            @if($traveller->date_of_birth)
                                                <div>
                                                    <span>Date of birth</span>
                                                    <strong>
                                                        {{ \Illuminate\Support\Carbon::parse($traveller->date_of_birth)->format('d M Y') }}
                                                    </strong>
                                                </div>
                                            @endif

                                            @if($traveller->gender)
                                                <div>
                                                    <span>Gender</span>
                                                    <strong>
                                                        {{ \Illuminate\Support\Str::headline($traveller->gender) }}
                                                    </strong>
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="booking-empty-state">
                            No traveller details available.
                        </div>

                    @endif

                </section>


                {{-- ======================================================
                     CONTACT
                ======================================================= --}}
                <section class="booking-info-card">

                    <div class="booking-card-header">

                        <div>
                            <span class="booking-card-eyebrow">
                                CONTACT
                            </span>

                            <h2>
                                Contact information
                            </h2>
                        </div>

                    </div>

                    <div class="booking-contact-grid">

                        <div class="booking-detail-item">
                            <span>Full name</span>
                            <strong>
                                {{ $booking->contact_name ?: '—' }}
                            </strong>
                        </div>

                        <div class="booking-detail-item">
                            <span>Email</span>
                            <strong>
                                {{ $booking->contact_email ?: '—' }}
                            </strong>
                        </div>

                        <div class="booking-detail-item">
                            <span>Phone</span>
                            <strong>
                                {{ $booking->contact_phone ?: '—' }}
                            </strong>
                        </div>

                        @if($booking->country)
                            <div class="booking-detail-item">
                                <span>Country</span>
                                <strong>
                                    {{ $booking->country }}
                                </strong>
                            </div>
                        @endif

                    </div>

                </section>


                {{-- ======================================================
                     SPECIAL REQUESTS
                ======================================================= --}}
                @if($booking->special_requests)

                    <section class="booking-info-card">

                        <div class="booking-card-header">

                            <div>
                                <span class="booking-card-eyebrow">
                                    REQUESTS
                                </span>

                                <h2>
                                    Special requests
                                </h2>
                            </div>

                        </div>

                        <div class="booking-request-box">
                            {{ $booking->special_requests }}
                        </div>

                    </section>

                @endif

            </div>


            {{-- ==========================================================
                 RIGHT SIDEBAR
            =========================================================== --}}
            <aside class="booking-details-sidebar">

                <div class="booking-payment-card">

                    <div class="booking-payment-card__top">

                        <span class="booking-card-eyebrow">
                            PAYMENT
                        </span>

                        <h2>
                            Payment summary
                        </h2>

                    </div>


                    {{-- Amount --}}
                    <div class="booking-payment-lines">

                        <div class="summary-row">
                            <span>
                                Price per traveller
                            </span>

                            <strong>
                                {{ $currencySymbol }}{{ number_format(
                                    $booking->traveller_count > 0
                                        ? ((float) $booking->subtotal / $booking->traveller_count)
                                        : 0,
                                    2
                                ) }}
                            </strong>
                        </div>

                        <div class="summary-row">
                            <span>
                                Travellers
                            </span>

                            <strong>
                                × {{ $booking->traveller_count }}
                            </strong>
                        </div>

                        <div class="summary-row">
                            <span>
                                Subtotal
                            </span>

                            <strong>
                                {{ $currencySymbol }}{{ number_format((float) $booking->subtotal, 2) }}
                            </strong>
                        </div>

                        @if((float) $booking->discount_amount > 0)
                            <div class="summary-row summary-row--discount">
                                <span>
                                    Discount
                                </span>

                                <strong>
                                    -{{ $currencySymbol }}{{ number_format((float) $booking->discount_amount, 2) }}
                                </strong>
                            </div>
                        @endif

                        @if((float) $booking->tax_amount > 0)
                            <div class="summary-row">
                                <span>
                                    Taxes
                                </span>

                                <strong>
                                    {{ $currencySymbol }}{{ number_format((float) $booking->tax_amount, 2) }}
                                </strong>
                            </div>
                        @endif

                    </div>


                    {{-- Total --}}
                    <div class="booking-total-box">

                        <span>
                            Total amount
                        </span>

                        <strong>
                            {{ $currencySymbol }}{{ number_format((float) $booking->total_amount, 2) }}
                        </strong>

                    </div>


                    {{-- Payment action --}}
                    @if($booking->isPayable())

                        <div class="booking-payment-action">

                            @if($expiresAt)

                                <div class="booking-hold-notice">

                                    <span class="booking-hold-icon">
                                        ⏱
                                    </span>

                                    <div>
                                        <strong>
                                            Payment hold active
                                        </strong>

                                        <small>
                                            Complete payment by
                                            {{ $expiresAt->format('d M Y, h:i A') }}
                                        </small>
                                    </div>

                                </div>

                            @endif

                            <a
                                href="{{ route('payments.checkout', $booking) }}"
                                class="storefront-button storefront-button--wide booking-pay-button"
                            >
                                Pay securely
                            </a>

                            <p class="booking-secure-text">
                                🔒 Secure payment powered by your payment gateway.
                            </p>

                        </div>


                        <div class="booking-cancel-area">

                            <form
                                method="POST"
                                action="{{ route('bookings.cancel', $booking) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="booking-cancel-button"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')"
                                >
                                    Cancel this booking
                                </button>
                            </form>

                        </div>

                    @elseif($paymentStatus === 'paid')

                        <div class="booking-payment-success">

                            <div class="booking-payment-success__icon">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Payment completed
                                </strong>

                                <p>
                                    Your booking payment has been received successfully.
                                </p>
                            </div>

                        </div>

                    @elseif($bookingStatus === 'cancelled' || $bookingStatus === 'canceled')

                        <div class="booking-payment-warning">

                            <strong>
                                Booking cancelled
                            </strong>

                            <p>
                                This booking is no longer active.
                            </p>

                        </div>

                    @elseif($bookingStatus === 'expired')

                        <div class="booking-payment-warning">

                            <strong>
                                Booking expired
                            </strong>

                            <p>
                                The payment hold for this booking has expired.
                            </p>

                        </div>

                    @else

                        <div class="booking-payment-warning">

                            <strong>
                                Payment unavailable
                            </strong>

                            <p>
                                This booking is currently not awaiting payment.
                            </p>

                        </div>

                    @endif


                    <div class="booking-help-box">

                        <strong>
                            Need help?
                        </strong>

                        <p>
                            If you have any issue with your booking or payment,
                            contact our support team.
                        </p>

                        <a
                            href="{{ route('contact') }}"
                            class="storefront-link"
                        >
                            Contact us →
                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection