@extends('admin.layouts.app')

@section('title', 'Points Wallets')

@section('description', 'Manage user points balances, adjustments and transaction history.')

@section('content')

<div class="admin-wallet-index">

    {{-- Success --}}
    @if(session('success'))
        <div class="wallet-alert success">
            <span>✓</span>

            <div>
                {{ session('success') }}
            </div>
        </div>
    @endif


    {{-- Search --}}
    <div class="wallet-search-card">

        <form
            method="GET"
            action="{{ route('admin.point-wallets.index') }}"
            class="wallet-search-form"
        >

            <div class="wallet-search-input">

                <span class="search-icon">⌕</span>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name, username or email..."
                    autocomplete="off"
                >

                @if($search)
                    <a
                        href="{{ route('admin.point-wallets.index') }}"
                        class="search-clear"
                        title="Clear search"
                    >
                        ×
                    </a>
                @endif

            </div>

            <button type="submit">
                Search
            </button>

        </form>

    </div>


    {{-- Wallet Table --}}
    <div class="wallet-table-card">

        <div class="wallet-table-header">

            <div>
                <span class="admin-eyebrow">
                    POINT MANAGEMENT
                </span>

                <h2>User Points Wallets</h2>
            </div>

            <a
                href="{{ route('admin.point-settings.index') }}"
                class="wallet-settings-btn"
            >
                <span>⚙</span>
                Point Settings
            </a>

        </div>


        <div class="wallet-table-wrap">

            <table class="wallet-table">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Balance</th>
                        <th>Total Earned</th>
                        <th>Redeemed</th>
                        <th>Expired</th>
                        <th>Adjusted</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($users as $user)

                    @php
                        $wallet = $user->pointWallet;
                    @endphp

                    <tr>

                        {{-- User --}}
                        <td>

                            <div class="wallet-user">

                                <div class="wallet-avatar">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>

                                <div class="wallet-user-info">

                                    <strong>
                                        {{ $user->name }}
                                    </strong>

                                    <span>
                                        {{ $user->email }}
                                    </span>

                                </div>

                            </div>

                        </td>


                        {{-- Balance --}}
                        <td>

                            <div class="wallet-balance">

                                <strong>
                                    {{ number_format($wallet?->balance ?? 0) }}
                                </strong>

                                <span>
                                    pts
                                </span>

                            </div>

                        </td>


                        {{-- Earned --}}
                        <td>

                            <span class="wallet-number">
                                {{ number_format($wallet?->total_earned ?? 0) }}
                            </span>

                        </td>


                        {{-- Redeemed --}}
                        <td>

                            <span class="wallet-number">
                                {{ number_format($wallet?->total_redeemed ?? 0) }}
                            </span>

                        </td>


                        {{-- Expired --}}
                        <td>

                            <span class="wallet-number">
                                {{ number_format($wallet?->total_expired ?? 0) }}
                            </span>

                        </td>


                        {{-- Adjusted --}}
                        <td>

                            <span class="wallet-number">
                                {{ number_format($wallet?->total_adjusted ?? 0) }}
                            </span>

                        </td>


                        {{-- Action --}}
                        <td>

                            <a
                                href="{{ route('admin.point-wallets.show', $user) }}"
                                class="admin-icon-button"
                                title="View"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span class="admin-sr-only">View</span>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">

                            <div class="wallet-empty">

                                <div class="wallet-empty-icon">
                                    ◎
                                </div>

                                <h3>
                                    No users found
                                </h3>

                                @if($search)

                                    <p>
                                        No user matches
                                        "<strong>{{ $search }}</strong>".
                                    </p>

                                    <a
                                        href="{{ route('admin.point-wallets.index') }}"
                                    >
                                        Clear search
                                    </a>

                                @else

                                    <p>
                                        There are no users available for
                                        points management yet.
                                    </p>

                                @endif

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        <div class="wallet-pagination">
            {{ $users->withQueryString()->links() }}
        </div>

    </div>

</div>


<style>

.admin-wallet-index {
    max-width: 1450px;
    margin: 0 auto;
    padding: 28px 28px 70px;
}


/* -----------------------------------------
   Header
----------------------------------------- */

.wallet-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 22px;
}

.wallet-eyebrow {
    margin-bottom: 7px;
    color: var(--admin-primary);
    font-size: 11px;
    font-weight: 750;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.wallet-page-header h1 {
    margin: 0;
    color: #182235;
    font-size: 26px;
    font-weight: 760;
}

.wallet-page-header p {
    margin: 6px 0 0;
    color: #8992a0;
    font-size: 11px;
}

.wallet-settings-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 38px;
    padding: 0 13px;
    border: 1px solid #dfe3e8;
    border-radius: 8px;
    background: #fff;
    color: #4c5768;
    text-decoration: none;
    font-size: 11px;
    font-weight: 650;
}

.wallet-settings-btn:hover {
    border-color: #bfc6d0;
    color: #202b3e;
}

.wallet-settings-btn span {
    font-size: 14px;
}


/* -----------------------------------------
   Alert
----------------------------------------- */

.wallet-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 18px;
    padding: 12px 14px;
    border-radius: 9px;
    font-size: 11px;
}

