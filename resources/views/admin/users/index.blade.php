@extends('admin.layouts.app')

@section('title', 'Users')

@section('description', 'Manage all registered users and administrators.')

@section('content')

<div class="admin-wallet-index db-users-page">

    {{-- Success / Error --}}
    @if(session('success'))
        <div class="wallet-alert success">
            <span>✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="wallet-alert error">
            <span>!</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif


    {{-- Search / Filter --}}
    <div class="wallet-search-card">

        <form method="GET" action="{{ route('admin.users.index') }}" class="wallet-search-form">

            <div class="wallet-search-input">
                <span class="search-icon">⌕</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, username or email..."
                    autocomplete="off"
                >
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="search-clear" title="Clear search">×</a>
                @endif
            </div>

            <button type="submit">Search</button>

        </form>

    </div>


    {{-- Users Table --}}
    <div class="wallet-table-card">

        <div class="wallet-table-header">
            <div>
                <span class="admin-eyebrow">USER MANAGEMENT</span>
                <h2>All Users</h2>
            </div>
        </div>

        <div class="wallet-table-wrap">

            <table class="wallet-table">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Points</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($users as $user)

                    <tr>

                        <td>
                            <div class="wallet-user">
                                <div class="wallet-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                <div class="wallet-user-info">
                                    <strong>{{ $user->name }}</strong>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="booking-pill booking-pill--status-{{ $user->status ? 'confirmed' : 'cancelled' }}">
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>
                            <span class="wallet-number">{{ number_format($user->bookings_count ?? 0) }}</span>
                        </td>

                        <td>
                            <span class="wallet-number">{{ number_format($user->pointWallet->balance ?? 0) }} pts</span>
                        </td>

                        <td>
                            <span class="wallet-number">{{ $user->created_at?->format('d M Y') }}</span>
                        </td>

                        <td>
                            <div class="wallet-row-actions">

                                <a href="{{ route('admin.users.show', $user) }}" class="wallet-icon-button" title="View">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span class="admin-sr-only">View</span>
                                </a>

                                <a href="{{ route('admin.users.edit', $user) }}" class="wallet-icon-button" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    <span class="admin-sr-only">Edit</span>
                                </a>

                                @if((int) $user->id !== (int) auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Delete this user? This can be restored later if needed.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="wallet-icon-button wallet-icon-button--danger" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                            <span class="admin-sr-only">Delete</span>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            <div class="wallet-empty">
                                <div class="wallet-empty-icon">◎</div>
                                <h3>No users found</h3>
                                @if(request('search') || request('role'))
                                    <p>No user matches your current filters.</p>
                                    <a href="{{ route('admin.users.index') }}">Clear filters</a>
                                @else
                                    <p>No users have registered yet.</p>
                                @endif
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        <div class="wallet-pagination">
            {{ $users->links() }}
        </div>

    </div>

</div>


<style>

.admin-wallet-index {
    max-width: 1450px;
    margin: 0 auto;
    padding: 28px 28px 70px;
}

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

.wallet-alert.error {
    background: #fff3f3;
    border: 1px solid #ffd7d7;
    color: #a32e2e;
}

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
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.wallet-table td {
    padding: 15px;
    border-bottom: 1px solid #edf0f3;
    vertical-align: middle;
    font-size: 14.5px;
}

.wallet-table tbody tr:last-child td {
    border-bottom: 0;
}

.wallet-table tbody tr:hover {
    background: #fcfcfd;
}

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
    font-size: 15px;
    font-weight: 680;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.wallet-user-info span {
    display: block;
    margin-top: 3px;
    overflow: hidden;
    color: #8c95a2;
    font-size: 13px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.wallet-number {
    color: #4c5768;
    font-size: 14.5px;
    font-weight: 600;
}

.wallet-row-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.wallet-icon-button {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--admin-primary, #d97706);
    border-radius: 9px;

    background: #fff;
    color: var(--admin-primary, #d97706);

    transition: background .15s ease, color .15s ease;
}

.wallet-icon-button svg {
    width: 17px;
    height: 17px;
}

.wallet-icon-button:hover {
    background: var(--admin-primary, #d97706);
    color: #fff;
}

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

.wallet-pagination {
    padding: 13px 17px;
    border-top: 1px solid #edf0f3;
}

@media (max-width: 700px) {

    .admin-wallet-index {
        padding: 20px 15px 50px;
    }

    .wallet-search-form {
        flex-direction: column;
    }

    .wallet-search-form button {
        width: 100%;
    }

}

.wallet-icon-button--danger {
    border-color: var(--admin-danger, #b42318);
    color: var(--admin-danger, #b42318);
}

.wallet-icon-button--danger:hover {
    background: var(--admin-danger, #b42318);
    color: #fff;
}

.booking-pill {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #4b5666;
    font-size: 11px;
    font-weight: 700;
}

.booking-pill--status-confirmed {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.booking-pill--status-cancelled {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.booking-pill--status-pending_payment {
    background: #fffbeb;
    color: #b45309;
}

</style>

@endsection
