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
                {{ $booking->tripName() }}
                ·
                {{ $booking->tripDepartureDate()->format('d M Y') }}
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
                <span class="admin-eyebrow">PAYMENT</span>
                <h2>Has payment been collected?</h2>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('admin.bookings.confirm', $booking) }}"
            id="checkout-confirm-form"
        >
            @csrf

            <div class="admin-form-grid">

                <div class="admin-form-group admin-form-group--full">
                    @include('admin.bookings.partials.payment-collected-toggle', [
                        'toggleId' => 'payment_collected',
                        'toggleTitle' => 'Payment already collected from the customer',
                        'toggleHint' => 'Tick this once the customer has paid, then select how below. Leave it unticked to save this booking as unpaid for now — you can confirm payment later from the bookings list.',
                    ])
                </div>

                <div
                    class="admin-form-group admin-form-group--full"
                    data-payment-collected-section
                    hidden
                >
                    @include('admin.bookings.partials.payment-method-fields')
                </div>

            </div>

            <div class="payment-method-actions">
                <a href="{{ route('admin.bookings.show', $booking) }}" class="admin-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Cancel
                </a>

                <button
                    type="submit"
                    class="admin-button admin-button--dark"
                    data-checkout-submit
                    data-label-unpaid="Save as unpaid"
                    data-label-paid="Confirm &amp; mark as paid — {{ $booking->currency }} {{ number_format($payableAmount, 2) }}"
                >
                    Save as unpaid
                </button>
            </div>

        </form>

    </section>

</div>

<style>
    .payment-method-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 20px;
    }

    #checkout-confirm-form [data-checkout-submit].admin-button--primary {
        background: var(--admin-primary);
        box-shadow: 0 6px 16px rgba(217, 119, 6, .22);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('payment_collected');
        const section = document.querySelector('[data-payment-collected-section]');
        const submitButton = document.querySelector('[data-checkout-submit]');

        if (!checkbox || !section || !submitButton) {
            return;
        }

        function updateVisibility() {
            const collected = checkbox.checked;
            section.hidden = !collected;

            section.querySelectorAll('[data-payment-method-radio]').forEach((radio) => {
                radio.disabled = !collected;
            });

            submitButton.classList.toggle('admin-button--dark', !collected);
            submitButton.classList.toggle('admin-button--primary', collected);
            submitButton.innerHTML = collected
                ? submitButton.dataset.labelPaid
                : submitButton.dataset.labelUnpaid;
        }

        checkbox.addEventListener('change', updateVisibility);

        updateVisibility();
    });
</script>

@endsection
