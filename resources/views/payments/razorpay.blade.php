@extends('layouts.app')

@section('title', 'Secure payment | '.config('travels.brand.name'))

@section('content')
    @php
        $totalAmount = (float) $booking->total_amount;
        $pointsRedeemed = (int) ($booking->points_redeemed ?? 0);
        $pointsDiscount = (float) ($booking->points_discount ?? 0);
        $payableAmount = (float) $booking->payableAmount();

        $wallet = auth()->user()?->pointWallet;
        $availablePoints = (int) ($wallet?->balance ?? 0);

        $redemption = app(\App\Services\Points\PointSettingService::class)
            ->calculateRedemption(
                availablePoints: $availablePoints,
                bookingAmount: $totalAmount
            );
    @endphp

    <section class="storefront-section payment-page">
        <div class="container payment-card">

            <span class="storefront-kicker">Secure payment</span>

            <h1>Confirm your booking</h1>

            <p>
                {{ $booking->tourPackage->name }}
                ·
                {{ $booking->traveller_count }}
                {{ Str::plural('traveller', $booking->traveller_count) }}
            </p>

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

                <div class="payment-summary__divider"></div>

                <div class="payment-summary__row payment-summary__row--total">
                    <span>Payable amount</span>

                    <strong class="payment-card__amount">
                        ₹{{ number_format($payableAmount, 2) }}
                    </strong>
                </div>

            </div>


            {{-- ================================
                TRAVEL POINTS
            ================================= --}}
            @if ($redemption['enabled'])
                <div class="payment-points">

                    <div class="payment-points__header">
                        <div>
                            <span class="storefront-kicker">
                                Travel Points
                            </span>

                            <h2>Use your points</h2>

                            <p class="storefront-muted">
                                You have
                                <strong>
                                    {{ number_format($availablePoints) }}
                                </strong>
                                points available.
                            </p>
                        </div>

                        <div class="payment-points__balance">
                            <strong>
                                {{ number_format($availablePoints) }}
                            </strong>
                            <span>Points</span>
                        </div>
                    </div>


                    @if ($availablePoints > 0)

                        @if ($pointsRedeemed > 0)

                            {{-- Currently applied points --}}
                            <div class="payment-points__applied">

                                <div>
                                    <span>Applied points</span>

                                    <strong>
                                        {{ number_format($pointsRedeemed) }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Discount</span>

                                    <strong>
                                        ₹{{ number_format($pointsDiscount, 2) }}
                                    </strong>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('payments.points.remove', $booking) }}"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="storefront-link-button"
                                    >
                                        Remove
                                    </button>
                                </form>

                            </div>

                        @else

                            {{-- Apply points --}}
                            <form
                                method="POST"
                                action="{{ route('payments.points.apply', $booking) }}"
                                class="payment-points__form"
                            >
                                @csrf

                                <label for="points">
                                    Points to use
                                </label>

                                <div class="payment-points__input-row">

                                    <input
                                        type="number"
                                        id="points"
                                        name="points"
                                        min="1"
                                        max="{{ $redemption['points_to_redeem'] }}"
                                        value="{{ old('points', $redemption['points_to_redeem']) }}"
                                        required
                                    >

                                    <button
                                        type="submit"
                                        class="storefront-button"
                                    >
                                        Apply Points
                                    </button>

                                </div>

                                <p class="storefront-muted">
                                    You can use up to
                                    <strong>
                                        {{ number_format($redemption['points_to_redeem']) }}
                                    </strong>
                                    points on this booking.
                                </p>

                                @error('points')
                                    <p class="payment-form-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </form>

                        @endif

                    @else

                        <p class="storefront-muted">
                            You currently don't have enough Travel Points
                            to use on this booking.
                        </p>

                    @endif

                </div>
            @endif


            {{-- ================================
                PAYMENT INFORMATION
            ================================= --}}
            <p class="storefront-muted payment-card__secure-text">
                Payments are processed securely by Razorpay.
                We do not store card or UPI details.
            </p>


            {{-- ================================
                PAY BUTTON
            ================================= --}}
            <button
                type="button"
                class="storefront-button storefront-button--wide"
                id="start-razorpay-payment"
            >
                Pay ₹{{ number_format($payableAmount, 2) }}
            </button>

            <a
                href="{{ route('bookings.show', $booking) }}"
                class="storefront-link"
            >
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

                    key: @json(config('services.razorpay.key_id')),

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
                        color: '#e27627'
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

