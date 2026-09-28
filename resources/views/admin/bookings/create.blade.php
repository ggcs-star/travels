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
                        'toggleHint' => 'Tick this if the customer already paid you directly (cash, UPI, bank transfer, or Razorpay) before this booking was entered into the system. The booking will be created already confirmed & paid, instead of landing on the checkout screen.',
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

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect = document.querySelector('[data-customer-select]');
    const pointsHint = document.querySelector('[data-customer-points-hint]');
    const nameInput = document.getElementById('contact_name');
    const emailInput = document.getElementById('contact_email');

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
