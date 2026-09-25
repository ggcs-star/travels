@extends('admin.layouts.app')

@section('title', 'Add / Deduct Points')

@section('description', 'Add or deduct points from this user\'s wallet.')

@section('content')

<div class="admin-wallet-detail">

    {{-- Breadcrumb --}}
    <div class="wallet-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <span>›</span>
        <a href="{{ route('admin.point-wallets.index') }}">Points Wallets</a>
        <span>›</span>
        <strong>Details</strong>
    </div>


    {{-- Header --}}
    <div class="wallet-detail-header">

        <svg class="wallet-detail-header__art" viewBox="0 0 200 90" fill="none">
            <path d="M0 90 30 55 55 78 90 40 130 78 160 50 200 90Z" fill="currentColor" opacity=".5"/>
            <circle cx="168" cy="26" r="16" fill="currentColor" opacity=".35"/>
            <path d="M120 30 178 10 172 20 190 24 178 28 182 40Z" fill="currentColor" opacity=".55"/>
        </svg>

        <div>

            <div class="wallet-profile-heading">

                <div class="wallet-large-avatar">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>

                <div>

                    <h1>
                        {{ $user->name }}
                    </h1>

                    <p>

                        {{ $user->email }}
                        <span>·</span>
                        <a href="{{ route('admin.point-wallets.transactions', $user) }}">
                            View transaction history →
                        </a>

                    </p>

                </div>

            </div>

        </div>


        <div class="wallet-user-status">

            <span class="status-dot"></span>

            {{ $user->isActive() ? 'Active User' : 'Inactive User' }}

        </div>

    </div>


    {{-- Success --}}
    @if(session('success'))

        <div class="wallet-alert success">

            <span>✓</span>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- Errors --}}
    @if($errors->any())

        <div class="wallet-alert error">

            <span>!</span>

            <div>

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- Statistics --}}
    <div class="wallet-stats">

        <div class="wallet-stat main">

            <span class="wallet-stat__icon wallet-stat__icon--onmain">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/><circle cx="16" cy="13" r="1.3" fill="currentColor" stroke="none"/></svg>
            </span>

            <span>Current Balance</span>

            <strong>
                {{ number_format($wallet->balance ?? 0) }}
            </strong>

            <small>
                Available points
            </small>

        </div>


        <div class="wallet-stat">

            <span class="wallet-stat__icon wallet-stat__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9 9.5a2.5 2.5 0 0 1 2.5-1.5h1a2.5 2.5 0 0 1 0 5h-1a2.5 2.5 0 0 0 0 5h1a2.5 2.5 0 0 0 2.5-1.5"/></svg>
            </span>

            <span>Total Earned</span>

            <strong>
                {{ number_format($wallet->total_earned ?? 0) }}
            </strong>

            <small>
                All earned points
            </small>

        </div>


        <div class="wallet-stat">

            <span class="wallet-stat__icon wallet-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg>
            </span>

            <span>Total Redeemed</span>

            <strong>
                {{ number_format($wallet->total_redeemed ?? 0) }}
            </strong>

            <small>
                Used by user
            </small>

        </div>


        <div class="wallet-stat">

            <span class="wallet-stat__icon wallet-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12M6 22h12M6 2c0 6 12 6 12 10s-12 4-12 10M18 2c0 6-12 6-12 10s12 4 12 10"/></svg>
            </span>

            <span>Total Expired</span>

            <strong>
                {{ number_format($wallet->total_expired ?? 0) }}
            </strong>

            <small>
                Expired points
            </small>

        </div>


        <div class="wallet-stat">

            <span class="wallet-stat__icon wallet-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
            </span>

            <span>Total Adjusted</span>

            <strong>
                {{ number_format($wallet->total_adjusted ?? 0) }}
            </strong>

            <small>
                Admin adjustments
            </small>

        </div>

    </div>


    {{-- Main Content --}}
    <div class="wallet-content-grid">


        {{-- Adjustment --}}
        <div class="wallet-adjust-card" id="adjust-points">

            <div class="wallet-card-heading">

                <div class="adjust-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
                </div>

                <div>

                    <h2>
                        Adjust Points
                    </h2>

                    <p>
                        Add or deduct points from this user's wallet.
                    </p>

                </div>

            </div>


            <div class="adjust-warning">

                <span>ℹ</span>

                <div>

                    <strong>
                        Admin adjustment
                    </strong>

                    <p>
                        Every adjustment is permanently recorded in the
                        points ledger with the reason and admin details.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.point-wallets.adjust', $user) }}"
                class="adjust-form"
            >

                @csrf


                {{-- Action --}}
                <div class="adjust-action">

                    <label class="adjust-option add-option">

                        <input
                            type="radio"
                            name="action"
                            value="add"
                            {{ old('action', 'add') === 'add' ? 'checked' : '' }}
                        >

                        <span class="adjust-option__icon adjust-option__icon--add">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </span>

                        <div>

                            <strong>
                                Add Points
                            </strong>

                            <small>
                                Credit points to wallet
                            </small>

                        </div>

                    </label>


                    <label class="adjust-option deduct-option">

                        <input
                            type="radio"
                            name="action"
                            value="deduct"
                            {{ old('action') === 'deduct' ? 'checked' : '' }}
                        >

                        <span class="adjust-option__icon adjust-option__icon--deduct">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14"/></svg>
                        </span>

                        <div>

                            <strong>
                                Deduct Points
                            </strong>

                            <small>
                                Remove points from wallet
                            </small>

                        </div>

                    </label>

                </div>


                {{-- Points --}}
                <div class="form-field">

                    <label for="points">
                        Points
                    </label>

                    <div class="form-field__input-wrap">

                        <input
                            id="points"
                            type="number"
                            name="points"
                            min="1"
                            max="1000000000"
                            value="{{ old('points') }}"
                            placeholder="Enter points"
                            required
                        >

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18M7 6h1v4M16.71 13.88l.7.71-2.82 2.82"/></svg>

                    </div>

                </div>


                {{-- Reason --}}
                <div class="form-field">

                    <label for="reason">
                        Reason
                    </label>

                    <div class="form-field__input-wrap form-field__input-wrap--textarea">

                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            maxlength="500"
                            placeholder="Enter the reason for this adjustment..."
                            required
                        >{{ old('reason') }}</textarea>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>

                    </div>

                    <small>
                        Required for audit history.
                    </small>

                </div>


                <button
                    type="submit"
                    class="adjust-submit"
                    id="adjustSubmit"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 5v14M5 12h14"/></svg>
                    <span id="adjustSubmitText">Add Points</span>
                </button>

            </form>

        </div>


        {{-- Wallet Summary --}}
        <div class="wallet-summary-card">

            <div class="wallet-card-heading">

                <div class="adjust-icon adjust-icon--mint">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/><circle cx="16" cy="13" r="1.3" fill="currentColor" stroke="none"/></svg>
                </div>

                <div>

                    <h2>
                        Wallet Summary
                    </h2>

                    <p>
                        Current points overview.
                    </p>

                </div>

            </div>


            <div class="summary-row">

                <span class="summary-row__icon summary-row__icon--gold">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="8"/><path d="M12 8v8M9 10a2 2 0 0 1 2-1h1.5a2 2 0 0 1 0 4H11a2 2 0 0 0 0 4h1.5a2 2 0 0 0 2-1"/></svg>
                </span>

                <span class="summary-row__label">
                    Current Balance
                </span>

                <strong>
                    {{ number_format($wallet->balance ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span class="summary-row__icon summary-row__icon--green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                </span>

                <span class="summary-row__label">
                    Total Earned
                </span>

                <strong>
                    {{ number_format($wallet->total_earned ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span class="summary-row__icon summary-row__icon--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14"/></svg>
                </span>

                <span class="summary-row__label">
                    Total Redeemed
                </span>

                <strong>
                    {{ number_format($wallet->total_redeemed ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span class="summary-row__icon summary-row__icon--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
                </span>

                <span class="summary-row__label">
                    Total Expired
                </span>

                <strong>
                    {{ number_format($wallet->total_expired ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span class="summary-row__icon summary-row__icon--orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
                </span>

                <span class="summary-row__label">
                    Admin Adjustments
                </span>

                <strong>
                    {{ number_format($wallet->total_adjusted ?? 0) }}
                </strong>

            </div>


            <div class="wallet-info-box">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>

                <div>
                    <strong>Points Information</strong>
                    <p>Points are used for special rewards and discounts on your bookings.</p>
                </div>

            </div>

        </div>

    </div>


</div>


<style>

.admin-wallet-detail {
    max-width: 1400px;
    margin: 0 auto;
    padding: 28px 28px 70px;
}

#adjust-points {
    scroll-margin-top: 90px;
}


/* Breadcrumb */

.wallet-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    color: #9aa2ae;
    font-size: 13px;
}

.wallet-breadcrumb a {
    color: #7d8796;
    text-decoration: none;
}

.wallet-breadcrumb a:hover {
    color: var(--admin-primary, #d97706);
}

.wallet-breadcrumb strong {
    color: #344054;
}


/* Header */

.wallet-detail-header {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
    padding: 22px 24px;
    border: 1px solid #e5e8ed;
    border-radius: 13px;
    background: #fff;
}

.wallet-detail-header__art {
    position: absolute;
    top: 0;
    right: 0;
    width: 45%;
    height: 100%;
    color: var(--admin-sidebar, #f5faf6);
    pointer-events: none;
}

.wallet-detail-header > div,
.wallet-user-status {
    position: relative;
}

.wallet-profile-heading {
    display: flex;
    align-items: center;
    gap: 13px;
}

.wallet-large-avatar {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: var(--admin-sidebar, #f5faf6);
    color: var(--admin-sidebar-dark, #14532d);
    font-size: 16px;
    font-weight: 750;
}

.wallet-profile-heading h1 {
    margin: 0;
    color: #172033;
    font-size: 25px;
    font-weight: 750;
}

.wallet-profile-heading p {
    margin: 4px 0 0;
    color: #8992a0;
    font-size: 12px;
}

.wallet-profile-heading p span {
    margin: 0 5px;
    color: #c3c8d0;
}

.wallet-profile-heading p a {
    color: var(--admin-primary, #d97706);
    font-weight: 650;
    text-decoration: none;
}

.wallet-profile-heading p a:hover {
    text-decoration: underline;
}

.wallet-user-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 11px;
    border: 1px solid #e1e6eb;
    border-radius: 999px;
    background: #fff;
    color: #687384;
    font-size: 11px;
    font-weight: 650;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #29a66f;
}


/* Alerts */

.wallet-alert {
    display: flex;
    gap: 10px;
    padding: 12px 14px;
    margin-bottom: 20px;
    border-radius: 9px;
    font-size: 12px;
}

.wallet-alert.success {
    background: #eefaf4;
    border: 1px solid #ccebdc;
    color: #176b48;
}

.wallet-alert.error {
    background: #fff3f3;
    border: 1px solid #ffd7d7;
    color: #a32e2e;
}


/* Stats */

.wallet-stats {
    display: grid;
    grid-template-columns: 1.35fr repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.wallet-stat {
    position: relative;
    min-width: 0;
    padding: 18px;
    border: 1px solid #e0e6de;
    border-radius: 13px;
    background: var(--admin-sidebar, #f5faf6);
}

.wallet-stat__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    margin-bottom: 10px;
    border-radius: 9px;
}

.wallet-stat__icon svg {
    width: 17px;
    height: 17px;
}

.wallet-stat__icon--onmain {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.wallet-stat__icon--green {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.wallet-stat__icon--orange {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.wallet-stat > span:not(.wallet-stat__icon) {
    display: block;
    color: var(--admin-sidebar-dark, #14532d);
    font-size: 13px;
    font-weight: 700;
}

.wallet-stat strong {
    display: block;
    margin-top: 7px;
    color: #1b2537;
    font-size: 23px;
    font-weight: 750;
}

.wallet-stat small {
    display: block;
    margin-top: 4px;
    color: #6b7d6c;
    font-size: 12px;
}


/* Main Content */

.wallet-content-grid {
    display: grid;
    grid-template-columns: 1.35fr .65fr;
    gap: 18px;
    margin-bottom: 20px;
}

.wallet-adjust-card,
.wallet-summary-card {
    border: 1px solid #e5e8ed;
    border-radius: 13px;
    background: #fff;
}

.wallet-adjust-card,
.wallet-summary-card {
    padding: 20px;
}

.wallet-card-heading {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 18px;
}

.wallet-card-heading h2 {
    margin: 0;
    color: #202b3e;
    font-size: 17px;
    font-weight: 750;
}

.wallet-card-heading p {
    margin: 4px 0 0;
    color: #929aa7;
    font-size: 13px;
}

.adjust-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.adjust-icon svg {
    width: 18px;
    height: 18px;
}

.adjust-icon--mint {
    background: var(--admin-sidebar, #f5faf6);
    color: var(--admin-sidebar-dark, #14532d);
}


/* Warning */

.adjust-warning {
    display: flex;
    gap: 10px;
    padding: 11px 12px;
    margin-bottom: 18px;
    border: 1px solid #eceff2;
    border-radius: 9px;
    background: #fafbfc;
}

.adjust-warning > span {
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eef0f3;
    color: #5d6878;
    font-size: 10px;
    font-weight: 750;
}

.adjust-warning strong {
    display: block;
    color: #3b4657;
    font-size: 12px;
}

.adjust-warning p {
    margin: 3px 0 0;
    color: #8d96a3;
    font-size: 12px;
    line-height: 1.5;
}


/* Action */

.adjust-action {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 17px;
}

.adjust-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px;
    border: 1px solid #e1e5ea;
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease;
}

.adjust-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.adjust-option__icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f1f3f5;
    color: #98a1ae;
}

.adjust-option__icon svg {
    width: 15px;
    height: 15px;
}

.adjust-option:has(input:checked).add-option {
    border-color: var(--admin-primary, #d97706);
    background: var(--admin-primary, #d97706);
}

.adjust-option:has(input:checked).add-option strong,
.adjust-option:has(input:checked).add-option small {
    color: #fff;
}

.adjust-option:has(input:checked).add-option .adjust-option__icon {
    background: rgba(255, 255, 255, .22);
    color: #fff;
}

.adjust-option:has(input:checked).deduct-option {
    border-color: var(--admin-primary, #d97706);
    background: var(--admin-primary-soft, #fff7ed);
}

.adjust-option:has(input:checked).deduct-option .adjust-option__icon {
    background: #fff;
    color: var(--admin-primary, #d97706);
}

.adjust-option strong {
    display: block;
    color: #344054;
    font-size: 12.5px;
}

.adjust-option small {
    display: block;
    margin-top: 2px;
    color: #939ba7;
    font-size: 11px;
}


/* Form */

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 6px;
    color: #4b5666;
    font-size: 12px;
    font-weight: 700;
}

.form-field__input-wrap {
    position: relative;
}

.form-field__input-wrap svg {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 16px;
    height: 16px;
    color: #b7bec8;
    pointer-events: none;
}

.form-field__input-wrap--textarea svg {
    top: auto;
    bottom: 12px;
}

.form-field input,
.form-field textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #dfe3e8;
    border-radius: 8px;
    outline: none;
    background: #fff;
    color: #344054;
    font-family: inherit;
    font-size: 13px;
}

.form-field input {
    height: 42px;
    padding: 0 36px 0 12px;
}

.form-field textarea {
    min-height: 95px;
    padding: 10px 36px 10px 12px;
    resize: vertical;
}

.form-field input:focus,
.form-field textarea:focus {
    border-color: var(--admin-primary, #d97706);
    box-shadow: 0 0 0 3px rgba(217, 119, 6, .08);
}

.form-field > small {
    display: block;
    margin-top: 5px;
    color: #9aa2ae;
    font-size: 12px;
}

.adjust-submit {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border: 0;
    border-radius: 8px;
    background: var(--admin-primary, #d97706);
    color: #fff;
    cursor: pointer;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
}

.adjust-submit svg {
    width: 17px;
    height: 17px;
}

.adjust-submit:hover {
    background: var(--admin-primary-dark, #b45309);
}


/* Summary */

.summary-row {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 13px 0;
    border-bottom: 1px solid #edf0f3;
}

.summary-row:last-of-type {
    border-bottom: 0;
}

.summary-row__icon {
    width: 28px;
    height: 28px;
    flex: 0 0 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.summary-row__icon svg {
    width: 13px;
    height: 13px;
}

.summary-row__icon--gold {
    background: #fff8e1;
    color: #b7871a;
}

.summary-row__icon--green {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.summary-row__icon--orange {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.summary-row__label {
    flex: 1;
    color: #808a98;
    font-size: 13px;
}

.summary-row strong {
    color: #344054;
    font-size: 14.5px;
    font-weight: 700;
}

.wallet-info-box {
    display: flex;
    gap: 10px;
    margin-top: 16px;
    padding: 14px;
    border-radius: 10px;
    background: var(--admin-success-bg, #ecfdf3);
}

.wallet-info-box svg {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
    margin-top: 2px;
    color: var(--admin-success, #15803d);
}

.wallet-info-box strong {
    display: block;
    color: var(--admin-success, #15803d);
    font-size: 12.5px;
}

.wallet-info-box p {
    margin: 3px 0 0;
    color: #2f7a53;
    font-size: 11.5px;
    line-height: 1.5;
}


/* Responsive */

@media (max-width: 900px) {

    .wallet-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .wallet-stat.main {
        grid-column: span 2;
    }

    .wallet-content-grid {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 600px) {

    .admin-wallet-detail {
        padding: 20px 14px 50px;
    }

    .wallet-detail-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .wallet-stats {
        grid-template-columns: 1fr;
    }

    .wallet-stat.main {
        grid-column: auto;
    }

    .adjust-action {
        grid-template-columns: 1fr;
    }

}

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const submitButton =
        document.getElementById('adjustSubmitText');

    const radios =
        document.querySelectorAll('input[name="action"]');

    function updateButton() {

        const selected =
            document.querySelector(
                'input[name="action"]:checked'
            );

        if (!selected || !submitButton) {
            return;
        }

        if (selected.value === 'deduct') {

            submitButton.textContent =
                'Deduct Points';

        } else {

            submitButton.textContent =
                'Add Points';

        }

    }

    radios.forEach(function (radio) {

        radio.addEventListener(
            'change',
            updateButton
        );

    });

    updateButton();

});

</script>

@endsection
