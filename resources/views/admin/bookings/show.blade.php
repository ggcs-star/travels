@extends('admin.layouts.app')

@section('title', 'Booking '.$booking->booking_number)

@section('content')

<div class="admin-page booking-detail-page">

    {{-- =========================================================
         BREADCRUMB
         ========================================================= --}}

    <div class="bd-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <span>›</span>
        <a href="{{ route('admin.bookings.index') }}">Bookings</a>
        <span>›</span>
        <strong>{{ $booking->booking_number }}</strong>
    </div>


    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section class="bd-hero">

        <svg class="bd-hero__plane" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2.5 1.5V22l4-1 4 1v-1.5L13 19v-5.5l8 2.5Z"/></svg>

        <div class="bd-hero__top">

            <div>
                <span class="bd-hero__eyebrow">Booking Details</span>
                <h1>{{ $booking->booking_number }}</h1>
                <p>
                    {{ $booking->tourPackage->name }}
                    <span>•</span>
                    {{ $booking->departure->departure_date->format('d M Y') }}
                    –
                    {{ $booking->departure->return_date->format('d M Y') }}
                </p>
            </div>

            <a href="{{ route('admin.bookings.index') }}" class="bd-hero__back">
                ← Back to bookings
            </a>

        </div>

    </section>


    {{-- =========================================================
         STAT CARDS
         ========================================================= --}}

    <div class="bd-stats">

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </span>
            <div>
                <small>Booking Status</small>
                <strong>{{ Str::headline($booking->status) }}</strong>
                <span class="booking-pill booking-pill--status-{{ $booking->status }} bd-stat__pill">
                    {{ Str::headline($booking->status) }}
                </span>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            </span>
            <div>
                <small>Payment Status</small>
                <strong>{{ Str::headline($booking->payment_status) }}</strong>
                <span class="booking-pill booking-pill--payment-{{ $booking->payment_status }} bd-stat__pill">
                    {{ Str::headline($booking->payment_status) }}
                </span>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div>
                <small>Travellers</small>
                <strong>{{ $booking->traveller_count }}</strong>
                <span class="bd-stat__sub">{{ Str::plural('Traveller', $booking->traveller_count) }}</span>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/><path d="M12 3v18"/></svg>
            </span>
            <div>
                <small>Payable Amount</small>
                <strong>{{ $booking->currency }} {{ number_format((float) $booking->payableAmount(), 2) }}</strong>
                @if((float) $booking->points_discount > 0)
                    <span class="bd-stat__sub">{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }} before points discount</span>
                @else
                    <span class="bd-stat__sub">No points discount applied</span>
                @endif
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
            </span>
            <div>
                <small>Points Redeemed</small>
                <strong>{{ number_format((int) $booking->points_redeemed) }} pts</strong>
                <span class="bd-stat__sub">
                    {{ (int) $booking->points_redeemed > 0 ? 'Discount applied' : 'No points used' }}
                </span>
            </div>
        </div>

    </div>


    {{-- =========================================================
         ROW 1: TOUR & DEPARTURE / AMOUNT SUMMARY
         ========================================================= --}}

    <div class="bd-grid">

            {{-- TOUR & DEPARTURE --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                    <h2>Tour &amp; Departure</h2>
                </div>

                <div class="bd-tour">

                    <div
                        class="bd-tour__image"
                        style="background-image:url('{{ $booking->tourPackage->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}')"
                    ></div>

                    <div class="bd-tour__info">

                        <span class="admin-eyebrow">TOUR PACKAGE</span>
                        <h3>{{ $booking->tourPackage->name }}</h3>

                        <div class="bd-tour__facts">

                            <div>
                                <span>Departure date</span>
                                <strong>{{ $booking->departure->departure_date->format('D, d M Y') }}</strong>
                            </div>

                            <div>
                                <span>Return date</span>
                                <strong>{{ $booking->departure->return_date->format('D, d M Y') }}</strong>
                            </div>

                            <div>
                                <span>Travellers</span>
                                <strong>{{ $booking->traveller_count }}</strong>
                            </div>

                            @if(isset($booking->departure->capacity))
                                <div>
                                    <span>Departure capacity</span>
                                    <strong>{{ $booking->departure->capacity }}</strong>
                                </div>
                            @endif

                            @if(isset($availableSeats))
                                <div>
                                    <span>Current available seats</span>
                                    <strong>{{ $availableSeats }}</strong>
                                </div>
                            @endif

                        </div>

                        <a href="{{ route('admin.tours.show', $booking->tourPackage) }}" class="bd-tour__link">
                            View Package →
                        </a>

                    </div>

                </div>

            </section>


            {{-- AMOUNT SUMMARY --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    <h2>Amount Summary</h2>
                </div>

                <div class="admin-detail-list">

                    <div>
                        <span>Subtotal</span>
                        <strong>{{ $booking->currency }} {{ number_format((float) $booking->subtotal, 2) }}</strong>
                    </div>

                    <div>
                        <span>Taxes</span>
                        <strong>{{ $booking->currency }} {{ number_format((float) $booking->tax_amount, 2) }}</strong>
                    </div>

                    <div class="bd-total-row">
                        <span>Total Amount</span>
                        <strong>{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}</strong>
                    </div>

                </div>

                @if((int) $booking->points_redeemed > 0)

                    <div class="bd-points-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <div>
                            <strong>Points redeemed</strong>
                            <span>{{ number_format((int) $booking->points_redeemed) }} pts</span>
                        </div>
                        <small>Discount of {{ $booking->currency }} {{ number_format((float) $booking->points_discount, 2) }} applied on this booking</small>
                    </div>

                @endif


                <div class="bd-payable">
                    <span>Payable Amount</span>
                    <strong>{{ $booking->currency }} {{ number_format((float) $booking->payableAmount(), 2) }}</strong>
                </div>

                <div class="admin-detail-list">
                    <div>
                        <span>Paid at</span>
                        <strong>{{ $booking->paid_at?->format('d M Y, h:i A') ?: '—' }}</strong>
                    </div>
                </div>

            </section>

    </div>


    {{-- =========================================================
         ROW 2: TRAVELLER INFORMATION / CONTACT INFORMATION
         ========================================================= --}}

    <div class="bd-grid">

            {{-- TRAVELLER INFORMATION --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h2>Traveller Information</h2>
                    <span class="bd-card__count">{{ $booking->travellers->count() }} {{ Str::plural('traveller', $booking->travellers->count()) }}</span>
                </div>

                @forelse($booking->travellers as $index => $traveller)

                    <article class="bd-traveller">

                        <div class="bd-traveller__header">

                            <span class="bd-traveller__avatar">
                                {{ strtoupper(substr($traveller->full_name ?: 'T', 0, 1)) }}
                            </span>

                            <div>
                                <strong>{{ $traveller->full_name }}</strong>
                                @if($index === 0)
                                    <span class="bd-badge bd-badge--primary">Primary Traveller</span>
                                @endif
                            </div>

                        </div>

                        <div class="bd-traveller__body">

                            <div class="bd-traveller__personal">

                                <span class="admin-eyebrow">Personal Details</span>

                                <div class="admin-detail-list">

                                    <div>
                                        <span>Full name</span>
                                        <strong>{{ $traveller->full_name }}</strong>
                                    </div>

                                    <div>
                                        <span>Email</span>
                                        <strong>{{ $traveller->email ?: '—' }}</strong>
                                    </div>

                                    <div>
                                        <span>Phone</span>
                                        <strong>{{ $traveller->phone ?: '—' }}</strong>
                                    </div>

                                    <div>
                                        <span>Date of birth</span>
                                        <strong>{{ $traveller->date_of_birth?->format('d M Y') ?: '—' }}</strong>
                                    </div>

                                    <div>
                                        <span>Gender</span>
                                        <strong>{{ $traveller->gender ? Str::headline($traveller->gender) : '—' }}</strong>
                                    </div>

                                </div>

                            </div>

                            <div class="bd-traveller__identity">

                                <div class="bd-traveller__identity-heading">
                                    <span class="admin-eyebrow">Identity Verification</span>

                                    @if($traveller->id_proof_document)
                                        <span class="bd-badge bd-badge--success">✓ Uploaded</span>
                                    @else
                                        <span class="bd-badge bd-badge--danger">Missing</span>
                                    @endif
                                </div>

                                <div class="admin-detail-list">

                                    <div>
                                        <span>Document type</span>
                                        <strong>{{ $traveller->id_proof_type ? Str::headline($traveller->id_proof_type) : '—' }}</strong>
                                    </div>

                                    <div>
                                        <span>Document number</span>
                                        <strong>{{ $traveller->id_proof_number ?: '—' }}</strong>
                                    </div>

                                </div>

                                @if($traveller->id_proof_document)
                                    <a
                                        href="{{ route('admin.bookings.travellers.id-proof', ['booking' => $booking, 'traveller' => $traveller]) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="bd-tour__link bd-tour__link--small"
                                    >
                                        View ID proof →
                                    </a>
                                @endif

                            </div>

                        </div>

                    </article>

                @empty

                    <p class="admin-muted">No travellers found for this booking.</p>

                @endforelse

            </section>


            {{-- CONTACT INFORMATION --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                    <h2>Contact Information</h2>
                </div>

                <div class="admin-detail-list">

                    <div>
                        <span>Full name</span>
                        <strong>{{ $booking->contact_name }}</strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong>{{ $booking->contact_email }}</strong>
                    </div>

                    <div>
                        <span>Phone</span>
                        <strong>{{ $booking->contact_phone }}</strong>
                    </div>

                    <div>
                        <span>Country</span>
                        <strong>{{ $booking->country ?: '—' }}</strong>
                    </div>

                    <div>
                        <span>Account</span>
                        <strong>{{ $booking->user?->username ?: 'Guest' }}</strong>
                    </div>

                </div>

            </section>

    </div>


    {{-- =========================================================
         ROW 3: PAYMENT HISTORY / BOOKING TIMELINE
         ========================================================= --}}

    <div class="bd-grid">

            {{-- PAYMENT HISTORY --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    <h2>Payment History</h2>
                </div>

                @if($booking->payments->isNotEmpty())

                    <div class="admin-table-wrapper">

                        <table class="admin-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Payment Mode</th>
                                    <th>Order ID</th>
                                    <th>Payment ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($booking->payments as $i => $payment)

                                    <tr>
                                        <td>{{ $i + 1 }}</td>

                                        <td>
                                            {{ $payment->paid_at?->format('d M Y') ?: $payment->created_at->format('d M Y') }}
                                        </td>

                                        <td>
                                            {{ $payment->provider === 'razorpay' ? 'Online (Razorpay)' : Str::headline($payment->provider) }}
                                        </td>

                                        <td class="bd-payment-id">
                                            {{ $payment->provider_order_id ?: '—' }}
                                        </td>

                                        <td class="bd-payment-id">
                                            {{ $payment->provider_payment_id ?: '—' }}
                                        </td>

                                        <td>
                                            {{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}
                                        </td>

                                        <td>
                                            <span class="booking-pill booking-pill--payment-{{ $payment->status }}">
                                                {{ Str::headline($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <p class="admin-muted">No payment transaction has been recorded for this booking.</p>

                @endif

            </section>


            {{-- TIMELINE --}}
            <section class="bd-card">

                <div class="bd-card__header bd-card__header--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
                    <h2>Booking Timeline</h2>
                </div>

                <div class="booking-timeline">

                    <div class="booking-timeline__item">
                        <span class="booking-timeline__dot"></span>
                        <div>
                            <strong>Booking created</strong>
                            <small>{{ $booking->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                    </div>

                    @if($booking->paid_at)
                        <div class="booking-timeline__item">
                            <span class="booking-timeline__dot"></span>
                            <div>
                                <strong>Payment completed</strong>
                                <small>{{ $booking->paid_at->format('d M Y, h:i A') }}</small>
                            </div>
                        </div>
                    @endif

                    @if($booking->status === 'confirmed')
                        <div class="booking-timeline__item">
                            <span class="booking-timeline__dot"></span>
                            <div>
                                <strong>Booking confirmed</strong>
                                <small>Payment successfully verified.</small>
                            </div>
                        </div>
                    @endif

                </div>

            </section>

    </div>


    {{-- =========================================================
         SPECIAL REQUESTS (full width)
         ========================================================= --}}

    @if($booking->special_requests)

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>
                <h2>Special Requests</h2>
            </div>

            <p class="bd-notes">{{ $booking->special_requests }}</p>

        </section>

    @endif

</div>


<style>

.booking-detail-page {
    max-width: 1360px;
    margin: 0 auto;
}


/* Breadcrumb */

.bd-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    color: #9aa2ae;
    font-size: 13px;
}

.bd-breadcrumb a {
    color: #7d8796;
    text-decoration: none;
}

.bd-breadcrumb a:hover {
    color: var(--admin-primary, #d97706);
}

.bd-breadcrumb strong {
    color: #344054;
}


/* Hero */

.bd-hero {
    position: relative;
    overflow: hidden;
    padding: 26px 28px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: linear-gradient(120deg, #b45309 0%, #d97706 45%, #ea9a3e 100%);
    color: #fff;
}

.bd-hero__plane {
    position: absolute;
    top: 50%;
    right: 26px;
    width: 130px;
    height: 130px;
    transform: translateY(-50%) rotate(20deg);
    color: rgba(255, 255, 255, .16);
}

.bd-hero__top {
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.bd-hero__eyebrow {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, .85);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.bd-hero__top h1 {
    margin: 0;
    color: #fff;
    font-size: 28px;
    font-weight: 800;
}

.bd-hero__top p {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, .9);
    font-size: 14px;
}

.bd-hero__top p span {
    margin: 0 6px;
    color: rgba(255, 255, 255, .55);
}

.bd-hero__back {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .18);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 650;
    white-space: nowrap;
}

.bd-hero__back:hover {
    background: rgba(255, 255, 255, .3);
}


/* Stats */

.bd-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
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
    font-size: 16px;
    font-weight: 750;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bd-stat__pill {
    margin-top: 6px;
    padding: 3px 10px;
    font-size: 11px;
}

.bd-stat__sub {
    display: block;
    margin-top: 5px;
    color: #9aa2ae;
    font-size: 11px;
    line-height: 1.4;
}


/* Layout */

.bd-grid {
    display: grid;
    grid-template-columns: 1.7fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    display: flex;
    flex-direction: column;
}

.bd-grid > .bd-card {
    min-width: 0;
}

.bd-card {
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
    padding: 20px;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    margin-bottom: 0;
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

.bd-card__count {
    margin-left: auto;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #586273;
    font-size: 11px;
    font-weight: 650;
}


/* Tour & Departure */

.bd-tour {
    display: flex;
    gap: 18px;
}

.bd-tour__image {
    width: 190px;
    flex: 0 0 190px;
    min-height: 150px;
    border-radius: 12px;
    background-size: cover;
    background-position: center;
}

.bd-tour__info {
    flex: 1;
    min-width: 0;
}

.bd-tour__info h3 {
    margin: 4px 0 14px;
    color: #202b3e;
    font-size: 17px;
    font-weight: 750;
}

.bd-tour__facts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px 20px;
    margin-bottom: 16px;
}

.bd-tour__facts > div {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.bd-tour__facts span {
    color: #8c95a2;
    font-size: 12px;
}

.bd-tour__facts strong {
    color: #344054;
    font-size: 14px;
    font-weight: 650;
}

.bd-tour__link {
    display: inline-flex;
    align-items: center;
    padding: 9px 16px;
    border-radius: 8px;
    background: var(--admin-primary, #d97706);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 650;
}

.bd-tour__link:hover {
    background: var(--admin-primary-dark, #b45309);
}

.bd-tour__link--small {
    margin-top: 12px;
    padding: 7px 13px;
    font-size: 12px;
}


/* Traveller */

.bd-traveller {
    padding: 18px 0;
    border-top: 1px solid #eef0f2;
}

.bd-traveller:first-of-type {
    padding-top: 0;
    border-top: 0;
}

.bd-traveller__header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.bd-traveller__avatar {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #202b3e;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
}

.bd-traveller__header strong {
    display: block;
    color: #202b3e;
    font-size: 15px;
    font-weight: 750;
}

.bd-badge {
    display: inline-flex;
    align-items: center;
    margin-top: 3px;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
}

.bd-badge--primary {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary-dark, #b45309);
}

.bd-badge--success {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.bd-badge--danger {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.bd-traveller__body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.bd-traveller__identity-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}


/* Payment History */

.bd-payment-id {
    color: #697384;
    font-family: ui-monospace, "SF Mono", Consolas, monospace;
    font-size: 12px;
    white-space: nowrap;
}


/* Notes */

.bd-notes {
    margin: 0;
    padding: 14px 16px;
    border-radius: 10px;
    background: #fafbfc;
    color: #4b5666;
    font-size: 13.5px;
    line-height: 1.6;
}


/* Amount Summary */

.bd-total-row {
    padding: 12px 14px !important;
    margin: 4px -14px 0;
    border-radius: 9px;
    background: var(--admin-primary-soft, #fff7ed);
}

.bd-total-row span {
    color: var(--admin-primary-dark, #b45309) !important;
    font-weight: 700 !important;
}

.bd-total-row strong {
    color: var(--admin-primary-dark, #b45309) !important;
}

.bd-points-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 16px 0;
    padding: 14px;
    border-radius: 10px;
    background: var(--admin-success-bg, #ecfdf3);
}

.bd-points-box svg {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
    margin-top: 2px;
    color: var(--admin-success, #15803d);
}

.bd-points-box strong {
    color: var(--admin-success, #15803d);
    font-size: 13px;
}

.bd-points-box span {
    margin-left: 6px;
    color: var(--admin-success, #15803d);
    font-weight: 700;
    font-size: 13px;
}

.bd-points-box small {
    display: block;
    width: 100%;
    margin-top: 4px;
    color: #2f7a53;
    font-size: 11.5px;
    line-height: 1.5;
}

.bd-payable {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px;
    margin-bottom: 16px;
    border-radius: 10px;
    background: #202b3e;
}

.bd-payable span {
    color: #b8c0cd;
    font-size: 12px;
    font-weight: 650;
}

.bd-payable strong {
    color: #fff;
    font-size: 18px;
    font-weight: 800;
}


/* Responsive */

@media (max-width: 1150px) {

    .bd-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .bd-grid {
        grid-template-columns: 1fr;
    }

    .bd-grid > .bd-card {
        margin-bottom: 18px;
    }

    .bd-grid > .bd-card:last-child {
        margin-bottom: 0;
    }

}

@media (max-width: 760px) {

    .bd-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .bd-hero__top {
        flex-direction: column;
    }

    .bd-tour {
        flex-direction: column;
    }

    .bd-tour__image {
        width: 100%;
        flex: none;
    }

    .bd-traveller__body {
        grid-template-columns: 1fr;
    }

}


/* Pills (shared with index/status dropdown color scheme) */

.booking-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #4b5666;
    font-size: 13px;
    font-weight: 700;
}

.booking-pill > span:first-child:empty {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}

.booking-pill--status-confirmed,
.booking-pill--payment-paid {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.booking-pill--status-cancelled,
.booking-pill--payment-failed {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.booking-pill--status-expired,
.booking-pill--payment-refunded {
    background: #fffbeb;
    color: #b45309;
}

.booking-pill--status-pending_payment,
.booking-pill--payment-unpaid,
.booking-pill--payment-created {
    background: #f1f3f5;
    color: #4b5666;
}

</style>

@endsection
