@extends('admin.layouts.app')

@section('title', 'Points Management')

@section('description', 'Control how users earn, redeem and use reward points.')

@section('content')

<div class="admin-page">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="admin-alert admin-alert--danger">
            <strong>Please check the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form
        method="POST"
        action="{{ route('admin.point-settings.update') }}"
        id="pointsSettingsForm"
    >
        @csrf
        @method('PUT')


        {{-- =========================
            REWARD SETTINGS
        ========================== --}}

        <span class="admin-eyebrow">REWARD SETTINGS</span>


        {{-- Registration Reward --}}
        @php
            $registration = $settings->get(\App\Models\PointSetting::REGISTRATION_REWARD);
        @endphp

        @if($registration)
            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>New Joining Reward</h2>
                        <p class="admin-card__subtitle">Reward new users when they register.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $registration->key }}][enabled]"
                            value="1"
                            {{ $registration->enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>

                <div class="admin-form-grid admin-form-grid--three">

                    <div class="admin-form-group">
                        <label>Reward Points</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $registration->key }}][points]"
                            value="{{ old(
                                'settings.' . $registration->key . '.points',
                                $registration->points
                            ) }}"
                        >
                    </div>

                </div>

            </div>
        @endif


        {{-- Booking Reward --}}
        @php
            $booking = $settings->get(\App\Models\PointSetting::BOOKING_REWARD);
        @endphp

        @if($booking)
            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>Booking Reward</h2>
                        <p class="admin-card__subtitle">Configure points earned from successful bookings.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $booking->key }}][enabled]"
                            value="1"
                            {{ $booking->enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>


                <div class="admin-form-grid admin-form-grid--three">

                    {{-- Reward Type --}}
                    <div class="admin-form-group">
                        <label>Reward Type</label>

                        <select
                            name="settings[{{ $booking->key }}][reward_type]"
                            id="bookingRewardType"
                        >
                            <option
                                value="fixed"
                                {{ $booking->reward_type === 'fixed' ? 'selected' : '' }}
                            >
                                Fixed Points
                            </option>

                            <option
                                value="amount_based"
                                {{ $booking->reward_type === 'amount_based' ? 'selected' : '' }}
                            >
                                Amount Based
                            </option>
                        </select>
                    </div>


                    {{-- Calculation Basis --}}
                    <div class="admin-form-group">
                        <label>Calculation Based On</label>

                        <select name="settings[{{ $booking->key }}][calculation_basis]">
                            <option
                                value="final_paid"
                                {{ $booking->calculation_basis === 'final_paid' ? 'selected' : '' }}
                            >
                                Final Paid Amount
                            </option>

                            <option
                                value="booking_total"
                                {{ $booking->calculation_basis === 'booking_total' ? 'selected' : '' }}
                            >
                                Booking Total
                            </option>
                        </select>
                    </div>


                    {{-- Points --}}
                    <div class="admin-form-group">
                        <label>Points</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $booking->key }}][points]"
                            value="{{ old(
                                'settings.' . $booking->key . '.points',
                                $booking->points
                            ) }}"
                        >
                    </div>


                    {{-- Minimum Amount --}}
                    <div class="admin-form-group amount-based-field">
                        <label>Minimum Amount</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $booking->key }}][minimum_amount]"
                            value="{{ old(
                                'settings.' . $booking->key . '.minimum_amount',
                                $booking->minimum_amount
                            ) }}"
                        >

                        <small>Minimum booking amount required for reward.</small>
                    </div>


                    {{-- Amount Unit --}}
                    <div class="admin-form-group amount-based-field">
                        <label>Amount Unit (₹)</label>

                        <input
                            type="number"
                            min="1"
                            name="settings[{{ $booking->key }}][amount_unit]"
                            value="{{ old(
                                'settings.' . $booking->key . '.amount_unit',
                                $booking->amount_unit
                            ) }}"
                        >

                        <small>Example: ₹100 = configured points.</small>
                    </div>


                    {{-- Maximum Points --}}
                    <div class="admin-form-group">
                        <label>Maximum Points Per Booking</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $booking->key }}][max_points_per_booking]"
                            value="{{ old(
                                'settings.' . $booking->key . '.max_points_per_booking',
                                $booking->max_points_per_booking
                            ) }}"
                            placeholder="No limit"
                        >

                        <small>Leave empty for unlimited.</small>
                    </div>

                </div>

            </div>
        @endif


        {{-- Review Reward --}}
        @php
            $review = $settings->get(\App\Models\PointSetting::REVIEW_REWARD);
        @endphp

        @if($review)
            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>Review Reward</h2>
                        <p class="admin-card__subtitle">Reward users for submitting reviews.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $review->key }}][enabled]"
                            value="1"
                            {{ $review->enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>

                <div class="admin-form-grid admin-form-grid--three">

                    <div class="admin-form-group">
                        <label>Reward Points</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $review->key }}][points]"
                            value="{{ old(
                                'settings.' . $review->key . '.points',
                                $review->points
                            ) }}"
                        >
                    </div>

                </div>

            </div>
        @endif


        {{-- Referral Reward --}}
        @php
            $referral = $settings->get(\App\Models\PointSetting::REFERRAL_REWARD);
        @endphp

        @if($referral)
            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>Referral Reward</h2>
                        <p class="admin-card__subtitle">Reward users for successful referrals.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $referral->key }}][enabled]"
                            value="1"
                            {{ $referral->enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>

                <div class="admin-form-grid admin-form-grid--three">

                    <div class="admin-form-group">
                        <label>Referral Points</label>

                        <input
                            type="number"
                            min="0"
                            name="settings[{{ $referral->key }}][points]"
                            value="{{ old(
                                'settings.' . $referral->key . '.points',
                                $referral->points
                            ) }}"
                        >
                    </div>

                </div>

            </div>
        @endif


        {{-- =========================
            REDEMPTION
        ========================== --}}
        @php
            $redemption = $booking;
        @endphp

        @if($redemption)

            <span class="admin-eyebrow admin-eyebrow--spaced">BOOKING REDEMPTION</span>

            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>Use Points on Booking</h2>
                        <p class="admin-card__subtitle">Allow users to convert their points into booking discount.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $redemption->key }}][redemption_enabled]"
                            value="1"
                            {{ $redemption->redemption_enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>

                <div class="admin-form-grid admin-form-grid--three">

                    <div class="admin-form-group">
                        <label>1 Point Value (₹)</label>

                        <input
                            type="number"
                            step="0.0001"
                            min="0"
                            name="settings[{{ $redemption->key }}][point_value]"
                            value="{{ old(
                                'settings.' . $redemption->key . '.point_value',
                                $redemption->point_value
                            ) }}"
                        >

                        <small>Example: 1 point = ₹1.</small>
                    </div>


                    <div class="admin-form-group">
                        <label>Maximum Redemption (%)</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            name="settings[{{ $redemption->key }}][max_redemption_percent]"
                            value="{{ old(
                                'settings.' . $redemption->key . '.max_redemption_percent',
                                $redemption->max_redemption_percent
                            ) }}"
                        >

                        <small>Maximum percentage of booking amount payable using points.</small>
                    </div>

                </div>

            </div>

        @endif


        {{-- =========================
            EXPIRY
        ========================== --}}
        @if($booking)

            <span class="admin-eyebrow admin-eyebrow--spaced">POINTS EXPIRY</span>

            <div class="admin-card">

                <div class="admin-card__header">

                    <div>
                        <h2>Enable Points Expiry</h2>
                        <p class="admin-card__subtitle">Expire unused points automatically after a defined period.</p>
                    </div>

                    <label class="admin-points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $booking->key }}][expiry_enabled]"
                            value="1"
                            {{ $booking->expiry_enabled ? 'checked' : '' }}
                        >
                        <span></span>
                    </label>

                </div>

                <div class="admin-form-grid admin-form-grid--three">

                    <div class="admin-form-group">
                        <label>Expiry Period (days)</label>

                        <input
                            type="number"
                            min="1"
                            name="settings[{{ $booking->key }}][expiry_days]"
                            value="{{ old(
                                'settings.' . $booking->key . '.expiry_days',
                                $booking->expiry_days
                            ) }}"
                        >

                        <small>Points will expire after this many days.</small>
                    </div>

                </div>

            </div>

        @endif


        {{-- =========================
            SAVE BAR
        ========================== --}}
        <div class="admin-card admin-save-bar">

            <div>
                <strong>Points configuration</strong>
                <small>Changes will apply to future point transactions.</small>
            </div>

            <button
                type="submit"
                class="admin-button admin-button--primary"
            >
                Save Changes →
            </button>

        </div>

    </form>

