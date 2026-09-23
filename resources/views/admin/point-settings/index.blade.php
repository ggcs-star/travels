@extends('admin.layouts.app')

@section('title', 'Points Management')

@section('description', 'Control how users earn, redeem and use reward points.')

@section('content')
<div class="points-settings-page">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="points-alert points-alert-success">
            <span class="points-alert-icon">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="points-alert points-alert-error">
            <span class="points-alert-icon">!</span>

            <div>
                <strong>Please check the following:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
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
        <div class="points-section-heading">
            <div class="points-section-icon">✦</div>

            <div>
                <h2>Reward Settings</h2>
                <p>Configure how users earn points.</p>
            </div>
        </div>


        <div class="points-settings-grid">


            {{-- Registration Reward --}}
            @php
                $registration = $settings->get(\App\Models\PointSetting::REGISTRATION_REWARD);
            @endphp

            @if($registration)
                <div class="points-card">

                    <div class="points-card-top">
                        <div class="points-card-title">
                            <div class="points-icon-box">＋</div>

                            <div>
                                <h3>New Joining Reward</h3>
                                <p>Reward new users when they register.</p>
                            </div>
                        </div>

                        <label class="points-switch">
                            <input
                                type="checkbox"
                                name="settings[{{ $registration->key }}][enabled]"
                                value="1"
                                {{ $registration->enabled ? 'checked' : '' }}
                            >
                            <span></span>
                        </label>
                    </div>

                    <div class="points-divider"></div>

                    <div class="points-field">
                        <label>Reward Points</label>

                        <div class="points-input-wrap">
                            <input
                                type="number"
                                min="0"
                                name="settings[{{ $registration->key }}][points]"
                                value="{{ old(
                                    'settings.' . $registration->key . '.points',
                                    $registration->points
                                ) }}"
                            >

                            <span>Points</span>
                        </div>
                    </div>

                </div>
            @endif



            {{-- Booking Reward --}}
            @php
                $booking = $settings->get(\App\Models\PointSetting::BOOKING_REWARD);
            @endphp

            @if($booking)
                <div class="points-card points-card-wide">

                    <div class="points-card-top">
                        <div class="points-card-title">
                            <div class="points-icon-box">◈</div>

                            <div>
                                <h3>Booking Reward</h3>
                                <p>Configure points earned from successful bookings.</p>
                            </div>
                        </div>

                        <label class="points-switch">
                            <input
                                type="checkbox"
                                name="settings[{{ $booking->key }}][enabled]"
                                value="1"
                                {{ $booking->enabled ? 'checked' : '' }}
                            >
                            <span></span>
                        </label>
                    </div>

                    <div class="points-divider"></div>


                    <div class="points-form-grid">

                        {{-- Reward Type --}}
                        <div class="points-field">
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
                        <div class="points-field">
                            <label>Calculation Based On</label>

                            <select
                                name="settings[{{ $booking->key }}][calculation_basis]"
                            >
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
                        <div class="points-field">
                            <label>Points</label>

                            <div class="points-input-wrap">
                                <input
                                    type="number"
                                    min="0"
                                    name="settings[{{ $booking->key }}][points]"
                                    value="{{ old(
                                        'settings.' . $booking->key . '.points',
                                        $booking->points
                                    ) }}"
                                >

                                <span>Points</span>
                            </div>
                        </div>


                        {{-- Minimum Amount --}}
                        <div class="points-field amount-based-field">
                            <label>Minimum Amount</label>

                            <div class="points-input-wrap">
                                <span class="points-prefix">₹</span>

                                <input
                                    type="number"
                                    min="0"
                                    name="settings[{{ $booking->key }}][minimum_amount]"
                                    value="{{ old(
                                        'settings.' . $booking->key . '.minimum_amount',
                                        $booking->minimum_amount
                                    ) }}"
                                >
                            </div>

                            <small>
                                Minimum booking amount required for reward.
                            </small>
                        </div>


                        {{-- Amount Unit --}}
                        <div class="points-field amount-based-field">
                            <label>Amount Unit</label>

                            <div class="points-input-wrap">
                                <span class="points-prefix">₹</span>

                                <input
                                    type="number"
                                    min="1"
                                    name="settings[{{ $booking->key }}][amount_unit]"
                                    value="{{ old(
                                        'settings.' . $booking->key . '.amount_unit',
                                        $booking->amount_unit
                                    ) }}"
                                >
                            </div>

                            <small>
                                Example: ₹100 = configured points.
                            </small>
                        </div>


                        {{-- Maximum Points --}}
                        <div class="points-field">
                            <label>Maximum Points Per Booking</label>

                            <div class="points-input-wrap">
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

                                <span>Points</span>
                            </div>

                            <small>
                                Leave empty for unlimited.
                            </small>
                        </div>

                    </div>

                </div>
            @endif



            {{-- Review Reward --}}
            @php
                $review = $settings->get(\App\Models\PointSetting::REVIEW_REWARD);
            @endphp

            @if($review)
                <div class="points-card">

                    <div class="points-card-top">
                        <div class="points-card-title">
                            <div class="points-icon-box">★</div>

                            <div>
                                <h3>Review Reward</h3>
                                <p>Reward users for submitting reviews.</p>
                            </div>
                        </div>

                        <label class="points-switch">
                            <input
                                type="checkbox"
                                name="settings[{{ $review->key }}][enabled]"
                                value="1"
                                {{ $review->enabled ? 'checked' : '' }}
                            >
                            <span></span>
                        </label>
                    </div>

                    <div class="points-divider"></div>

                    <div class="points-field">
                        <label>Reward Points</label>

                        <div class="points-input-wrap">
                            <input
                                type="number"
                                min="0"
                                name="settings[{{ $review->key }}][points]"
                                value="{{ old(
                                    'settings.' . $review->key . '.points',
                                    $review->points
                                ) }}"
                            >

                            <span>Points</span>
                        </div>
                    </div>

                </div>
            @endif



            {{-- Referral Reward --}}
            @php
                $referral = $settings->get(\App\Models\PointSetting::REFERRAL_REWARD);
            @endphp

            @if($referral)
                <div class="points-card">

                    <div class="points-card-top">
                        <div class="points-card-title">
                            <div class="points-icon-box">↗</div>

                            <div>
                                <h3>Referral Reward</h3>
                                <p>Reward users for successful referrals.</p>
                            </div>
                        </div>

                        <label class="points-switch">
                            <input
                                type="checkbox"
                                name="settings[{{ $referral->key }}][enabled]"
                                value="1"
                                {{ $referral->enabled ? 'checked' : '' }}
                            >
                            <span></span>
                        </label>
                    </div>

                    <div class="points-divider"></div>

                    <div class="points-field">
                        <label>Referral Points</label>

                        <div class="points-input-wrap">
                            <input
                                type="number"
                                min="0"
                                name="settings[{{ $referral->key }}][points]"
                                value="{{ old(
                                    'settings.' . $referral->key . '.points',
                                    $referral->points
                                ) }}"
                            >

                            <span>Points</span>
                        </div>
                    </div>

                </div>
            @endif

        </div>



        {{-- =========================
            REDEMPTION
        ========================== --}}
        @php
            $redemption = $booking;
        @endphp

        @if($redemption)

            <div class="points-section-heading points-section-spacing">
                <div class="points-section-icon">◎</div>

                <div>
                    <h2>Booking Redemption</h2>
                    <p>Control how users can use their points while booking.</p>
                </div>
            </div>


            <div class="points-card points-card-wide">

                <div class="points-card-top">

                    <div class="points-card-title">

                        <div class="points-icon-box">₹</div>

                        <div>
                            <h3>Use Points on Booking</h3>
                            <p>
                                Allow users to convert their points into booking discount.
                            </p>
                        </div>

                    </div>

                    <label class="points-switch">
                        <input
                            type="checkbox"
                            name="settings[{{ $redemption->key }}][redemption_enabled]"
                            value="1"
                            {{ $redemption->redemption_enabled ? 'checked' : '' }}
                        >

                        <span></span>
                    </label>

                </div>


                <div class="points-divider"></div>


                <div class="points-form-grid">

                    <div class="points-field">

                        <label>1 Point Value</label>

                        <div class="points-input-wrap">

                            <span class="points-prefix">₹</span>

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

                        </div>

                        <small>
                            Example: 1 point = ₹1.
                        </small>

                    </div>


                    <div class="points-field">

                        <label>Maximum Redemption</label>

                        <div class="points-input-wrap">

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

                            <span>%</span>

                        </div>

                        <small>
                            Maximum percentage of booking amount payable using points.
                        </small>

                    </div>

                </div>

            </div>

        @endif



        {{-- =========================
            EXPIRY
        ========================== --}}
        @if($booking)

            <div class="points-section-heading points-section-spacing">
                <div class="points-section-icon">◷</div>

                <div>
                    <h2>Points Expiry</h2>
                    <p>Automatically control the lifetime of earned points.</p>
                </div>
            </div>


            <div class="points-card points-card-wide">

                <div class="points-card-top">

                    <div class="points-card-title">

                        <div class="points-icon-box">⌛</div>

                        <div>
                            <h3>Enable Points Expiry</h3>
                            <p>
                                Expire unused points automatically after a defined period.
                            </p>
                        </div>

                    </div>


                    <label class="points-switch">

                        <input
                            type="checkbox"
                            name="settings[{{ $booking->key }}][expiry_enabled]"
                            value="1"
                            {{ $booking->expiry_enabled ? 'checked' : '' }}
                        >

                        <span></span>

                    </label>

                </div>


                <div class="points-divider"></div>


                <div class="points-field points-expiry-field">

                    <label>Expiry Period</label>

                    <div class="points-input-wrap">

                        <input
                            type="number"
                            min="1"
                            name="settings[{{ $booking->key }}][expiry_days]"
                            value="{{ old(
                                'settings.' . $booking->key . '.expiry_days',
                                $booking->expiry_days
                            ) }}"
                        >

                        <span>Days</span>

                    </div>

                    <small>
                        Points will expire after this many days.
                    </small>

                </div>

            </div>

        @endif



        {{-- =========================
            SAVE BAR
        ========================== --}}
        <div class="points-save-bar">

            <div>
                <strong>Points configuration</strong>
                <span>Changes will apply to future point transactions.</span>
            </div>

            <button
                type="submit"
                class="points-save-btn"
            >
                <span>Save Changes</span>
                <span class="points-save-arrow">→</span>
            </button>

        </div>

    </form>

