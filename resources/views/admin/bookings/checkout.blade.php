@extends('admin.layouts.app')

@section('title', 'Confirm Booking '.$booking->booking_number)

@section('content')

@php
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

            <span class="admin-eyebrow">CONFIRM PAYMENT</span>

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

        <p class="admin-muted">
            Leave this booking unpaid for now, or record how the customer paid.
        </p>

        <div class="payment-method-actions">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="admin-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Cancel
            </a>

            <div class="payment-method-actions__buttons">

                <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                    @csrf
                    <button type="submit" class="admin-button">
                        Save as unpaid
                    </button>
                </form>

                <button
                    type="button"
                    class="admin-button admin-button--primary"
                    data-open-mark-paid-modal
                >
                    Confirm &amp; mark as paid — {{ $booking->currency }} {{ number_format($payableAmount, 2) }}
                </button>

            </div>
        </div>

    </section>

</div>


{{-- =====================================================
     MARK AS PAID MODAL
     Opens directly on the payment options — no checkbox gate.
====================================================== --}}

<dialog id="mark-paid-modal" class="mark-paid-modal">

    <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" id="mark-paid-form">
        @csrf

        <div class="admin-card__header">
            <div>
                <span class="admin-eyebrow">PAYMENT</span>
                <h2>How did the customer pay?</h2>
                <p>Select the payment method to mark this booking as paid.</p>
            </div>
        </div>

        @include('admin.bookings.partials.payment-method-fields')

        <div class="payment-method-actions">
            <button type="button" class="admin-button" data-mark-paid-cancel>
                Cancel
            </button>

            <button type="submit" class="admin-button admin-button--primary">
                Confirm &amp; mark as paid
            </button>
        </div>

    </form>

</dialog>


<style>
    .payment-method-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 20px;
    }

    .payment-method-actions__buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mark-paid-modal {
        /*
         * Tailwind's preflight resets `margin` to 0 on every element
         * (including dialog), which breaks the browser's native
         * `margin: auto` centering for <dialog>. Center it explicitly
         * instead of relying on that default.
         */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        margin: 0;

        max-height: 90vh;
        width: min(640px, 92vw);
        padding: 24px;

        border: none;
        border-radius: var(--admin-radius-lg);

        box-shadow: var(--admin-shadow-md);

        overflow-y: auto;
    }

    .mark-paid-modal::backdrop {
        background: rgba(15, 23, 42, .5);
    }

    .mark-paid-modal .admin-card__header {
        margin-bottom: 18px;
    }

    .mark-paid-modal .payment-method-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;

        margin-top: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modal = document.getElementById('mark-paid-modal');
        const openButton = document.querySelector('[data-open-mark-paid-modal]');
        const cancelButton = document.querySelector('[data-mark-paid-cancel]');

        if (!modal || !openButton) {
            return;
        }

        function openModal() {
            modal.showModal();
        }

        function closeModal() {
            modal.close();
        }

        openButton.addEventListener('click', openModal);

        cancelButton?.addEventListener('click', closeModal);

        modal.addEventListener('cancel', function (event) {
            event.preventDefault();
            closeModal();
        });

    });
</script>

@endsection
