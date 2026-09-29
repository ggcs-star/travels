@extends('layouts.app')

@php
    $razorpaySettings = app(\App\Services\Payments\PaymentSettingsService::class);
@endphp

@section('title', 'Secure payment | '.config('travels.brand.name'))

@section('content')
    @php
        $totalAmount = (float) $booking->total_amount;
        $pointsRedeemed = (int) ($booking->points_redeemed ?? 0);
        $pointsDiscount = (float) ($booking->points_discount ?? 0);
        $payableAmount = (float) $booking->payableAmount();
    @endphp

    <section class="storefront-section payment-page">
        <div class="container payment-card">

            <div class="payment-card__badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                Secure Payment
            </div>

            <h1>Confirm your booking</h1>

            {{-- ================================
                TRIP DETAILS
            ================================= --}}
            <div class="payment-trip">
                <strong>{{ $booking->tripName() }}</strong>

                <div class="payment-trip__meta">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                        {{ $booking->tripDepartureDate()?->format('d M Y') }}
                        –
                        {{ $booking->tripReturnDate()?->format('d M Y') }}
                    </span>

                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        {{ $booking->traveller_count }}
                        {{ Str::plural('traveller', $booking->traveller_count) }}
                    </span>
                </div>
            </div>

            {{-- ================================
                PAYMENT SUMMARY
            ================================= --}}
            <div class="payment-summary">

                <div class="payment-summary__row">
                    <span>Booking amount</span>
                    <strong>
                        ₹{{ number_format($totalAmount, 2) }}
                    </strong>
                </div>

                @if ($pointsRedeemed > 0)
                    <div class="payment-summary__row payment-summary__row--discount">
                        <span>
                            Points discount
                            <small>
                                ({{ number_format($pointsRedeemed) }} points)
                            </small>
                        </span>

                        <strong>
                            −₹{{ number_format($pointsDiscount, 2) }}
                        </strong>
                    </div>
                @endif

                <div class="payment-summary__row payment-summary__row--total">
                    <span>Payable amount</span>

                    <strong class="payment-card__amount">
                        ₹{{ number_format($payableAmount, 2) }}
                    </strong>
                </div>

            </div>


            {{-- ================================
                PAY BUTTON
            ================================= --}}
            <button
                type="button"
                class="storefront-button storefront-button--wide"
                id="start-razorpay-payment"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                Pay ₹{{ number_format($payableAmount, 2) }} securely
            </button>

            {{-- ================================
                PAYMENT INFORMATION
            ================================= --}}
            <p class="payment-trust">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
                Payments are processed securely by Razorpay. We do not store card or UPI details.
            </p>

            <a
                href="{{ route('bookings.show', $booking) }}"
                class="payment-back-link"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Return to booking
            </a>


            {{-- ================================
                RAZORPAY VERIFICATION FORM
            ================================= --}}
            <form
                id="razorpay-verification-form"
                method="POST"
                action="{{ route('payments.verify', $booking) }}"
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
    </section>
@endsection


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
                 * Prevent double-click from creating multiple
                 * Razorpay checkout attempts.
                 */
                if (button.dataset.processing === '1') {
                    return;
                }

                button.dataset.processing = '1';
                button.disabled = true;

                button.dataset.originalText = button.textContent;

                button.textContent = 'Opening payment...';

                const checkout = new Razorpay({

                    key: @json($razorpaySettings->getKeyId()),

                    /*
                     * This comes from the Razorpay order created
                     * by PaymentController.
                     */
                    amount: @json($order['amount']),

                    currency: @json($order['currency']),

                    name: @json(config('travels.brand.name')),

                    description: @json(
                        'Booking '.$booking->booking_number
                    ),

                    order_id: @json($order['id']),

                    prefill: {
                        name: @json($booking->contact_name),
                        email: @json($booking->contact_email),
                        contact: @json($booking->contact_phone),
                    },

                    theme: {
                        color: '#ff7914'
                    },

                    handler(response) {

                        verificationForm
                            .razorpay_payment_id
                            .value = response.razorpay_payment_id;

                        verificationForm
                            .razorpay_order_id
                            .value = response.razorpay_order_id;

                        verificationForm
                            .razorpay_signature
                            .value = response.razorpay_signature;

                        verificationForm.submit();
                    },

                    modal: {
                        ondismiss() {
                            button.dataset.processing = '0';
                            button.disabled = false;
                            button.textContent =
                                button.dataset.originalText;
                        }
                    }
                });

                checkout.open();
            });
    </script>
@endpush

