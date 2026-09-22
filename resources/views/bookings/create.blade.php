@extends('layouts.app')

@section('title', 'Book '.$tour->name.' | '.config('travels.brand.name'))

@section('content')

@php

    $travellers = old('travellers');

    if ($travellers === null) {
        $travellers = [[
            'full_name' => auth()->user()->name ?? '',
            'email' => auth()->user()->email ?? '',
            'phone' => '',
            'date_of_birth' => '',
            'gender' => '',
            'id_proof_type' => '',
            'id_proof_number' => '',
            'id_proof_document' => null,
        ]];
    }

    $initialTravellerCount = max(
        1,
        count($travellers)
    );

@endphp


<section
    class="storefront-section booking-page"
    data-booking-form
>

    <div class="container">

        <div class="booking-header">

            <a
                href="{{ route('tours.show', $tour) }}"
                class="storefront-back"
            >
                ← Back to tour
            </a>


            <span class="storefront-kicker">
                Secure your seats
            </span>


            <h1>
                Complete your booking
            </h1>


            <p class="storefront-intro">
                Your seats are held for
                {{ config('travels.booking.payment_hold_minutes', 15) }}
                minutes once you continue to payment.
            </p>


            {{-- Validation summary --}}

            @if($errors->any())

                <div class="storefront-alert storefront-alert--error">

                    <strong>
                        Please correct the highlighted details below.
                    </strong>

                    <ul>
                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach
                    </ul>

                </div>

            @endif

        </div>


        <div class="booking-layout">

        {{-- =========================================================
             MAIN BOOKING FORM
             ========================================================= --}}

        <div>

            {{-- =====================================================
                 BOOKING FORM
                 ===================================================== --}}

            <form
                method="POST"
                action="{{ route('bookings.store', $tour) }}"
                enctype="multipart/form-data"
                class="booking-form"
            >

                @csrf


                {{-- =================================================
                     DEPARTURE
                     ================================================= --}}

                <section class="booking-form__section">

                    <h2>
                        Departure
                    </h2>


                    <label for="departure_id">
                        Select a date
                    </label>


                    <select
                        id="departure_id"
                        name="departure_id"
                        data-departure-select
                        required
                    >

                        @foreach($departures as $departure)

                            <option
                                value="{{ $departure->id }}"
                                data-price="{{ $departure->effective_price }}"
                                data-available-seats="{{ $departure->available_seats }}"
                                @selected(
                                    (int) old(
                                        'departure_id',
                                        $selectedDeparture->id
                                    ) === (int) $departure->id
                                )
                            >

                                {{ $departure->departure_date->format('D, d M Y') }}

                                –

                                {{ $departure->return_date->format('D, d M Y') }}

                                ·

                                ₹{{ number_format(
                                    (float) $departure->effective_price,
                                    0
                                ) }}

                                ·

                                {{ $departure->available_seats }}
                                {{ Str::plural('seat', $departure->available_seats) }}
                                left

                            </option>

                        @endforeach

                    </select>


                    <div
                        class="booking-availability"
                        data-availability-message
                    >
                        {{ $selectedDeparture->available_seats }}
                        {{ Str::plural('seat', $selectedDeparture->available_seats) }}
                        available
                    </div>


                    @error('departure_id')

                        <small class="booking-error">
                            {{ $message }}
                        </small>

                    @enderror

                </section>


                {{-- =================================================
                     CONTACT DETAILS
                     ================================================= --}}

                <section class="booking-form__section booking-contact-grid">

                    <h2>
                        Lead traveller and contact
                    </h2>


                    {{-- Contact Name --}}

                    <div>

                        <label for="contact_name">
                            Full name *
                        </label>

                        <input
                            id="contact_name"
                            name="contact_name"
                            type="text"
                            value="{{ old(
                                'contact_name',
                                auth()->user()->name
                            ) }}"
                            maxlength="150"
                            autocomplete="name"
                            required
                        >

                        @error('contact_name')

                            <small class="booking-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Contact Email --}}

                    <div>

                        <label for="contact_email">
                            Email *
                        </label>

                        <input
                            id="contact_email"
                            type="email"
                            name="contact_email"
                            value="{{ old(
                                'contact_email',
                                auth()->user()->email
                            ) }}"
                            maxlength="255"
                            autocomplete="email"
                            required
                        >

                        @error('contact_email')

                            <small class="booking-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Contact Phone --}}

                    <div>

                        <label for="contact_phone">
                            Phone *
                        </label>

                        <input
                            id="contact_phone"
                            type="tel"
                            name="contact_phone"
                            value="{{ old('contact_phone') }}"
                            maxlength="40"
                            autocomplete="tel"
                            required
                        >

                        @error('contact_phone')

                            <small class="booking-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Country --}}

                    <div>

                        <label for="country">
                            Country
                        </label>

                        <input
                            id="country"
                            name="country"
                            value="{{ old('country', 'India') }}"
                            maxlength="100"
                            autocomplete="country-name"
                        >

                        @error('country')

                            <small class="booking-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>

                </section>


                {{-- =================================================
                     TRAVELLERS
                     ================================================= --}}

                <section class="booking-form__section">

                    <div class="booking-section-heading">

                        <div>

                            <h2>
                                Traveller details
                            </h2>

                            <p>
                                Add one traveller for each seat.
                                Every traveller must provide individual
                                identity proof.
                            </p>

                        </div>


                        <button
                            type="button"
                            class="storefront-button storefront-button--secondary storefront-button--small"
                            data-add-traveller
                        >
                            + Add traveller
                        </button>

                    </div>


                    <div
                        class="booking-seat-info"
                        data-traveller-count
                    >
                        {{ $initialTravellerCount }}
                        {{ Str::plural(
                            'traveller',
                            $initialTravellerCount
                        ) }}
                        selected
                    </div>


                    @error('travellers')

                        <small class="booking-error">
                            {{ $message }}
                        </small>

                    @enderror


                    <div data-traveller-list>

                        @foreach($travellers as $index => $traveller)

                            @include(
                                'bookings.partials.traveller-form',
                                [
                                    'index' => $index,
                                    'traveller' => $traveller,
                                ]
                            )

                        @endforeach

                    </div>

                </section>


                {{-- =================================================
                     SPECIAL REQUESTS
                     ================================================= --}}

                <section class="booking-form__section">

                    <label for="special_requests">

                        Special requests

                        <span class="storefront-muted">
                            (optional)
                        </span>

                    </label>


                    <textarea
                        id="special_requests"
                        name="special_requests"
                        rows="4"
                        maxlength="2000"
                        placeholder="Accessibility needs, meal preferences, or anything else we should know."
                    >{{ old('special_requests') }}</textarea>


                    @error('special_requests')

                        <small class="booking-error">
                            {{ $message }}
                        </small>

                    @enderror

                </section>


                {{-- =================================================
                     SUBMIT
                     ================================================= --}}

                <button
                    type="submit"
                    class="storefront-button storefront-button--wide"
                    data-booking-submit
                >
                    Continue to payment
                </button>

            </form>

        </div>


        {{-- =========================================================
             BOOKING SUMMARY
             ========================================================= --}}

        <aside class="booking-summary">

            <img
                src="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                alt="{{ $tour->name }}"
            >


            <h2>
                {{ $tour->name }}
            </h2>


            <p>
                {{ $tour->duration_days }}
                days /
                {{ $tour->duration_nights }}
                nights
            </p>


            <div class="booking-summary__departure">

                <span>
                    Selected departure
                </span>

                <strong data-summary-departure>
                    {{ $selectedDeparture->departure_date->format('d M Y') }}
                </strong>

            </div>


            <div class="booking-summary__seats">

                <span>
                    Available seats
                </span>

                <strong data-summary-seats>
                    {{ $selectedDeparture->available_seats }}
                </strong>

            </div>


            <div class="booking-summary__travellers">

                <span>
                    Travellers
                </span>

                <strong data-summary-travellers>
                    {{ $initialTravellerCount }}
                </strong>

            </div>


            <div class="booking-summary__total">

                <span>
                    Estimated total
                </span>

                <strong data-booking-total>
                    ₹{{ number_format(
                        (float) $selectedDeparture->effective_price
                        * $initialTravellerCount,
                        0
                    ) }}
                </strong>

                <small>
                    Taxes, if applicable, appear before payment.
                </small>

            </div>

        </aside>

        </div>

    </div>


    {{-- =============================================================
         DYNAMIC TRAVELLER TEMPLATE
         ============================================================= --}}

    <template id="traveller-template">

        @include(
            'bookings.partials.traveller-form',
            [
                'index' => '__INDEX__',
                'traveller' => [],
            ]
        )

    </template>

</section>

@endsection