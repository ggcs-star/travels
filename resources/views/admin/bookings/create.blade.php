@extends('admin.layouts.app')

@section('title', 'Create Booking')

@section('description', 'Book a tour on behalf of a customer and apply their travel points.')

@section('content')

<div class="admin-page">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div class="admin-page__actions">
            <a
                href="{{ route('admin.bookings.index') }}"
                class="admin-button admin-button--primary"
            >
                ← Back to Bookings
            </a>
        </div>

    </div>


    {{-- =====================================================
         VALIDATION ERRORS
    ====================================================== --}}

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


    @if($tours->isEmpty())

        <div class="admin-card">
            <p class="admin-muted">
                There are no published tours with bookable departures
                right now. Add an open departure before creating a booking.
            </p>
        </div>

    @else

    <form
        method="POST"
        action="{{ route('admin.bookings.store') }}"
        enctype="multipart/form-data"
        data-booking-form
    >
        @csrf


        {{-- =================================================
             CUSTOMER
        ================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">CUSTOMER</span>
                    <h2>Who is this booking for?</h2>
                </div>
            </div>

            <div class="admin-form-grid">

                <div class="admin-form-group">

                    <label for="user_id">
                        Customer
                        <span>*</span>
                    </label>

                    <select
                        id="user_id"
                        name="user_id"
                        data-customer-select
                        required
                    >
                        <option value="">Select a customer</option>

                        @foreach($users as $user)
                            <option
                                value="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-points="{{ (int) ($user->pointWallet->balance ?? 0) }}"
                                @selected((string) old('user_id') === (string) $user->id)
                            >
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>

                    <small>
                        Only registered customers can be booked for,
                        so their travel points can be applied.
                    </small>

                    @error('user_id')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror

                </div>

                <div class="admin-form-group">
                    <label>Available points</label>
                    <div class="admin-points-hint" data-customer-points-hint>
                        Select a customer to see their points balance.
                    </div>
                </div>

            </div>

        </div>


        {{-- =================================================
             TOUR & DEPARTURE
        ================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">TRAVEL</span>
                    <h2>Tour &amp; departure</h2>
                </div>
            </div>

            <div class="admin-form-grid">

                <div class="admin-form-group admin-form-group--full">

                    <label for="departure_id">
                        Departure
                        <span>*</span>
                    </label>

                    <select
                        id="departure_id"
                        name="departure_id"
                        data-departure-select
                        required
                    >
                        <option value="">Select a departure</option>

                        @foreach($tours as $tour)
                            <optgroup label="{{ $tour->name }}">
                                @foreach($tour->departures as $departure)
                                    <option
                                        value="{{ $departure->id }}"
                                        data-price="{{ $departure->effective_price }}"
                                        data-available-seats="{{ $departure->available_seats }}"
                                        @selected((string) old('departure_id') === (string) $departure->id)
                                    >
                                        {{ $tour->name }}
                                        ·
                                        {{ $departure->departure_date->format('D, d M Y') }}
                                        –
                                        {{ $departure->return_date->format('D, d M Y') }}
                                        ·
                                        ₹{{ number_format((float) $departure->effective_price, 0) }}
                                        ·
                                        {{ $departure->available_seats }}
                                        {{ Str::plural('seat', $departure->available_seats) }}
                                        left
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    <small data-availability-message>
                        Select a departure to see availability.
                    </small>

                    @error('departure_id')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror

                </div>

                <div class="admin-form-group">
                    <label>Travellers selected</label>
                    <div class="admin-points-hint" data-traveller-count>
                        1 traveller selected
                    </div>
                </div>

                <div class="admin-form-group">
                    <label>Estimated total</label>
                    <div class="admin-points-hint admin-points-hint--strong" data-booking-total>
                        ₹0
                    </div>
                </div>

            </div>

        </div>


        {{-- =================================================
             CONTACT DETAILS
        ================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">CONTACT</span>
                    <h2>Lead traveller &amp; contact</h2>
                </div>
            </div>

            <div class="admin-form-grid">

                <div class="admin-form-group">
                    <label for="contact_name">Full name <span>*</span></label>
                    <input
                        id="contact_name"
                        name="contact_name"
                        type="text"
                        value="{{ old('contact_name') }}"
                        maxlength="150"
                        required
                    >
                    @error('contact_name')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="contact_email">Email <span>*</span></label>
                    <input
                        id="contact_email"
                        type="email"
                        name="contact_email"
                        value="{{ old('contact_email') }}"
                        maxlength="255"
                        required
                    >
                    @error('contact_email')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="contact_phone">Phone <span>*</span></label>
                    <input
                        id="contact_phone"
                        type="tel"
                        name="contact_phone"
                        value="{{ old('contact_phone') }}"
                        maxlength="40"
                        required
                    >
                    @error('contact_phone')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="country">Country</label>
                    <input
                        id="country"
                        name="country"
                        value="{{ old('country', 'India') }}"
                        maxlength="100"
                    >
                    @error('country')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

        </div>


        {{-- =================================================
             TRAVELLERS
        ================================================== --}}

        @php
            $travellers = old('travellers') ?: [[]];
        @endphp

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">PASSENGERS</span>
                    <h2>Traveller details</h2>
                    <p>Add one traveller for each seat. Every traveller needs identity proof.</p>
                </div>

                <button
                    type="button"
                    class="admin-button"
                    data-add-traveller
                >
                    + Add traveller
                </button>
            </div>

            @error('travellers')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

            <div data-traveller-list>
                @foreach($travellers as $index => $traveller)
                    @include('admin.bookings.partials.traveller-form', [
                        'index' => $index,
                        'traveller' => $traveller,
                    ])
                @endforeach
            </div>

        </div>


        {{-- =================================================
             SPECIAL REQUESTS
        ================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">NOTES</span>
                    <h2>Special requests</h2>
                </div>
            </div>

            <div class="admin-form-grid">
                <div class="admin-form-group admin-form-group--full">
                    <textarea
                        name="special_requests"
                        rows="4"
                        maxlength="2000"
                        placeholder="Accessibility needs, meal preferences, or anything else..."
                    >{{ old('special_requests') }}</textarea>
                    @error('special_requests')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

        </div>


        {{-- =================================================
             SUBMIT
        ================================================== --}}

        <div class="admin-card">
            <div class="admin-form-actions">
                <div>
                    <strong>Ready to hold these seats?</strong>
                    <small>You'll apply points and confirm payment on the next screen.</small>
                </div>

                <div class="admin-form-actions__buttons">
                    <a href="{{ route('admin.bookings.index') }}" class="admin-button">
                        Cancel
                    </a>
                    <button type="submit" class="admin-button admin-button--primary">
                        Create booking →
                    </button>
                </div>
            </div>
        </div>

    </form>


    {{-- =========================================================
         DYNAMIC TRAVELLER TEMPLATE
         ========================================================= --}}

    <template id="traveller-template">
        @include('admin.bookings.partials.traveller-form', [
            'index' => '__INDEX__',
            'traveller' => [],
        ])
    </template>

    @endif

</div>


<style>

/* Match the table font-size baseline used across Points Wallets */

.admin-page .admin-eyebrow {
    font-size: 11px;
}

.admin-page .admin-card__header h2 {
    font-size: 17px;
}

.admin-page .admin-card__subtitle {
    font-size: 13px;
}

.admin-page .admin-form-group label {
    font-size: 13px;
}

.admin-page .admin-form-group input,
.admin-page .admin-form-group select,
.admin-page .admin-form-group textarea {
    font-size: 14px;
}

.admin-page .admin-form-group small {
    font-size: 12.5px;
}

.admin-traveller-fieldset {
    padding: 18px;
    margin-bottom: 16px;
    border: 1px solid var(--admin-border, #e5e8ed);
    border-radius: 12px;
    background: #fafbfc;
}

.admin-traveller-fieldset:last-child {
    margin-bottom: 0;
}

.admin-traveller-fieldset__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.admin-traveller-fieldset__header legend {
    color: #344054;
    font-size: 14px;
    font-weight: 750;
}

.admin-traveller-fieldset__remove {
    border: 0;
    background: none;
    color: #a13939;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.admin-points-hint {
    padding: 10px 12px;
    border: 1px solid var(--admin-border, #e5e8ed);
    border-radius: 8px;
    background: #fff;
    color: #586273;
    font-size: 13px;
}

.admin-points-hint--strong {
    font-weight: 750;
    color: #202b3e;
}

.admin-form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.admin-form-actions strong {
    display: block;
    color: var(--admin-text-secondary, #4b5666);
    font-size: 14.5px;
}

.admin-form-actions small {
    display: block;
    margin-top: 4px;
    color: var(--admin-text-light, #9aa2ae);
    font-size: 13px;
}

.admin-form-actions__buttons {
    display: flex;
    align-items: center;
    gap: 8px;
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect = document.querySelector('[data-customer-select]');
    const pointsHint = document.querySelector('[data-customer-points-hint]');
    const nameInput = document.getElementById('contact_name');
    const emailInput = document.getElementById('contact_email');

    if (!customerSelect) {
        return;
    }

    function applyCustomer() {

        const option = customerSelect.selectedOptions?.[0];

        if (!option || !option.value) {

            if (pointsHint) {
                pointsHint.textContent = 'Select a customer to see their points balance.';
            }

            return;
        }

        const points = Number(option.dataset.points ?? 0);

        if (pointsHint) {
            pointsHint.textContent = `${points.toLocaleString('en-IN')} points available`;
        }

        if (nameInput && !nameInput.value) {
            nameInput.value = option.dataset.name ?? '';
        }

        if (emailInput && !emailInput.value) {
            emailInput.value = option.dataset.email ?? '';
        }
    }

    customerSelect.addEventListener('change', applyCustomer);
    applyCustomer();

});
</script>

@endsection
