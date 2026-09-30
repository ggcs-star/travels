@extends('layouts.app')

@php
    $razorpaySettings = app(\App\Services\Payments\PaymentSettingsService::class);

    /*
    |--------------------------------------------------------------------------
    | PAYMENT AMOUNTS
    |--------------------------------------------------------------------------
    */

    $totalAmount = (float) $booking->total_amount;

    $pointsRedeemed = (int) (
        $booking->points_redeemed ?? 0
    );

    $pointsDiscount = (float) (
        $booking->points_discount ?? 0
    );

    $payableAmount = (float) $booking->payableAmount();


    /*
    |--------------------------------------------------------------------------
    | TOUR
    |--------------------------------------------------------------------------
    */

    $tour = $booking->tourPackage;


    /*
    |--------------------------------------------------------------------------
    | TOUR IMAGE
    |--------------------------------------------------------------------------
    */

    $tourImageUrl = $tour?->cover_image_url;
@endphp


@section('title', 'Secure payment | '.config('travels.brand.name'))


@section('content')

<section class="storefront-section payment-page">

    <div class="container">

        <div class="payment-layout">


            {{-- =========================================================
                LEFT SIDE
            ========================================================== --}}

            <div class="payment-card">


                {{-- =====================================================
                    SECURE PAYMENT BADGE
                ====================================================== --}}

                <div class="payment-card__badge">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect
                            x="5"
                            y="11"
                            width="14"
                            height="10"
                            rx="2"
                        />

                        <path
                            d="M8 11V7a4 4 0 1 1 8 0v4"
                        />
                    </svg>

                    Secure Payment

                </div>


                {{-- =====================================================
                    PAGE TITLE
                ====================================================== --}}

                <h1>
                    Confirm your booking
                </h1>


                {{-- =====================================================
                    TRIP DETAILS
                ====================================================== --}}

                <div class="payment-trip">

                    <strong>
                        {{ $booking->tripName() }}
                    </strong>


                    <div class="payment-trip__meta">


                        {{-- DATE --}}
                        <span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="16"
                                    rx="2"
                                />

                                <path
                                    d="M3 10h18M8 3v4M16 3v4"
                                />
                            </svg>


                            {{ $booking->tripDepartureDate()?->format('d M Y') }}


                            @if ($booking->tripReturnDate())

                                <span class="payment-date-separator">
                                    –
                                </span>

                                {{ $booking->tripReturnDate()->format('d M Y') }}

                            @endif

                        </span>


                        {{-- TRAVELLERS --}}
                        <span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                />

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    d="M23 21v-2a4 4 0 0 0-3-3.87"
                                />

                                <path
                                    d="M16 3.13a4 4 0 0 1 0 7.75"
                                />
                            </svg>


                            {{ $booking->traveller_count }}

                            {{ Str::plural(
                                'traveller',
                                $booking->traveller_count
                            ) }}

                        </span>

                    </div>

                </div>


                {{-- =====================================================
                    PAYMENT SUMMARY
                ====================================================== --}}

                <div class="payment-summary">


                    {{-- BOOKING AMOUNT --}}
                    <div class="payment-summary__row">

                        <span>
                            Booking amount
                        </span>

                        <strong>
                            ₹{{ number_format(
                                $totalAmount,
                                2
                            ) }}
                        </strong>

                    </div>


                    {{-- POINTS DISCOUNT --}}
                    @if ($pointsRedeemed > 0)

                        <div
                            class="
                                payment-summary__row
                                payment-summary__row--discount
                            "
                        >

                            <span>

                                Points discount

                                <small>
                                    (
                                    {{ number_format(
                                        $pointsRedeemed
                                    ) }}
                                    points
                                    )
                                </small>

                            </span>


                            <strong>
                                −₹{{ number_format(
                                    $pointsDiscount,
                                    2
                                ) }}
                            </strong>

                        </div>

                    @endif


                    {{-- PAYABLE --}}
                    <div
                        class="
                            payment-summary__row
                            payment-summary__row--total
                        "
                    >

                        <span>
                            Payable amount
                        </span>


                        <strong class="payment-card__amount">

                            ₹{{ number_format(
                                $payableAmount,
                                2
                            ) }}

                        </strong>

                    </div>

                </div>


                {{-- =====================================================
                    PAY BUTTON
                ====================================================== --}}

                <button
                    type="button"
                    class="
                        storefront-button
                        storefront-button--wide
                    "
                    id="start-razorpay-payment"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect
                            x="5"
                            y="11"
                            width="14"
                            height="10"
                            rx="2"
                        />

                        <path
                            d="M8 11V7a4 4 0 1 1 8 0v4"
                        />
                    </svg>


                    Pay ₹{{ number_format(
                        $payableAmount,
                        2
                    ) }} securely

                </button>


                {{-- =====================================================
                    SECURITY MESSAGE
                ====================================================== --}}

                <p class="payment-trust">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"
                        />
                    </svg>

                    Payments are processed securely by Razorpay.
                    We do not store card or UPI details.

                </p>


                {{-- =====================================================
                    BACK
                ====================================================== --}}

                <a
                    href="{{ route(
                        'bookings.show',
                        $booking
                    ) }}"
                    class="payment-back-link"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M19 12H5"/>

                        <path d="M12 19l-7-7 7-7"/>
                    </svg>

                    Return to booking

                </a>


                {{-- =====================================================
                    VERIFICATION FORM
                ====================================================== --}}

                <form
                    id="razorpay-verification-form"
                    method="POST"
                    action="{{ route(
                        'payments.verify',
                        $booking
                    ) }}"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="razorpay_payment_id"
                    >

                    <input
                        type="hidden"
                        name="razorpay_order_id"
                    >

                    <input
                        type="hidden"
                        name="razorpay_signature"
                    >

                </form>

            </div>


            {{-- =========================================================
                RIGHT SIDE
            ========================================================== --}}

            <aside class="payment-tour-card">


                {{-- =====================================================
                    TOUR IMAGE
                ====================================================== --}}

                @if ($tourImageUrl)

                    <div class="payment-tour-image">

                        <img
                            src="{{ $tourImageUrl }}"
                            alt="{{ $tour?->name ?? $booking->tripName() }}"
                            loading="lazy"
                        >

                    </div>

                @else

                    <div
                        class="
                            payment-tour-image
                            payment-tour-image--placeholder
                        "
                    >

                        <div>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="2"
                                />

                                <circle
                                    cx="8.5"
                                    cy="8.5"
                                    r="1.5"
                                />

                                <path
                                    d="m21 15-5-5L5 21"
                                />

                            </svg>

                            <span>
                                Tour image
                            </span>

                        </div>

                    </div>

                @endif


                {{-- =====================================================
                    TOUR INFORMATION
                ====================================================== --}}

                <div class="payment-tour-content">

                    <span class="payment-tour-label">
                        Your trip
                    </span>


                    <h2 class="payment-tour-title">
                        {{ $tour?->name ?? $booking->tripName() }}
                    </h2>


                    <div class="payment-tour-info">


                        {{-- BOOKING --}}
                        <div class="payment-tour-info-item">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M4 4h16v16H4z"/>

                                <path
                                    d="M8 8h8M8 12h8M8 16h5"
                                />
                            </svg>


                            <span>

                                <strong>
                                    Booking
                                </strong>

                                <br>

                                {{ $booking->booking_number }}

                            </span>

                        </div>


                        {{-- DEPARTURE --}}
                        <div class="payment-tour-info-item">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="16"
                                    rx="2"
                                />

                                <path
                                    d="M3 10h18M8 3v4M16 3v4"
                                />

                            </svg>


                            <span>

                                <strong>
                                    Departure
                                </strong>

                                <br>

                                {{ $booking->tripDepartureDate()?->format(
                                    'd M Y'
                                ) }}

                            </span>

                        </div>


                        {{-- TRAVELLERS --}}
                        <div class="payment-tour-info-item">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    d="M3 21v-2a6 6 0 0 1 12 0v2"
                                />

                                <path
                                    d="M16 3.5a4 4 0 0 1 0 7"
                                />

                                <path
                                    d="M21 21v-2a5 5 0 0 0-4-4.9"
                                />

                            </svg>


                            <span>

                                <strong>
                                    Travellers
                                </strong>

                                <br>

                                {{ $booking->traveller_count }}

                                {{ Str::plural(
                                    'traveller',
                                    $booking->traveller_count
                                ) }}

                            </span>

                        </div>


                        {{-- CONTACT --}}
                        @if ($booking->contact_name)

                            <div class="payment-tour-info-item">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <circle
                                        cx="12"
                                        cy="8"
                                        r="4"
                                    />

                                    <path
                                        d="M4 21a8 8 0 0 1 16 0"
                                    />

                                </svg>


                                <span>

                                    <strong>
                                        Contact
                                    </strong>

                                    <br>

                                    {{ $booking->contact_name }}

                                </span>

                            </div>

                        @endif

                    </div>


                    {{-- =================================================
                        PRICE
                    ================================================== --}}

                    <div class="payment-tour-price">

                        <span>
                            Amount to pay
                        </span>


                        <strong>
                            ₹{{ number_format(
                                $payableAmount,
                                2
                            ) }}
                        </strong>

                    </div>

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection


