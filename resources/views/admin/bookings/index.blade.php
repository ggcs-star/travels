@extends('admin.layouts.app')

@section('title', 'Bookings')

@section('description', 'Monitor customer reservations and payment status.')

@section('content')

<div class="admin-page">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div class="admin-page__actions">
            <a
                href="{{ route('admin.bookings.create') }}"
                class="admin-button admin-button--primary"
            >
                + Add Booking
            </a>
        </div>

    </div>


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
                                        ₹{{ number_format((float) $booking->payableAmount(), 2) }}
                                    </strong>

                                    @if((float) $booking->points_discount > 0)

                                        <small class="admin-table-discount">
                                            <s>₹{{ number_format((float) $booking->total_amount, 2) }}</s>
                                            ·
                                            −{{ number_format((int) $booking->points_redeemed) }} pts
                                        </small>

                                    @endif

                                </td>


                                {{-- Payment --}}
                                <td>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.bookings.payment-status.update', $booking) }}"
                                        class="admin-inline-status-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="payment_status"
                                            title="Change payment status"
                                            data-status-select
                                            class="admin-status-select admin-status-select--{{ $booking->payment_status }}"
                                            onchange="this.form.submit()"
                                        >
                                            @foreach(['unpaid', 'paid', 'failed', 'refunded'] as $status)
                                                <option
                                                    value="{{ $status }}"
                                                    @selected($booking->payment_status === $status)
                                                >
                                                    {{ Str::headline($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

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

                                    <form
                                        method="POST"
                                        action="{{ route('admin.bookings.destroy', $booking) }}"
                                        onsubmit="return confirm('Delete this booking? It can be restored later if needed.')"
                                        style="display:inline;"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="admin-table-action admin-table-action--danger"
                                            title="Delete"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                            <span class="admin-sr-only">Delete</span>
                                        </button>
                                    </form>

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


<style>

/* Match the table font-size baseline used across Points Wallets */

.admin-page .admin-eyebrow {
    font-size: 11px;
}

.admin-page .admin-filter-form label {
    font-size: 13px;
}

.admin-page .admin-filter-form input,
.admin-page .admin-filter-form select {
    font-size: 14px;
}

.admin-table-discount {
    display: block;
    margin-top: 3px;
    color: #a13939;
    font-size: 12px;
    font-weight: 650;
}

.admin-table-discount s {
    color: #9aa2ae;
    text-decoration-color: #cbd0d7;
}

.admin-status-select {
    height: 28px;
    border: 1px solid #dfe3e9;
    border-radius: 6px;
    background: #fff;
    color: #4b5666;
    font-size: 10px;
    font-weight: 650;
    padding: 0 6px;
    cursor: pointer;
}

.admin-status-select:focus {
    outline: none;
    border-color: #8993a3;
}

.admin-status-select--paid {
    background: var(--admin-success-bg, #ecfdf3);
    border-color: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.admin-status-select--failed {
    background: var(--admin-danger-bg, #fff1f2);
    border-color: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.admin-status-select--refunded {
    background: #fffbeb;
    border-color: #fffbeb;
    color: #b45309;
}

.admin-status-select--unpaid {
    background: #eef1f5;
    border-color: #eef1f5;
    color: #4b5666;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-status-select]').forEach(function (select) {

        select.addEventListener('change', function () {

            select.className = 'admin-status-select admin-status-select--' + select.value;

        });

    });

});
</script>

@endsection