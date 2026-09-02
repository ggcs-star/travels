@extends('layouts.app')

@section('title', 'My bookings | '.config('travels.brand.name'))

@section('content')
    <section class="storefront-section">
        <div class="container">
            <span class="storefront-kicker">Your account</span>
            <div class="storefront-heading-row">
                <div><h1>My bookings</h1><p class="storefront-intro">Track payment and travel details in one place.</p></div>
                <a href="{{ route('tours.index') }}" class="storefront-button storefront-button--secondary">Explore tours</a>
            </div>

            @if(session('success'))<div class="storefront-alert storefront-alert--success">{{ session('success') }}</div>@endif

            @forelse($bookings as $booking)
                <article class="booking-list-card">
                    <div>
                        <span class="storefront-kicker">{{ $booking->booking_number }}</span>
                        <h2>{{ $booking->tourPackage->name }}</h2>
                        <p>{{ $booking->departure->departure_date->format('d M Y') }} · {{ $booking->traveller_count }} {{ Str::plural('traveller', $booking->traveller_count) }}</p>
                    </div>
                    <div class="booking-list-card__right">
                        <span class="booking-status booking-status--{{ $booking->status }}">{{ Str::headline($booking->status) }}</span>
                        <strong>₹{{ number_format((float) $booking->total_amount, 0) }}</strong>
                        <a href="{{ route('bookings.show', $booking) }}" class="storefront-link">View booking →</a>
                    </div>
                </article>
            @empty
                <div class="storefront-empty">
                    <h2>You have no bookings yet.</h2>
                    <p>Your upcoming journeys will appear here after you reserve a departure.</p>
                    <a href="{{ route('tours.index') }}" class="storefront-button">Explore tours</a>
                </div>
            @endforelse

            <div class="storefront-pagination">{{ $bookings->links() }}</div>
        </div>
    </section>
@endsection
