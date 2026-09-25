@extends('admin.layouts.app')

@section('title', 'Confirm Booking '.$booking->booking_number)

@section('content')

@php
    $totalAmount = (float) $booking->total_amount;
    $pointsRedeemed = (int) ($booking->points_redeemed ?? 0);
    $pointsDiscount = (float) ($booking->points_discount ?? 0);
    $payableAmount = (float) $booking->payableAmount();
@endphp

<div class="admin-page">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div>
            <div class="admin-breadcrumb">
                <a href="{{ route('admin.bookings.index') }}">Bookings</a>
                <span>/</span>
                <span>{{ $booking->booking_number }}</span>
            </div>

            <span class="admin-eyebrow">CONFIRM &amp; APPLY POINTS</span>

            <h1 class="admin-page__title">
                {{ $booking->booking_number }}
            </h1>

            <p class="admin-page__description">
                {{ $booking->tourPackage->name }}
                ·
                {{ $booking->departure->departure_date->format('d M Y') }}
                for
                {{ $booking->user->name ?? $booking->contact_name }}
            </p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.bookings.index') }}" class="admin-button">
                ← Back to bookings
            </a>
        </div>

    </div>


    @if(session('error'))
        <div class="admin-alert admin-alert--danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alert admin-alert--danger">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="admin-grid admin-grid--main">

        {{-- =================================================
             AMOUNT SUMMARY
        ================================================== --}}

        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">PAYMENT</span>
                    <h2>Amount summary</h2>
                </div>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Traveller count</span>
                    <strong>{{ $booking->traveller_count }}</strong>
                </div>

                <div>
                    <span>Subtotal</span>
                    <strong>{{ $booking->currency }} {{ number_format((float) $booking->subtotal, 2) }}</strong>
                </div>

                <div>
                    <span>Taxes</span>
                    <strong>{{ $booking->currency }} {{ number_format((float) $booking->tax_amount, 2) }}</strong>
                </div>

                <div>
                    <span>Booking total</span>
                    <strong>{{ $booking->currency }} {{ number_format($totalAmount, 2) }}</strong>
                </div>

                @if($pointsRedeemed > 0)
                    <div>
                        <span>Points discount ({{ number_format($pointsRedeemed) }} pts)</span>
                        <strong>&minus;{{ $booking->currency }} {{ number_format($pointsDiscount, 2) }}</strong>
                    </div>
                @endif

                <div class="admin-detail-list__highlight">
                    <span>Payable amount</span>
                    <strong>{{ $booking->currency }} {{ number_format($payableAmount, 2) }}</strong>
                </div>

            </div>

        </section>


        {{-- =================================================
             TRAVEL POINTS
        ================================================== --}}

        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">TRAVEL POINTS</span>
                    <h2>{{ $booking->user->name ?? 'Customer' }}'s points</h2>
                    <p>
                        Available balance:
                        <strong>{{ number_format($availablePoints) }}</strong>
                        points
                    </p>
                </div>
            </div>

            @if(! $redemption['enabled'])

                <p class="admin-muted">
                    Points redemption is currently disabled in
                    <a href="{{ route('admin.point-settings.index') }}">Points Management</a>.
                </p>

            @elseif($availablePoints <= 0)

                <p class="admin-muted">
                    This customer doesn't have any points to redeem yet.
                </p>

            @elseif($pointsRedeemed > 0)

                <div class="admin-detail-list">
                    <div>
                        <span>Applied points</span>
                        <strong>{{ number_format($pointsRedeemed) }}</strong>
                    </div>
                    <div>
                        <span>Discount</span>
                        <strong>{{ $booking->currency }} {{ number_format($pointsDiscount, 2) }}</strong>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.bookings.checkout.points.remove', $booking) }}"
                    style="margin-top: 14px;"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-button">
                        Remove applied points
                    </button>
                </form>

            @else

                <form
                    method="POST"
                    action="{{ route('admin.bookings.checkout.points.apply', $booking) }}"
                    class="admin-form-grid"
                >
                    @csrf

                    <div class="admin-form-group admin-form-group--full">
                        <label for="points">Points to redeem</label>
                        <input
                            id="points"
                            type="number"
                            name="points"
                            min="1"
                            max="{{ $redemption['points_to_redeem'] }}"
                            value="{{ old('points', $redemption['points_to_redeem']) }}"
                            required
                        >
                        <small>
                            Up to {{ number_format($redemption['points_to_redeem']) }} points
                            can be redeemed on this booking
                            (max discount {{ $booking->currency }} {{ number_format($redemption['max_discount'], 2) }}).
                        </small>
                        @error('points')
                            <small class="admin-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="admin-form-group admin-form-group--full">
                        <button type="submit" class="admin-button admin-button--dark">
                            Apply points discount
                        </button>
                    </div>

                </form>

            @endif

        </section>

    </div>


    {{-- =================================================
         CONFIRM PAYMENT
    ================================================== --}}

    <section class="admin-card">

        <div class="admin-card__header">
            <div>
                <span class="admin-eyebrow">FINALIZE</span>
                <h2>Confirm booking</h2>
                <p>
                    Mark this booking as paid once payment has been
                    collected from the customer (cash, bank transfer,
                    or another offline method).
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
            @csrf
            <button type="submit" class="admin-button admin-button--dark">
                Confirm &amp; mark as paid — {{ $booking->currency }} {{ number_format($payableAmount, 2) }}
            </button>
        </form>

    </section>

</div>

@endsection