</div>


<style>
/* =========================================================
   POINTS MANAGEMENT
========================================================= */

.points-settings-page {
    max-width: 1280px;
    margin: 0 auto;
    padding: 30px 28px 70px;
}

.points-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 30px;
}

.points-breadcrumb {
    color: #8b95a7;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 9px;
}

.points-breadcrumb span {
    margin: 0 7px;
    color: #c2c8d1;
}

.points-page-header h1 {
    margin: 0;
    font-size: 30px;
    line-height: 1.2;
    font-weight: 750;
    color: #172033;
    letter-spacing: -0.5px;
}

.points-page-header p {
    margin: 9px 0 0;
    color: #7b8495;
    font-size: 14px;
}

.points-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    border: 1px solid #e3e7ed;
    border-radius: 999px;
    background: #fff;
    color: #626c7c;
    font-size: 12px;
    font-weight: 650;
    white-space: nowrap;
}

.points-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22a06b;
    box-shadow: 0 0 0 4px rgba(34, 160, 107, .10);
}


/* Alerts */

.points-alert {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 13px 15px;
    margin-bottom: 24px;
    border-radius: 10px;
    font-size: 13px;
}

.points-alert-success {
    background: #eefaf4;
    border: 1px solid #cceede;
    color: #176b48;
}

