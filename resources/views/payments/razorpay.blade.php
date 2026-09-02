@extends('layouts.app')

@section('title', 'Secure payment | '.config('travels.brand.name'))

@section('content')
    <section class="storefront-section payment-page">
        <div class="container payment-card">
            <span class="storefront-kicker">Secure payment</span>
            <h1>Confirm your booking</h1>
            <p>{{ $booking->tourPackage->name }} · {{ $booking->traveller_count }} {{ Str::plural('traveller', $booking->traveller_count) }}</p>
            <strong class="payment-card__amount">₹{{ number_format((float) $booking->total_amount, 2) }}</strong>
            <p class="storefront-muted">Payments are processed securely by Razorpay. We do not store card or UPI details.</p>

            <button type="button" class="storefront-button storefront-button--wide" id="start-razorpay-payment">Pay ₹{{ number_format((float) $booking->total_amount, 0) }}</button>
            <a href="{{ route('bookings.show', $booking) }}" class="storefront-link">Return to booking</a>

            <form id="razorpay-verification-form" method="POST" action="{{ route('payments.verify', $booking) }}">
                @csrf
                <input type="hidden" name="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id">
                <input type="hidden" name="razorpay_signature">
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('start-razorpay-payment')?.addEventListener('click', () => {
            const verificationForm = document.getElementById('razorpay-verification-form');
            const checkout = new Razorpay({
                key: @json(config('services.razorpay.key_id')),
                amount: @json($order['amount']),
                currency: @json($order['currency']),
                name: @json(config('travels.brand.name')),
                description: @json('Booking '.$booking->booking_number),
                order_id: @json($order['id']),
                prefill: {
                    name: @json($booking->contact_name),
                    email: @json($booking->contact_email),
                    contact: @json($booking->contact_phone),
                },
                theme: { color: '#e27627' },
                handler(response) {
                    verificationForm.razorpay_payment_id.value = response.razorpay_payment_id;
                    verificationForm.razorpay_order_id.value = response.razorpay_order_id;
                    verificationForm.razorpay_signature.value = response.razorpay_signature;
                    verificationForm.submit();
                },
            });
            checkout.open();
        });
    </script>
@endpush