</div>


<style>

.admin-eyebrow--spaced {
    display: block;
    margin: 26px 0 10px;
}

.admin-form-grid--three {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}


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

@media (max-width: 900px) {
    .admin-form-grid--three {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .admin-form-grid--three {
        grid-template-columns: 1fr;
    }
}


/* Toggle Switch */

.admin-points-switch {
    position: relative;
    display: inline-flex;
    width: 40px;
    height: 22px;
    flex: 0 0 40px;
    cursor: pointer;
}

.admin-points-switch input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.admin-points-switch span {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d8dde5;
    transition: .18s ease;
}

.admin-points-switch span::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 3px;
    left: 3px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .18);
    transition: .18s ease;
}

.admin-points-switch input:checked + span {
    background: var(--admin-primary, #d97706);
}

.admin-points-switch input:checked + span::after {
    transform: translateX(18px);
}


/* Save Bar */

.admin-save-bar {
    position: sticky;
    bottom: 15px;
    z-index: 20;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    box-shadow: 0 10px 30px rgba(16, 24, 40, .10);
}

.admin-save-bar strong {
    display: block;
    color: var(--admin-text);
    font-size: 14.5px;
}

.admin-save-bar small {
    display: block;
    margin-top: 3px;
    color: var(--admin-text-muted);
    font-size: 13px;
}

@media (max-width: 600px) {
    .admin-save-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .admin-save-bar .admin-button {
        width: 100%;
    }
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const rewardType = document.getElementById('bookingRewardType');

    if (!rewardType) {
        return;
    }

    const amountFields = document.querySelectorAll('.amount-based-field');

    function updateRewardFields() {

        const isAmountBased = rewardType.value === 'amount_based';

        amountFields.forEach(function (field) {

            field.style.opacity = isAmountBased ? '1' : '.45';

            const input = field.querySelector('input');

            if (input) {
                input.disabled = !isAmountBased;
            }

        });
    }

    rewardType.addEventListener('change', updateRewardFields);

    updateRewardFields();

});
</script>
@endsection
