@extends('admin.layouts.app')

@section('title', 'Transaction History')

@section('description', 'Complete points ledger for this user.')

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
                        <span>·</span>
                        <a href="{{ route('admin.point-wallets.show', $user) }}">
                            Add / deduct points
                        </a>
                    </p>

                </div>

            </div>

        </div>


        <div class="wallet-user-status">
            <strong>{{ number_format($wallet->balance ?? 0) }}</strong>
            <span>points</span>
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


        <div class="transaction-pagination">

            {{ $transactions->withQueryString()->links() }}

        </div>

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
    margin-bottom: 20px;
    padding: 22px 24px;
    border: 1px solid #e5e8ed;
    border-radius: 13px;
    background: #fff;
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
    align-items: baseline;
    gap: 6px;
    padding: 8px 14px;
    border: 1px solid #e1e6eb;
    border-radius: 999px;
    background: #fff;
    color: #687384;
    font-size: 11px;
    font-weight: 650;
}

.wallet-user-status strong {
    color: #202b3e;
    font-size: 14px;
}


/* Transactions */

.wallet-transactions-card {
    overflow: hidden;
    border: 1px solid #e5e8ed;
    border-radius: 13px;
    background: #fff;
}

.wallet-transactions-card .wallet-card-heading {
    padding: 18px 20px;
    margin: 0;
    border-bottom: 1px solid #edf0f3;
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

.transaction-table-wrap {
    overflow-x: auto;
}

.transaction-table {
    width: 100%;
    min-width: 950px;
    border-collapse: collapse;
}

.transaction-table th {
    padding: 18px 15px;
    background: var(--admin-sidebar, #f5faf6);
    border-bottom: 1px solid #e8ebef;
    color: var(--admin-sidebar-dark, #14532d);
    text-align: left;
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.transaction-table td {
    padding: 15px;
    border-bottom: 1px solid #edf0f3;
    vertical-align: middle;
    font-size: 14.5px;
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
    font-size: 14px;
}

.transaction-date span {
    display: block;
    margin-top: 3px;
    color: #9aa2ae;
    font-size: 12px;
}

.transaction-type {
    color: #586273;
    font-size: 14px;
    font-weight: 650;
}

.transaction-direction {
    display: inline-flex;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
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
    font-size: 15px;
}

.transaction-source {
    color: #7d8795;
    font-size: 13px;
}

.transaction-description {
    max-width: 260px;
    color: #697384;
    font-size: 13px;
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

.transaction-pagination {
    padding: 13px 17px;
    border-top: 1px solid #edf0f3;
}


/* Responsive */

@media (max-width: 600px) {

    .admin-wallet-detail {
        padding: 20px 14px 50px;
    }

    .wallet-detail-header {
        align-items: flex-start;
        flex-direction: column;
    }

}

</style>

@endsection