{{-- ================================================================
    RAZORPAY SCRIPT
================================================================= --}}

@push('scripts')

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>

document
    .getElementById('start-razorpay-payment')
    ?.addEventListener('click', function () {

        const button = this;


        const verificationForm =
            document.getElementById(
                'razorpay-verification-form'
            );


        if (!verificationForm) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Double Click
        |--------------------------------------------------------------------------
        */

        if (button.dataset.processing === '1') {
            return;
        }


        button.dataset.processing = '1';

        button.disabled = true;


        button.dataset.originalText =
            button.textContent;


        button.textContent =
            'Opening payment...';


        /*
        |--------------------------------------------------------------------------
        | Razorpay
        |--------------------------------------------------------------------------
        */

        const checkout = new Razorpay({

            key: @json(
                $razorpaySettings->getKeyId()
            ),


            amount: @json(
                $order['amount']
            ),


            currency: @json(
                $order['currency']
            ),


            name: @json(
                config('travels.brand.name')
            ),


            description: @json(
                'Booking '.$booking->booking_number
            ),


            order_id: @json(
                $order['id']
            ),


            prefill: {

                name: @json(
                    $booking->contact_name
                ),

                email: @json(
                    $booking->contact_email
                ),

                contact: @json(
                    $booking->contact_phone
                ),

            },


            theme: {
                color: '#ff7914'
            },


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            handler(response) {

                verificationForm
                    .razorpay_payment_id
                    .value =
                    response.razorpay_payment_id;


                verificationForm
                    .razorpay_order_id
                    .value =
                    response.razorpay_order_id;


                verificationForm
                    .razorpay_signature
                    .value =
                    response.razorpay_signature;


                verificationForm.submit();

            },


            /*
            |--------------------------------------------------------------------------
            | CLOSE
            |--------------------------------------------------------------------------
            */

            modal: {

                ondismiss() {

                    button.dataset.processing =
                        '0';


                    button.disabled =
                        false;


                    button.textContent =
                        button.dataset.originalText;

                }

            }

        });


        checkout.open();

    });

</script>

@endpush