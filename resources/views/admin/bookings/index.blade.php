@extends('admin.layouts.app')

@section('title', 'Bookings')

@section('description', 'Monitor customer reservations and payment status.')

@section('content')

<div class="admin-page">


    {{-- =====================================================
         FILTERS
    ====================================================== --}}
    <section class="admin-card">

        <form
            method="GET"
            action="{{ route('admin.bookings.index') }}"
            class="admin-filter-form"
        >

            {{-- Search --}}
            <div class="admin-filter-form__group">

                <label for="search">
                    Search
                </label>

                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Booking number or customer"
                >

            </div>


            {{-- Booking Status --}}
            <div class="admin-filter-form__group">

                <label for="status">
                    Booking status
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option value="">
                        All
                    </option>

                    @foreach([
                        'pending_payment',
                        'confirmed',
                        'cancelled',
                        'expired'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ Str::headline($status) }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Payment Status --}}
            <div class="admin-filter-form__group">

                <label for="payment_status">
                    Payment status
                </label>

                <select
                    id="payment_status"
                    name="payment_status"
                >

                    <option value="">
                        All
                    </option>

                    @foreach([
                        'unpaid',
                        'paid',
                        'failed',
                        'refunded'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(request('payment_status') === $status)
                        >
                            {{ Str::headline($status) }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Filter Actions --}}
            <div class="admin-filter-form__actions">

                <button
                    type="submit"
                    class="admin-button admin-button--primary"
                >
                    Filter
                </button>

                <a
                    href="{{ route('admin.bookings.index') }}"
                    class="admin-button"
                >
                    Reset
                </a>

            </div>

        </form>

    </section>


    {{-- =====================================================
         BOOKINGS
    ====================================================== --}}
    <section class="admin-card">

        @if($bookings->isNotEmpty())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Booking
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Tour
                            </th>

                            <th>
                                Departure
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Payment
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($bookings as $booking)

                            <tr>

                                {{-- Booking --}}
                                <td>

                                    <strong>
                                        {{ $booking->booking_number }}
                                    </strong>

                                    <small>
                                        {{ Str::headline($booking->status) }}
                                    </small>

                                </td>


                                {{-- Customer --}}
                                <td>

                                    <strong>
                                        {{ $booking->contact_name }}
                                    </strong>

                                    <small>
                                        {{ $booking->contact_email }}
                                    </small>

                                </td>


                                {{-- Tour --}}
                                <td>

                                    {{ $booking->tourPackage?->name ?? '—' }}

                                </td>


                                {{-- Departure --}}
                                <td>

                                    @if($booking->departure)

                                        {{ $booking->departure->departure_date?->format('d M Y') ?? '—' }}

                                    @else

                                        —

                                    @endif

                                </td>


                                {{-- Total --}}
                                <td>

                                    <strong>
                                        ₹{{ number_format((float) $booking->total_amount, 2) }}
                                    </strong>

                                </td>


                                {{-- Payment --}}
                                <td>

                                    @php

                                        $paymentBadgeClass = match ($booking->payment_status) {

                                            'paid' =>
                                                'admin-badge--success',

                                            'failed' =>
                                                'admin-badge--danger',

                                            'refunded' =>
                                                'admin-badge--warning',

                                            default =>
                                                '',

                                        };

                                    @endphp


                                    <span
                                        class="admin-badge {{ $paymentBadgeClass }}"
                                    >
                                        {{ Str::headline($booking->payment_status) }}
                                    </span>

                                </td>


                                {{-- Action --}}
                                <td>

                                    <a
                                        href="{{ route('admin.bookings.show', $booking) }}"
                                        class="admin-table-action"
                                        title="View"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="admin-sr-only">View</span>
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="admin-pagination">

                {{ $bookings->links() }}

            </div>


        @else

            <div class="admin-empty-state">

                <div class="admin-empty-state__icon">
                    ▣
                </div>

                <h2>
                    No bookings found
                </h2>

                <p>
                    There are no bookings matching your current filters.
                </p>

                @if(request()->hasAny([
                    'search',
                    'status',
                    'payment_status'
                ]))

                    <a
                        href="{{ route('admin.bookings.index') }}"
                        class="admin-button admin-button--dark"
                    >
                        Clear filters
                    </a>

                @endif

            </div>

        @endif

    </section>

</div>

@endsection