.wallet-alert.success {
    background: #eefaf4;
    border: 1px solid #ccebdc;
    color: #176b48;
}


/* -----------------------------------------
   Search
----------------------------------------- */

.wallet-search-card {
    padding: 14px;
    margin-bottom: 18px;
    border: 1px solid #e5e8ed;
    border-radius: 12px;
    background: #fff;
}

.wallet-search-form {
    display: flex;
    gap: 9px;
}

.wallet-search-input {
    position: relative;
    flex: 1;
}

.search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9aa2af;
    font-size: 18px;
}

.wallet-search-input input {
    width: 100%;
    height: 40px;
    box-sizing: border-box;
    padding: 0 40px 0 38px;
    border: 1px solid #dfe3e9;
    border-radius: 8px;
    outline: none;
    color: #273348;
    font-family: inherit;
    font-size: 11px;
}

.wallet-search-input input:focus {
    border-color: #8791a1;
    box-shadow: 0 0 0 3px rgba(32, 43, 62, .05);
}

.search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #8c95a2;
    font-size: 17px;
    text-decoration: none;
}

.wallet-search-form button {
    height: 40px;
    padding: 0 19px;
    border: 0;
    border-radius: 8px;
    background: var(--admin-primary);
    color: #fff;
    cursor: pointer;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 650;
}

.wallet-search-form button:hover {
    background: var(--admin-primary-dark);
}


/* -----------------------------------------
   Table Card
----------------------------------------- */

.wallet-table-card {
    overflow: hidden;
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
}

.wallet-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px;
    border-bottom: 1px solid #edf0f3;
}

.wallet-table-header h2 {
    margin: 0;
    color: var(--admin-text, #17263d);
    font-size: 16px;
    font-weight: 800;
}

.wallet-table-header p {
    margin: 4px 0 0;
    color: #929aa7;
    font-size: 12px;
}


/* -----------------------------------------
   Table
----------------------------------------- */

.wallet-table-wrap {
    overflow-x: auto;
}

.wallet-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}

.wallet-table th {
    padding: 18px 15px;
    border-bottom: 1px solid #e8ebef;
    background: var(--admin-sidebar, #f5faf6);
    color: var(--admin-sidebar-dark, #14532d);
    text-align: left;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.wallet-table td {
    padding: 15px;
    border-bottom: 1px solid #edf0f3;
    vertical-align: middle;
    font-size: 13px;
}

.wallet-table tbody tr:last-child td {
    border-bottom: 0;
}

.wallet-table tbody tr:hover {
    background: #fcfcfd;
}


/* -----------------------------------------
   User
----------------------------------------- */

.wallet-user {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 220px;
}

.wallet-avatar {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #eef1f5;
    color: #344054;
    font-size: 13px;
    font-weight: 750;
}

.wallet-user-info {
    min-width: 0;
}

.wallet-user-info strong {
    display: block;
    overflow: hidden;
    color: #344054;
    font-size: 13.5px;
    font-weight: 680;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.wallet-user-info span {
    display: block;
    margin-top: 3px;
    overflow: hidden;
    color: #8c95a2;
    font-size: 11.5px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.wallet-user-info small {
    display: block;
    margin-top: 2px;
    color: #a1a8b3;
    font-size: 11px;
}


/* -----------------------------------------
   Numbers
----------------------------------------- */

.wallet-balance {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.wallet-balance strong {
    color: #202b3e;
    font-size: 15px;
    font-weight: 750;
}

.wallet-balance span {
    color: #929aa7;
    font-size: 11px;
}

.wallet-number {
    color: #4c5768;
    font-size: 13px;
    font-weight: 600;
}


/* -----------------------------------------
   View
----------------------------------------- */

.wallet-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 10px;
    border: 1px solid #dfe3e8;
    border-radius: 7px;
    background: #fff;
    color: #344054;
    text-decoration: none;
    font-size: 9px;
    font-weight: 680;
    white-space: nowrap;
}

.wallet-view-btn:hover {
    border-color: #bfc6d0;
    background: #f8f9fa;
}

.wallet-view-btn span {
    font-size: 12px;
}


/* -----------------------------------------
   Empty
----------------------------------------- */

.wallet-empty {
    padding: 60px 20px;
    text-align: center;
}

.wallet-empty-icon {
    width: 45px;
    height: 45px;
    margin: 0 auto 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #f4f5f7;
    color: #697386;
    font-size: 20px;
}

.wallet-empty h3 {
    margin: 0;
    color: #344054;
    font-size: 13px;
}

.wallet-empty p {
    margin: 5px 0 10px;
    color: #929aa7;
    font-size: 10px;
}

.wallet-empty a {
    color: #344054;
    font-size: 10px;
    font-weight: 650;
}


/* -----------------------------------------
   Pagination
----------------------------------------- */

.wallet-pagination {
    padding: 13px 17px;
    border-top: 1px solid #edf0f3;
}


/* -----------------------------------------
   Responsive
----------------------------------------- */

@media (max-width: 700px) {

    .admin-wallet-index {
        padding: 20px 15px 50px;
    }

    .wallet-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .wallet-search-form {
        flex-direction: column;
    }

    .wallet-search-form button {
        width: 100%;
    }

}

</style>

@endsection