.points-alert-error {
    background: #fff3f3;
    border: 1px solid #ffd7d7;
    color: #a92e2e;
}

.points-alert-icon {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 22px;
    border-radius: 50%;
    background: rgba(0, 0, 0, .05);
    font-weight: 800;
}

.points-alert ul {
    margin: 6px 0 0 17px;
    padding: 0;
}


/* Section */

.points-section-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin: 4px 0 17px;
}

.points-section-spacing {
    margin-top: 42px;
}

.points-section-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f2f4f7;
    border: 1px solid #e4e7ec;
    color: #273348;
    font-size: 17px;
}

.points-section-heading h2 {
    margin: 0;
    color: #1b2537;
    font-size: 18px;
    font-weight: 720;
}

.points-section-heading p {
    margin: 3px 0 0;
    color: #8a93a2;
    font-size: 12px;
}


/* Cards */

.points-settings-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.points-card {
    min-width: 0;
    background: #fff;
    border: 1px solid #e5e8ed;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(16, 24, 40, .025);
}

.points-card-wide {
    grid-column: 1 / -1;
}

.points-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.points-card-title {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.points-icon-box {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 38px;
    border-radius: 10px;
    background: #f5f6f8;
    border: 1px solid #e5e8ec;
    color: #273348;
    font-size: 17px;
    font-weight: 700;
}

.points-card-title h3 {
    margin: 1px 0 3px;
    color: #1d2738;
    font-size: 15px;
    font-weight: 720;
}

.points-card-title p {
    margin: 0;
    color: #8a93a2;
    font-size: 12px;
    line-height: 1.5;
}

.points-divider {
    height: 1px;
    background: #edf0f3;
    margin: 19px 0;
}


/* Switch */

.points-switch {
    position: relative;
    display: inline-flex;
    width: 43px;
    height: 24px;
    flex: 0 0 43px;
    cursor: pointer;
}

.points-switch input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.points-switch span {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d8dde5;
    transition: .2s ease;
}

.points-switch span::after {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    top: 3px;
    left: 3px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.18);
    transition: .2s ease;
}

