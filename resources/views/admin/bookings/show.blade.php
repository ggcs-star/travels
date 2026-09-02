@extends('admin.layouts.app')

@section('title', 'Booking '.$booking->booking_number)

@section('content')

<div class="admin-page admin-booking-detail-page">

    {{-- =========================================================
         HEADER
         ========================================================= --}}

    <div class="admin-page__header">

        <div>

            <div class="admin-breadcrumb">
                <a href="{{ route('admin.bookings.index') }}">
                    Bookings
                </a>

                <span>/</span>

                <span>
                    {{ $booking->booking_number }}
                </span>
            </div>

            <span class="admin-eyebrow">
                BOOKING DETAILS
            </span>

            <h1 class="admin-page__title">
                {{ $booking->booking_number }}
            </h1>

            <p class="admin-page__description">
                {{ $booking->tourPackage->name }}
                ·
                {{ $booking->departure->departure_date->format('d M Y') }}
                –
                {{ $booking->departure->return_date->format('d M Y') }}
            </p>

        </div>


        <div class="admin-header-actions">

            <a
                href="{{ route('admin.bookings.index') }}"
                class="admin-button"
            >
                ← Back to bookings
            </a>

        </div>

    </div>


    {{-- =========================================================
         TOP SUMMARY
         ========================================================= --}}

    <section class="admin-card booking-detail-summary">

        <div class="booking-detail-summary__item">

            <span class="admin-eyebrow">
                BOOKING STATUS
            </span>

            <span
                class="booking-pill booking-pill--status-{{ $booking->status }}"
            >
                {{ Str::headline($booking->status) }}
            </span>

        </div>


        <div class="booking-detail-summary__item">

            <span class="admin-eyebrow">
                PAYMENT
            </span>

            <span
                class="booking-pill booking-pill--payment-{{ $booking->payment_status }}"
            >
                <span></span>
                {{ Str::headline($booking->payment_status) }}
            </span>

        </div>


        <div class="booking-detail-summary__item">

            <span class="admin-eyebrow">
                TRAVELLERS
            </span>

            <strong>
                {{ $booking->traveller_count }}
            </strong>

        </div>


        <div class="booking-detail-summary__item">

            <span class="admin-eyebrow">
                TOTAL AMOUNT
            </span>

            <strong class="booking-detail-summary__amount">
                {{ $booking->currency }}
                {{ number_format((float) $booking->total_amount, 2) }}
            </strong>

        </div>

    </section>


    {{-- =========================================================
         TWO COLUMN INFORMATION
         ========================================================= --}}

    <div class="admin-grid admin-grid--main">

        {{-- CUSTOMER --}}
        <section class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        CUSTOMER
                    </span>

                    <h2>
                        Contact information
                    </h2>

                </div>

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
                    <strong>
                        {{ $booking->user?->username ?: 'Guest' }}
                    </strong>
                </div>

            </div>

        </section>


        {{-- PAYMENT SUMMARY --}}
        <section class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        PAYMENT
                    </span>

                    <h2>
                        Amount summary
                    </h2>

                </div>

            </div>


            <div class="admin-detail-list">

                <div>
                    <span>Subtotal</span>

                    <strong>
                        {{ $booking->currency }}
                        {{ number_format(
                            (float) $booking->subtotal,
                            2
                        ) }}
                    </strong>
                </div>

                <div>
                    <span>Taxes</span>

                    <strong>
                        {{ $booking->currency }}
                        {{ number_format(
                            (float) $booking->tax_amount,
                            2
                        ) }}
                    </strong>
                </div>

                <div class="admin-detail-list__highlight">
                    <span>Total</span>

                    <strong>
                        {{ $booking->currency }}
                        {{ number_format(
                            (float) $booking->total_amount,
                            2
                        ) }}
                    </strong>
                </div>

                <div>
                    <span>Paid at</span>

                    <strong>
                        {{ $booking->paid_at?->format(
                            'd M Y, h:i A'
                        ) ?: '—' }}
                    </strong>
                </div>

            </div>

        </section>

    </div>


    {{-- =========================================================
         TOUR DETAILS
         ========================================================= --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    TRAVEL
                </span>

                <h2>
                    Tour & departure
                </h2>

            </div>

        </div>


        <div class="booking-travel-grid">

            <div class="booking-travel-item">

                <span>
                    Tour package
                </span>

                <strong>
                    {{ $booking->tourPackage->name }}
                </strong>

            </div>


            <div class="booking-travel-item">

                <span>
                    Departure date
                </span>

                <strong>
                    {{ $booking->departure->departure_date
                        ->format('D, d M Y')
                    }}
                </strong>

            </div>


            <div class="booking-travel-item">

                <span>
                    Return date
                </span>

                <strong>
                    {{ $booking->departure->return_date
                        ->format('D, d M Y')
                    }}
                </strong>

            </div>


            <div class="booking-travel-item">

                <span>
                    Travellers
                </span>

                <strong>
                    {{ $booking->traveller_count }}
                </strong>

            </div>


            @if(isset($booking->departure->capacity))

                <div class="booking-travel-item">

                    <span>
                        Departure capacity
                    </span>

                    <strong>
                        {{ $booking->departure->capacity }}
                    </strong>

                </div>

            @endif


            @if(isset($availableSeats))

                <div class="booking-travel-item">

                    <span>
                        Current available seats
                    </span>

                    <strong>
                        {{ $availableSeats }}
                    </strong>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         TRAVELLERS
         ========================================================= --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    PASSENGERS
                </span>

                <h2>
                    Traveller information
                </h2>

                <p>
                    Individual details and identity documents for every traveller.
                </p>

            </div>


            <span class="booking-result-count">
                {{ $booking->travellers->count() }}
                {{ Str::plural(
                    'traveller',
                    $booking->travellers->count()
                ) }}
            </span>

        </div>


        <div class="admin-traveller-list">

            @forelse($booking->travellers as $index => $traveller)

                <article class="admin-traveller-card">

                    {{-- HEADER --}}

                    <div class="admin-traveller-card__header">

                        <div>

                            <span class="admin-eyebrow">
                                TRAVELLER {{ $index + 1 }}
                            </span>

                            <h3>
                                {{ $traveller->full_name }}
                            </h3>

                        </div>

                        @if($traveller->id_proof_document)

                            <span class="booking-document-status">
                                ID uploaded
                            </span>

                        @else

                            <span class="booking-document-status booking-document-status--missing">
                                ID missing
                            </span>

                        @endif

                    </div>


                    {{-- PERSONAL DETAILS --}}

                    <div class="booking-traveller-details">

                        <div>

                            <span>
                                Full name
                            </span>

                            <strong>
                                {{ $traveller->full_name }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $traveller->email ?: '—' }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Phone
                            </span>

                            <strong>
                                {{ $traveller->phone ?: '—' }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Date of birth
                            </span>

                            <strong>
                                {{ $traveller->date_of_birth?->format(
                                    'd M Y'
                                ) ?: '—' }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Gender
                            </span>

                            <strong>
                                {{ $traveller->gender
                                    ? Str::headline(
                                        $traveller->gender
                                    )
                                    : '—'
                                }}
                            </strong>

                        </div>

                    </div>


                    {{-- ID PROOF --}}

                    <div class="booking-id-proof">

                        <div class="booking-id-proof__heading">

                            <div>

                                <span class="admin-eyebrow">
                                    IDENTITY VERIFICATION
                                </span>

                                <h4>
                                    Government ID proof
                                </h4>

                            </div>

                        </div>


                        @if(
                            $traveller->id_proof_type ||
                            $traveller->id_proof_number
                        )

                            <div class="booking-id-proof__details">

                                <div>

                                    <span>
                                        Document type
                                    </span>

                                    <strong>
                                        {{ $traveller->id_proof_type
                                            ? Str::headline(
                                                $traveller->id_proof_type
                                            )
                                            : '—'
                                        }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Document number
                                    </span>

                                    <strong>
                                        {{ $traveller->id_proof_number ?: '—' }}
                                    </strong>

                                </div>

                            </div>

                        @else

                            <p class="admin-muted">
                                No ID proof information has been provided.
                            </p>

                        @endif


                        @if($traveller->id_proof_document)

                            <div class="booking-id-proof__footer">

                                <span>
                                    Identity document uploaded securely.
                                </span>

                                <a
                                    href="{{ route(
                                        'admin.bookings.travellers.id-proof',
                                        [
                                            'booking' => $booking,
                                            'traveller' => $traveller,
                                        ]
                                    ) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="admin-button admin-button--dark"
                                >
                                    View ID proof →
                                </a>

                            </div>

                        @else

                            <div class="booking-id-proof__footer">

                                <span class="admin-muted">
                                    No document uploaded.
                                </span>

                            </div>

                        @endif

                    </div>

                </article>

            @empty

                <div class="booking-empty">

                    <h3>
                        No travellers found
                    </h3>

                    <p>
                        This booking does not contain traveller records.
                    </p>

                </div>

            @endforelse

        </div>

    </section>


    {{-- =========================================================
         PAYMENT HISTORY
         ========================================================= --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    PAYMENT HISTORY
                </span>

                <h2>
                    Transactions
                </h2>

                <p>
                    Razorpay and payment activity related to this booking.
                </p>

            </div>

        </div>


        @if($booking->payments->isNotEmpty())

            <div class="booking-payment-list">

                @foreach($booking->payments as $payment)

                    <article class="booking-payment-item">

                        <div class="booking-payment-item__main">

                            <div>

                                <span class="admin-eyebrow">
                                    {{ Str::headline(
                                        $payment->provider
                                    ) }}
                                </span>

                                <h3>
                                    {{ $payment->provider_payment_id
                                        ?: 'Payment attempt'
                                    }}
                                </h3>

                            </div>


                            <span
                                class="booking-pill
                                booking-pill--payment-{{ $payment->status }}"
                            >
                                <span></span>
                                {{ Str::headline($payment->status) }}
                            </span>

                        </div>


                        <div class="booking-payment-details">

                            <div>

                                <span>
                                    Order ID
                                </span>

                                <strong>
                                    {{ $payment->provider_order_id ?: '—' }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Payment ID
                                </span>

                                <strong>
                                    {{ $payment->provider_payment_id ?: '—' }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Amount
                                </span>

                                <strong>
                                    {{ $payment->currency }}
                                    {{ number_format(
                                        (float) $payment->amount,
                                        2
                                    ) }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Paid at
                                </span>

                                <strong>
                                    {{ $payment->paid_at?->format(
                                        'd M Y, h:i A'
                                    ) ?: '—' }}
                                </strong>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="booking-empty">

                <h3>
                    No payment attempts
                </h3>

                <p>
                    No payment transaction has been recorded for this booking.
                </p>

            </div>

        @endif

    </section>


    {{-- =========================================================
         SPECIAL REQUESTS
         ========================================================= --}}

    @if($booking->special_requests)

        <section class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        CUSTOMER REQUEST
                    </span>

                    <h2>
                        Special requests
                    </h2>

                </div>

            </div>


            <div class="booking-notes">
                {{ $booking->special_requests }}
            </div>

        </section>

    @endif


    {{-- =========================================================
         BOOKING TIMELINE
         ========================================================= --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    ACTIVITY
                </span>

                <h2>
                    Booking timeline
                </h2>

            </div>

        </div>


        <div class="booking-timeline">

            <div class="booking-timeline__item">

                <span class="booking-timeline__dot"></span>

                <div>

                    <strong>
                        Booking created
                    </strong>

                    <small>
                        {{ $booking->created_at->format(
                            'd M Y, h:i A'
                        ) }}
                    </small>

                </div>

            </div>


            @if($booking->paid_at)

                <div class="booking-timeline__item">

                    <span class="booking-timeline__dot"></span>

                    <div>

                        <strong>
                            Payment completed
                        </strong>

                        <small>
                            {{ $booking->paid_at->format(
                                'd M Y, h:i A'
                            ) }}
                        </small>

                    </div>

                </div>

            @endif


            @if($booking->status === 'confirmed')

                <div class="booking-timeline__item">

                    <span class="booking-timeline__dot"></span>

                    <div>

                        <strong>
                            Booking confirmed
                        </strong>

                        <small>
                            Payment successfully verified.
                        </small>

                    </div>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection