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
        data-tax-percent="{{ (float) config('travels.booking.tax_percent', 0) }}"
        data-redemption-enabled="{{ $redemptionSetting?->redemption_enabled ? '1' : '0' }}"
        data-point-value="{{ (float) ($redemptionSetting->point_value ?? 0) }}"
        data-max-redemption-percent="{{ $redemptionSetting?->max_redemption_percent ?? '' }}"
        data-max-points-per-booking="{{ $redemptionSetting?->max_points_per_booking ?? '' }}"
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

                    <label for="departure_search">
                        Departure
                        <span>*</span>
                    </label>

                    <div class="admin-combo" data-departure-combo>

                        <input
                            type="text"
                            id="departure_search"
                            class="admin-combo__input"
                            data-combo-input
                            placeholder="Search a tour package…"
                            autocomplete="off"
                        >

                        <div class="admin-combo__panel" data-combo-panel hidden></div>

                        <small class="admin-form-error" data-combo-required-error hidden>
                            Please select a departure.
                        </small>

                    </div>

                    <select
                        id="departure_id"
                        name="departure_id"
                        data-departure-select
                        class="admin-combo__native-select"
                        hidden
                    >
                        <option value="">Select a departure</option>

                        @foreach($tours as $tour)
                            <optgroup label="{{ $tour->name }}">
                                @foreach($tour->departures as $departure)
                                    <option
                                        value="{{ $departure->id }}"
                                        data-price="{{ $departure->effective_price }}"
                                        data-currency="{{ $departure->currency }}"
                                        data-available-seats="{{ $departure->available_seats }}"
                                        data-departure-label="{{ $departure->departure_date->format('D, d M Y') }} – {{ $departure->return_date->format('D, d M Y') }} · ₹{{ number_format((float) $departure->effective_price, 0) }} · {{ $departure->available_seats }} {{ Str::plural('seat', $departure->available_seats) }} left"
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
             AMOUNT SUMMARY & TRAVEL POINTS
        ================================================== --}}

        <div class="admin-grid admin-grid--main">

            <section class="admin-card">

                <div class="admin-card__header">
                    <div>
                        <span class="admin-eyebrow">PAYMENT</span>
                        <h2>Amount summary</h2>
                    </div>
                </div>

                <div class="admin-detail-list">

                    <div>
                        <span>Traveller count</span>
                        <strong data-summary-traveller-count>1</strong>
                    </div>

                    <div>
                        <span>Subtotal</span>
                        <strong data-summary-subtotal>—</strong>
                    </div>

                    <div>
                        <span>Taxes</span>
                        <strong data-summary-tax>—</strong>
                    </div>

                    <div>
                        <span>Booking total</span>
                        <strong data-summary-booking-total>—</strong>
                    </div>

                    <div data-summary-discount-row hidden>
                        <span data-summary-discount-label>Points discount</span>
                        <strong data-summary-discount>—</strong>
                    </div>

                    <div class="admin-detail-list__highlight">
                        <span>Payable amount</span>
                        <strong data-summary-payable>—</strong>
                    </div>

                </div>

                <small class="admin-muted" data-summary-empty-hint>
                    Select a departure to see the amount summary.
                </small>

            </section>


            <section class="admin-card">

                <div class="admin-card__header">
                    <div>
                        <span class="admin-eyebrow">TRAVEL POINTS</span>
                        <h2>Redeem points for this booking?</h2>
                    </div>
                </div>

                <div class="admin-form-grid">

                    <div class="admin-form-group admin-form-group--full">
                        <label for="points">Points to redeem (optional)</label>

                        <div class="points-redeem-row">
                            <input
                                id="points"
                                type="number"
                                name="points"
                                min="1"
                                value="{{ old('points') }}"
                                placeholder="Leave blank to skip points redemption"
                            >
                            <button
                                type="button"
                                id="redeem-points-btn"
                                class="admin-button admin-button--dark"
                            >
                                Redeem points
                            </button>
                        </div>

                        <input type="hidden" id="points_otp_token" name="points_otp_token" value="">

                        <small data-points-max-hint>
                            Select a customer to see their points balance.
                        </small>

                        <div class="points-otp-status" data-points-otp-status hidden></div>

                        <small>
                            Points are only redeemed after the customer's OTP is verified.
                        </small>
                        @error('points')
                            <small class="admin-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

            </section>

        </div>

        @include('admin.bookings.partials.points-otp-modal')


        {{-- =================================================
             PAYMENT ALREADY COLLECTED?
        ================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">PAYMENT</span>
                    <h2>Was payment already collected?</h2>
                </div>
            </div>

            <div class="admin-form-grid">

                <div class="admin-form-group admin-form-group--full">
                    @include('admin.bookings.partials.payment-collected-toggle', [
                        'toggleId' => 'payment_collected',
                        'toggleTitle' => 'Payment already collected from the customer',
                        'toggleHint' => 'Tick this if the customer already paid you directly (cash, UPI, bank transfer, or Razorpay). Leave it unticked to save this booking as unpaid for now — you can confirm payment and apply points later from the bookings list.',
                    ])
                </div>

                <div
                    class="admin-form-group admin-form-group--full"
                    data-payment-collected-section
                    hidden
                >
                    @include('admin.bookings.partials.payment-method-fields')
                </div>

            </div>

        </div>


        {{-- =================================================
             SUBMIT
        ================================================== --}}

        <div class="admin-card">
            <div class="admin-form-actions">
                <div>
                    <strong>Ready to create this booking?</strong>
                    <small>Points and payment (if provided above) are applied immediately when you submit.</small>
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

.admin-combo {
    position: relative;
}

.admin-combo__input {
    width: 100%;
    box-sizing: border-box;
}

.admin-combo__input.admin-combo__input--error {
    border-color: #c0392b;
}

.admin-combo__panel {
    position: absolute;
    z-index: 20;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 320px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid var(--admin-border, #e5e8ed);
    border-radius: 10px;
    box-shadow: 0 10px 28px rgba(20, 30, 50, 0.12);
}

.admin-combo__tour-item,
.admin-combo__departure-item {
    padding: 10px 14px;
    font-size: 13.5px;
    color: #344054;
    cursor: pointer;
}

.admin-combo__tour-item:hover,
.admin-combo__departure-item:hover,
.admin-combo__tour-item--active,
.admin-combo__departure-item--active {
    background: #f3f5f8;
}

.admin-combo__group-label {
    padding: 10px 14px;
    font-weight: 750;
    font-size: 13px;
    color: #202b3e;
    background: #fafbfc;
    border-bottom: 1px solid var(--admin-border, #e5e8ed);
}

.admin-combo__back {
    display: block;
    width: 100%;
    text-align: left;
    padding: 9px 14px;
    border: 0;
    border-bottom: 1px solid var(--admin-border, #e5e8ed);
    background: #fafbfc;
    color: #4b5666;
    font-size: 12.5px;
    font-weight: 650;
    cursor: pointer;
}

.admin-combo__back:hover {
    background: #f0f2f5;
}

.admin-combo__empty {
    padding: 12px 14px;
    color: #9aa2ae;
    font-size: 13px;
}

.points-redeem-row {
    display: flex;
    gap: 8px;
}

.points-redeem-row input {
    flex: 1;
    min-width: 0;
}

.points-redeem-row #redeem-points-btn {
    flex: 0 0 auto;
    white-space: nowrap;
}

@media (max-width: 560px) {
    .points-redeem-row {
        flex-direction: column;
    }

    .points-redeem-row input,
    .points-redeem-row #redeem-points-btn {
        width: 100%;
    }
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect = document.querySelector('[data-customer-select]');
    const pointsHint = document.querySelector('[data-customer-points-hint]');
    const nameInput = document.getElementById('contact_name');
    const emailInput = document.getElementById('contact_email');
    const pointsInput = document.getElementById('points');

    function applyCustomer() {

        const option = customerSelect.selectedOptions?.[0];

        if (!option || !option.value) {

            if (pointsHint) {
                pointsHint.textContent = 'Select a customer to see their points balance.';
            }

            if (pointsInput) {
                pointsInput.removeAttribute('max');
            }

            return;
        }

        const points = Number(option.dataset.points ?? 0);

        if (pointsHint) {
            pointsHint.textContent = `${points.toLocaleString('en-IN')} points available`;
        }

        if (pointsInput) {
            if (points > 0) {
                pointsInput.max = String(points);
            } else {
                pointsInput.removeAttribute('max');
            }
        }

        if (nameInput && !nameInput.value) {
            nameInput.value = option.dataset.name ?? '';
        }

        if (emailInput && !emailInput.value) {
            emailInput.value = option.dataset.email ?? '';
        }
    }

    if (customerSelect) {
        customerSelect.addEventListener('change', applyCustomer);
        applyCustomer();
    }


    /*
    |--------------------------------------------------------------------------
    | Searchable Departure Combobox
    |--------------------------------------------------------------------------
    |
    | The real <select data-departure-select> stays in the DOM (hidden) as
    | the source of truth for form submission and for the existing booking
    | totals/availability script. This widget only reads its optgroups and
    | writes its value back, then dispatches a "change" event.
    |
    */

    const combo = document.querySelector('[data-departure-combo]');
    const comboInput = document.querySelector('[data-combo-input]');
    const comboPanel = document.querySelector('[data-combo-panel]');
    const comboRequiredError = document.querySelector('[data-combo-required-error]');
    const departureSelect = document.querySelector('[data-departure-select]');
    const bookingForm = document.querySelector('[data-booking-form]');

    if (combo && comboInput && comboPanel && departureSelect) {

        const tourGroups = Array.from(departureSelect.querySelectorAll('optgroup'));
        let lastConfirmedText = '';

        function closePanel() {
            comboPanel.hidden = true;
            comboPanel.innerHTML = '';
        }

        function openPanel() {
            comboPanel.hidden = false;
        }

        function clearRequiredError() {
            comboInput.classList.remove('admin-combo__input--error');
            if (comboRequiredError) {
                comboRequiredError.hidden = true;
            }
        }

        function renderTourList(filterText) {

            const needle = filterText.trim().toLowerCase();

            const matches = tourGroups.filter((group) => {
                return !needle || group.label.toLowerCase().includes(needle);
            });

            comboPanel.innerHTML = '';

            if (matches.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'admin-combo__empty';
                empty.textContent = 'No tour packages match your search.';
                comboPanel.appendChild(empty);
                openPanel();
                return;
            }

            matches.forEach((group) => {
                const item = document.createElement('div');
                item.className = 'admin-combo__tour-item';
                item.textContent = group.label;
                item.tabIndex = 0;
                item.addEventListener('click', (event) => {
                    event.stopPropagation();
                    comboInput.value = '';
                    renderDepartureList(group);
                });
                item.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        comboInput.value = '';
                        renderDepartureList(group);
                    }
                });
                comboPanel.appendChild(item);
            });

            openPanel();
        }

        function renderDepartureList(group, filterText = '') {

            const needle = filterText.trim().toLowerCase();
            const options = Array.from(group.querySelectorAll('option'));

            const matches = options.filter((option) => {
                const label = option.dataset.departureLabel ?? option.textContent;
                return !needle || label.toLowerCase().includes(needle);
            });

            comboPanel.innerHTML = '';

            const back = document.createElement('button');
            back.type = 'button';
            back.className = 'admin-combo__back';
            back.textContent = '← Back to tour packages';
            back.addEventListener('click', (event) => {
                event.stopPropagation();
                comboInput.value = '';
                comboInput.focus();
                renderTourList('');
            });
            comboPanel.appendChild(back);

            const groupLabel = document.createElement('div');
            groupLabel.className = 'admin-combo__group-label';
            groupLabel.textContent = group.label;
            comboPanel.appendChild(groupLabel);

            if (matches.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'admin-combo__empty';
                empty.textContent = 'No departures match your search.';
                comboPanel.appendChild(empty);
            } else {
                matches.forEach((option) => {
                    const item = document.createElement('div');
                    item.className = 'admin-combo__departure-item';
                    item.textContent = option.dataset.departureLabel ?? option.textContent.trim();
                    item.tabIndex = 0;
                    item.addEventListener('click', (event) => {
                        event.stopPropagation();
                        selectDeparture(option, group.label);
                    });
                    item.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            selectDeparture(option, group.label);
                        }
                    });
                    comboPanel.appendChild(item);
                });
            }

            openPanel();

            comboInput.dataset.comboActiveGroup = group.label;
        }

        function selectDeparture(option, tourName) {

            departureSelect.value = option.value;
            departureSelect.dispatchEvent(new Event('change', { bubbles: true }));

            const label = option.dataset.departureLabel ?? option.textContent.trim();
            lastConfirmedText = `${tourName} · ${label}`;
            comboInput.value = lastConfirmedText;

            clearRequiredError();
            closePanel();
            delete comboInput.dataset.comboActiveGroup;
        }

        comboInput.addEventListener('focus', () => {
            renderTourList('');
        });

        comboInput.addEventListener('input', () => {

            clearRequiredError();

            const activeGroupLabel = comboInput.dataset.comboActiveGroup;
            const activeGroup = activeGroupLabel
                ? tourGroups.find((group) => group.label === activeGroupLabel)
                : null;

            if (activeGroup) {
                renderDepartureList(activeGroup, comboInput.value);
            } else {
                renderTourList(comboInput.value);
            }
        });

        comboInput.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closePanel();
                comboInput.value = lastConfirmedText;
                comboInput.blur();
            }
        });

        document.addEventListener('click', (event) => {
            if (!combo.contains(event.target)) {

                if (!comboPanel.hidden) {
                    closePanel();
                    comboInput.value = lastConfirmedText;
                    delete comboInput.dataset.comboActiveGroup;
                }
            }
        });

        if (bookingForm) {
            bookingForm.addEventListener('submit', (event) => {
                if (!departureSelect.value) {
                    event.preventDefault();
                    comboInput.classList.add('admin-combo__input--error');
                    if (comboRequiredError) {
                        comboRequiredError.hidden = false;
                    }
                    comboInput.focus();
                }
            });
        }

        // Pre-fill from an already-selected option (e.g. validation round-trip).
        const preselected = departureSelect.selectedOptions?.[0];
        if (preselected && preselected.value) {
            const parentGroup = preselected.closest('optgroup');
            const label = preselected.dataset.departureLabel ?? preselected.textContent.trim();
            lastConfirmedText = parentGroup
                ? `${parentGroup.label} · ${label}`
                : label;
            comboInput.value = lastConfirmedText;
        }
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('[data-booking-form]');
    const customerSelect = document.querySelector('[data-customer-select]');
    const departureSelect = document.querySelector('[data-departure-select]');
    const travellerList = document.querySelector('[data-traveller-list]');
    const pointsInput = document.getElementById('points');

    const summaryTravellerCount = document.querySelector('[data-summary-traveller-count]');
    const summarySubtotal = document.querySelector('[data-summary-subtotal]');
    const summaryTax = document.querySelector('[data-summary-tax]');
    const summaryBookingTotal = document.querySelector('[data-summary-booking-total]');
    const summaryDiscountRow = document.querySelector('[data-summary-discount-row]');
    const summaryDiscount = document.querySelector('[data-summary-discount]');
    const summaryPayable = document.querySelector('[data-summary-payable]');
    const summaryEmptyHint = document.querySelector('[data-summary-empty-hint]');
    const pointsMaxHint = document.querySelector('[data-points-max-hint]');

    if (!form || !departureSelect || !summaryPayable) {
        return;
    }

    const taxPercent = Number(form.dataset.taxPercent ?? 0) || 0;
    const redemptionEnabled = form.dataset.redemptionEnabled === '1';
    const pointValue = Number(form.dataset.pointValue ?? 0) || 0;
    const maxRedemptionPercent = form.dataset.maxRedemptionPercent
        ? Number(form.dataset.maxRedemptionPercent)
        : null;
    const maxPointsPerBooking = form.dataset.maxPointsPerBooking
        ? Number(form.dataset.maxPointsPerBooking)
        : null;

    function formatMoney(amount, currency) {
        return `${currency} ${amount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    }

    function recalculate(options = {}) {

        const departureOption = departureSelect.selectedOptions?.[0];
        const price = departureOption ? Number(departureOption.dataset.price ?? 0) : 0;
        const currency = departureOption?.dataset.currency || 'INR';
        const count = travellerList
            ? travellerList.querySelectorAll('[data-traveller-card]').length
            : 1;

        if (summaryEmptyHint) {
            summaryEmptyHint.hidden = !!departureOption?.value;
        }

        if (summaryTravellerCount) {
            summaryTravellerCount.textContent = String(Math.max(1, count));
        }

        const subtotal = Math.round(price * Math.max(1, count) * 100) / 100;
        const tax = Math.round(subtotal * (taxPercent / 100) * 100) / 100;
        const total = Math.round((subtotal + tax) * 100) / 100;

        if (summarySubtotal) {
            summarySubtotal.textContent = formatMoney(subtotal, currency);
        }

        if (summaryTax) {
            summaryTax.textContent = formatMoney(tax, currency);
        }

        if (summaryBookingTotal) {
            summaryBookingTotal.textContent = formatMoney(total, currency);
        }


        /*
        |--------------------------------------------------------------------------
        | Points Redemption Estimate
        |--------------------------------------------------------------------------
        |
        | Mirrors PointSettingService::calculateRedemption() so the admin
        | sees a realistic cap. The server re-validates the real maximum
        | on submit regardless of what's shown here.
        */

        const customerOption = customerSelect?.selectedOptions?.[0];
        const availablePoints = customerOption ? Number(customerOption.dataset.points ?? 0) : 0;

        let maxPoints = 0;

        if (redemptionEnabled && pointValue > 0 && total > 0 && availablePoints > 0) {

            const maxDiscountByPercent = maxRedemptionPercent === null
                ? total
                : Math.round(total * (Math.min(100, Math.max(0, maxRedemptionPercent)) / 100) * 100) / 100;

            const pointsAllowedByAmount = Math.floor(maxDiscountByPercent / pointValue);

            maxPoints = Math.min(
                availablePoints,
                pointsAllowedByAmount,
                maxPointsPerBooking !== null ? maxPointsPerBooking : Infinity
            );

            maxPoints = Math.max(0, maxPoints);
        }

        if (pointsInput) {
            if (maxPoints > 0) {
                pointsInput.max = String(maxPoints);
            } else {
                pointsInput.removeAttribute('max');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Auto-fill / Clamp The Points Input
        |--------------------------------------------------------------------------
        |
        | Auto-fills the field with the maximum redeemable points as soon
        | as a customer is picked. After that, the admin can lower it
        | freely, but typing above the balance is clamped straight back
        | down — it can never exceed what's actually redeemable.
        */

        if (pointsInput && pointsInput.dataset.otpLocked !== '1') {
            if (options.resyncPointsValue) {
                pointsInput.value = maxPoints > 0 ? String(maxPoints) : '';
            } else if (Number(pointsInput.value || 0) > maxPoints) {
                pointsInput.value = maxPoints > 0 ? String(maxPoints) : '';
            }
        }

        if (pointsMaxHint) {
            if (!customerOption?.value) {
                pointsMaxHint.textContent = 'Select a customer to see their points balance.';
            } else if (!redemptionEnabled) {
                pointsMaxHint.textContent = 'Points redemption is currently disabled.';
            } else if (maxPoints <= 0) {
                pointsMaxHint.textContent = `Available balance: ${availablePoints.toLocaleString('en-IN')} points.`;
            } else {
                const maxDiscount = Math.round(maxPoints * pointValue * 100) / 100;
                pointsMaxHint.textContent =
                    `Available balance: ${availablePoints.toLocaleString('en-IN')} points. `
                    + `Up to ${maxPoints.toLocaleString('en-IN')} points can be redeemed on this booking `
                    + `(max discount ${formatMoney(maxDiscount, currency)}).`;
            }
        }

        const pointsEntered = Math.max(0, Number(pointsInput?.value ?? 0) || 0);
        const pointsToRedeem = Math.min(pointsEntered, maxPoints);
        const discount = Math.min(
            Math.round(pointsToRedeem * pointValue * 100) / 100,
            total
        );
        const payable = Math.round((total - discount) * 100) / 100;

        if (summaryDiscountRow) {
            summaryDiscountRow.hidden = discount <= 0;
        }

        if (summaryDiscount) {
            summaryDiscount.textContent = `−${formatMoney(discount, currency)}`;
        }

        if (summaryPayable) {
            summaryPayable.textContent = formatMoney(payable, currency);
        }
    }

    customerSelect?.addEventListener('change', () => {
        // Switching customers invalidates any OTP already sent/verified
        // for the previous customer — points must be re-verified.
        window.pointsOtpModal?.invalidate?.();
        recalculate({ resyncPointsValue: true });
    });
    departureSelect.addEventListener('change', () => recalculate());
    pointsInput?.addEventListener('input', () => recalculate());

    if (travellerList && window.MutationObserver) {
        new MutationObserver(() => recalculate()).observe(travellerList, { childList: true });
    }

    // On first load, auto-fill only if the field starts empty — a
    // validation round-trip (old('points')) should keep what the
    // admin already typed, just clamped to the real maximum.
    recalculate({ resyncPointsValue: !pointsInput?.value });

});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentCollectedCheckbox = document.getElementById('payment_collected');
        const paymentCollectedSection = document.querySelector('[data-payment-collected-section]');

        if (!paymentCollectedCheckbox || !paymentCollectedSection) {
            return;
        }

        function updateSectionVisibility() {
            const collected = paymentCollectedCheckbox.checked;
            paymentCollectedSection.hidden = !collected;

            paymentCollectedSection.querySelectorAll('[data-payment-method-radio]').forEach((radio) => {
                radio.disabled = !collected;
            });
        }

        paymentCollectedCheckbox.addEventListener('change', updateSectionVisibility);

        updateSectionVisibility();
    });
</script>

@endsection