.points-switch input:checked + span {
    background: #263247;
}

.points-switch input:checked + span::after {
    transform: translateX(19px);
}


/* Fields */

.points-form-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.points-field {
    min-width: 0;
}

.points-field label {
    display: block;
    margin-bottom: 7px;
    color: #4d586b;
    font-size: 12px;
    font-weight: 650;
}

.points-field select,
.points-field input {
    width: 100%;
    height: 42px;
    box-sizing: border-box;
    border: 1px solid #dfe3e9;
    border-radius: 9px;
    outline: none;
    background: #fff;
    color: #202b3e;
    font-size: 13px;
    padding: 0 12px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.points-field select:focus,
.points-field input:focus {
    border-color: #8993a3;
    box-shadow: 0 0 0 3px rgba(40, 52, 72, .06);
}

.points-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.points-input-wrap input {
    padding-right: 70px;
}

.points-input-wrap > span:not(.points-prefix) {
    position: absolute;
    right: 12px;
    color: #8a93a2;
    font-size: 11px;
    font-weight: 650;
}

.points-prefix {
    position: absolute;
    left: 12px;
    color: #717b8c;
    font-size: 13px;
    z-index: 2;
}

.points-input-wrap:has(.points-prefix) input {
    padding-left: 28px;
    padding-right: 12px;
}

.points-field small {
    display: block;
    margin-top: 6px;
    color: #9aa2af;
    font-size: 11px;
    line-height: 1.4;
}


/* Save */

.points-save-bar {
    position: sticky;
    bottom: 15px;
    z-index: 20;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    margin-top: 32px;
    padding: 14px 16px 14px 20px;

    border: 1px solid #e0e4ea;
    border-radius: 13px;

    background: rgba(255, 255, 255, .96);
    box-shadow: 0 10px 30px rgba(16, 24, 40, .10);

    backdrop-filter: blur(10px);
}

.points-save-bar strong {
    display: block;
    color: #202b3e;
    font-size: 13px;
}

.points-save-bar > div > span {
    display: block;
    margin-top: 3px;
    color: #8b94a2;
    font-size: 11px;
}

.points-save-btn {
    height: 42px;
    display: inline-flex;
    align-items: center;
    gap: 14px;
    padding: 0 17px;
    border: 0;
    border-radius: 9px;
    background: var(--admin-primary, #d97706);
    color: #fff;
    cursor: pointer;
    font-size: 13px;
    font-weight: 650;
    transition: transform .18s ease, background .18s ease;
}

.points-save-btn span {
    color: #fff;
}

.points-save-btn:hover {
    background: var(--admin-primary-dark, #b45309);
    transform: translateY(-1px);
}

.points-save-arrow {
    margin: 0 !important;
    color: #fff !important;
    font-size: 16px !important;
}


/* Responsive */

@media (max-width: 1000px) {

    .points-form-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

@media (max-width: 760px) {

    .points-settings-page {
        padding: 22px 16px 60px;
    }

    .points-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .points-settings-grid {
        grid-template-columns: 1fr;
    }

    .points-card-wide {
        grid-column: auto;
    }

    .points-form-grid {
        grid-template-columns: 1fr;
    }

    .points-save-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .points-save-btn {
        width: 100%;
        justify-content: center;
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