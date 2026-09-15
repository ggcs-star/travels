@extends('admin.layouts.app')
@section('content')

<div class="admin-wallet-detail">

    {{-- Header --}}
    <div class="wallet-detail-header">

        <div>

            <a
                href="{{ route('admin.point-wallets.index') }}"
                class="wallet-back"
            >
                ← Back to Wallets
            </a>

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

                        @if($user->username)
                            <span>•</span>
                            @{{ $user->username }}
                        @endif

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

            <span>Current Balance</span>

            <strong>
                {{ number_format($wallet->balance ?? 0) }}
            </strong>

            <small>
                Available points
            </small>

        </div>


        <div class="wallet-stat">

            <span>Total Earned</span>

            <strong>
                {{ number_format($wallet->total_earned ?? 0) }}
            </strong>

            <small>
                All earned points
            </small>

        </div>


        <div class="wallet-stat">

            <span>Total Redeemed</span>

            <strong>
                {{ number_format($wallet->total_redeemed ?? 0) }}
            </strong>

            <small>
                Used by user
            </small>

        </div>


        <div class="wallet-stat">

            <span>Total Expired</span>

            <strong>
                {{ number_format($wallet->total_expired ?? 0) }}
            </strong>

            <small>
                Expired points
            </small>

        </div>


        <div class="wallet-stat">

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
        <div class="wallet-adjust-card">

            <div class="wallet-card-heading">

                <div>

                    <h2>
                        Adjust Points
                    </h2>

                    <p>
                        Add or deduct points from this user's wallet.
                    </p>

                </div>

                <div class="adjust-icon">
                    ±
                </div>

            </div>


            <div class="adjust-warning">

                <span>!</span>

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

                        <span class="adjust-radio"></span>

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

                        <span class="adjust-radio"></span>

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

                </div>


                {{-- Reason --}}
                <div class="form-field">

                    <label for="reason">
                        Reason
                    </label>

                    <textarea
                        id="reason"
                        name="reason"
                        rows="4"
                        maxlength="500"
                        placeholder="Enter the reason for this adjustment..."
                        required
                    >{{ old('reason') }}</textarea>

                    <small>
                        Required for audit history.
                    </small>

                </div>


                <button
                    type="submit"
                    class="adjust-submit"
                    id="adjustSubmit"
                >
                    Add Points
                </button>

            </form>

        </div>


        {{-- Wallet Summary --}}
        <div class="wallet-summary-card">

            <div class="wallet-card-heading">

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

                <span>
                    Current Balance
                </span>

                <strong>
                    {{ number_format($wallet->balance ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Total Earned
                </span>

                <strong>
                    {{ number_format($wallet->total_earned ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Total Redeemed
                </span>

                <strong>
                    {{ number_format($wallet->total_redeemed ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Total Expired
                </span>

                <strong>
                    {{ number_format($wallet->total_expired ?? 0) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Admin Adjustments
                </span>

                <strong>
                    {{ number_format($wallet->total_adjusted ?? 0) }}
                </strong>

            </div>

        </div>

    </div>


    {{-- Transactions --}}
    <div class="wallet-transactions-card">

        <div class="wallet-card-heading">

            <div>

                <h2>
                    Transaction History
                </h2>

                <p>
                    Complete points ledger for this user.
                </p>

            </div>

        </div>


        <div class="transaction-table-wrap">

            <table class="transaction-table">

                <thead>

                    <tr>

                        <th>
                            Date
                        </th>

                        <th>
                            Type
                        </th>

                        <th>
                            Direction
                        </th>

                        <th>
                            Points
                        </th>

                        <th>
                            Balance
                        </th>

                        <th>
                            Source
                        </th>

                        <th>
                            Description
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($transactions as $transaction)

                    <tr>

                        <td>

                            <div class="transaction-date">

                                <strong>
                                    {{ $transaction->created_at->format('d M Y') }}
                                </strong>

                                <span>
                                    {{ $transaction->created_at->format('h:i A') }}
                                </span>

                            </div>

                        </td>


                        <td>

                            <span class="transaction-type">

                                {{ ucfirst($transaction->type) }}

                            </span>

                        </td>


                        <td>

                            @if($transaction->direction === 'credit')

                                <span class="transaction-direction credit">
                                    + Credit
                                </span>

                            @else

                                <span class="transaction-direction debit">
                                    − Debit
                                </span>

                            @endif

                        </td>


                        <td>

                            <strong class="transaction-points">

                                {{ number_format($transaction->points) }}

                            </strong>

                        </td>


                        <td>

                            <strong>

                                {{ number_format($transaction->balance_after) }}

                            </strong>

                        </td>


                        <td>

                            <span class="transaction-source">

                                {{ str_replace('_', ' ', ucfirst($transaction->source)) }}

                            </span>

                        </td>


                        <td>

                            <div class="transaction-description">

                                {{ $transaction->description ?: '—' }}

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">

                            <div class="transaction-empty">

                                <div>
                                    ◎
                                </div>

                                <h3>
                                    No transactions yet
                                </h3>

                                <p>
                                    This user's points wallet has no
                                    transaction history.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($transactions->hasPages())

            <div class="transaction-pagination">

                {{ $transactions->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>


<style>

.admin-wallet-detail {
    max-width: 1400px;
    margin: 0 auto;
    padding: 28px 28px 70px;
}


/* Header */

.wallet-detail-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.wallet-back {
    display: inline-block;
    margin-bottom: 14px;
    color: #7d8796;
    font-size: 12px;
    text-decoration: none;
}

.wallet-back:hover {
    color: #202b3e;
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
    background: #eef1f5;
    color: #344054;
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
    min-width: 0;
    padding: 18px;
    border: 1px solid #e5e8ed;
    border-radius: 13px;
    background: #fff;
}

.wallet-stat.main {
    background: #202b3e;
    border-color: #202b3e;
}

.wallet-stat span {
    display: block;
    color: #8992a0;
    font-size: 10px;
    font-weight: 650;
}

.wallet-stat.main span {
    color: #b8c0cd;
}

.wallet-stat strong {
    display: block;
    margin-top: 7px;
    color: #1b2537;
    font-size: 23px;
    font-weight: 750;
}

.wallet-stat.main strong {
    color: #fff;
}

.wallet-stat small {
    display: block;
    margin-top: 4px;
    color: #9ba3af;
    font-size: 10px;
}

.wallet-stat.main small {
    color: #aab4c2;
}


/* Main Content */

.wallet-content-grid {
    display: grid;
    grid-template-columns: 1.35fr .65fr;
    gap: 18px;
    margin-bottom: 20px;
}

.wallet-adjust-card,
.wallet-summary-card,
.wallet-transactions-card {
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
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 18px;
}

.wallet-card-heading h2 {
    margin: 0;
    color: #202b3e;
    font-size: 15px;
    font-weight: 750;
}

.wallet-card-heading p {
    margin: 4px 0 0;
    color: #929aa7;
    font-size: 10px;
}

.adjust-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f2f4f7;
    color: #344054;
    font-size: 19px;
    font-weight: 650;
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
    font-size: 10px;
}

.adjust-warning p {
    margin: 3px 0 0;
    color: #8d96a3;
    font-size: 9px;
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
    gap: 9px;
    padding: 12px;
    border: 1px solid #e1e5ea;
    border-radius: 9px;
    cursor: pointer;
}

.adjust-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.adjust-option:has(input:checked) {
    border-color: #202b3e;
    background: #fafbfc;
}

.adjust-radio {
    width: 15px;
    height: 15px;
    flex: 0 0 15px;
    border: 1px solid #b9c0c9;
    border-radius: 50%;
    position: relative;
}

.adjust-option input:checked + .adjust-radio {
    border-color: #202b3e;
}

.adjust-option input:checked + .adjust-radio::after {
    content: '';
    position: absolute;
    width: 7px;
    height: 7px;
    top: 3px;
    left: 3px;
    border-radius: 50%;
    background: #202b3e;
}

.adjust-option strong {
    display: block;
    color: #344054;
    font-size: 10px;
}

.adjust-option small {
    display: block;
    margin-top: 2px;
    color: #939ba7;
    font-size: 8px;
}


/* Form */

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 6px;
    color: #4b5666;
    font-size: 10px;
    font-weight: 700;
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
    font-size: 11px;
}

.form-field input {
    height: 40px;
    padding: 0 11px;
}

.form-field textarea {
    min-height: 95px;
    padding: 10px 11px;
    resize: vertical;
}

.form-field input:focus,
.form-field textarea:focus {
    border-color: #9aa3b0;
    box-shadow: 0 0 0 3px rgba(32, 43, 62, .04);
}

.form-field > small {
    display: block;
    margin-top: 5px;
    color: #9aa2ae;
    font-size: 8px;
}

.adjust-submit {
    width: 100%;
    height: 40px;
    border: 0;
    border-radius: 8px;
    background: #202b3e;
    color: #fff;
    cursor: pointer;
    font-family: inherit;
    font-size: 10px;
    font-weight: 700;
}

.adjust-submit:hover {
    background: #121b2a;
}


/* Summary */

.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #edf0f3;
}

.summary-row:last-child {
    border-bottom: 0;
}

.summary-row span {
    color: #808a98;
    font-size: 10px;
}

.summary-row strong {
    color: #344054;
    font-size: 11px;
    font-weight: 700;
}


/* Transactions */

.wallet-transactions-card {
    overflow: hidden;
}

.wallet-transactions-card .wallet-card-heading {
    padding: 18px 20px;
    margin: 0;
    border-bottom: 1px solid #edf0f3;
}

.transaction-table-wrap {
    overflow-x: auto;
}

.transaction-table {
    width: 100%;
    min-width: 950px;
    border-collapse: collapse;
}

.transaction-table th {
    padding: 11px 14px;
    background: #fafbfc;
    border-bottom: 1px solid #e7ebef;
    color: #737d8d;
    text-align: left;
    font-size: 8px;
    font-weight: 750;
    text-transform: uppercase;
    letter-spacing: .4px;
}

.transaction-table td {
    padding: 13px 14px;
    border-bottom: 1px solid #edf0f3;
    vertical-align: middle;
}

.transaction-table tbody tr:last-child td {
    border-bottom: 0;
}

.transaction-table tbody tr:hover {
    background: #fcfcfd;
}

.transaction-date strong {
    display: block;
    color: #465163;
    font-size: 9px;
}

.transaction-date span {
    display: block;
    margin-top: 3px;
    color: #9aa2ae;
    font-size: 8px;
}

.transaction-type {
    color: #586273;
    font-size: 9px;
    font-weight: 650;
}

.transaction-direction {
    display: inline-flex;
    padding: 4px 7px;
    border-radius: 5px;
    font-size: 8px;
    font-weight: 700;
}

.transaction-direction.credit {
    background: #eefaf4;
    color: #19734c;
}

.transaction-direction.debit {
    background: #fff2f2;
    color: #a13939;
}

.transaction-points {
    color: #344054;
    font-size: 10px;
}

.transaction-source {
    color: #7d8795;
    font-size: 8px;
}

.transaction-description {
    max-width: 260px;
    color: #697384;
    font-size: 9px;
    line-height: 1.45;
}

.transaction-empty {
    padding: 55px 20px;
    text-align: center;
}

.transaction-empty > div {
    width: 43px;
    height: 43px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #f4f5f7;
    color: #697386;
    font-size: 19px;
}

.transaction-empty h3 {
    margin: 0;
    color: #344054;
    font-size: 12px;
}

.transaction-empty p {
    margin: 5px 0 0;
    color: #929aa7;
    font-size: 9px;
}


/* Pagination */

.transaction-pagination {
    padding: 13px 17px;
    border-top: 1px solid #edf0f3;
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
        document.getElementById('adjustSubmit');

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