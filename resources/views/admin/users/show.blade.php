@extends('admin.layouts.app')

@section('title', $user->name)

@section('content')

<div class="admin-page tp-page">

    {{-- Breadcrumb --}}
    <div class="tp-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <span>›</span>
        <a href="{{ route('admin.users.index') }}">Users</a>
        <span>›</span>
        <strong>{{ $user->name }}</strong>
    </div>


    {{-- Hero --}}
    <section class="tp-hero">

        <div class="tp-hero__top">

            <div>
                <span class="tp-hero__eyebrow">{{ Str::headline($user->role) }}</span>
                <h1>{{ $user->name }}</h1>
                <p>
                    {{ $user->email }}
                    <span>•</span>
                    {{ $user->status ? 'Active' : 'Inactive' }}
                </p>
            </div>

            <div class="tp-hero__actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="tp-hero__btn tp-hero__btn--solid">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="tp-hero__btn">
                    ← Back
                </a>
            </div>

        </div>

    </section>


    @if(session('success'))
        <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
    @endif


    {{-- Stats --}}
    <div class="bd-stats tp-stats--four">

        <div class="bd-stat">
            <span class="bd-stat__icon {{ $user->status ? 'bd-stat__icon--green' : 'bd-stat__icon--orange' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </span>
            <div>
                <small>Status</small>
                <strong>{{ $user->status ? 'Active' : 'Inactive' }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6"/></svg>
            </span>
            <div>
                <small>Role</small>
                <strong>{{ Str::headline($user->role) }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/></svg>
            </span>
            <div>
                <small>Bookings</small>
                <strong>{{ number_format($user->bookings_count ?? 0) }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
            </span>
            <div>
                <small>Points Balance</small>
                <strong>{{ number_format($user->pointWallet->balance ?? 0) }}</strong>
            </div>
        </div>

    </div>


    {{-- Details --}}
    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6"/></svg>
                <h2>Account Details</h2>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Full name</span>
                    <strong>{{ $user->name }}</strong>
                </div>

                <div>
                    <span>Username</span>
                    <strong>{{ $user->username }}</strong>
                </div>

                <div>
                    <span>Email</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div>
                    <span>Role</span>
                    <strong>{{ Str::headline($user->role) }}</strong>
                </div>

                <div>
                    <span>Status</span>
                    <strong>{{ $user->status ? 'Active' : 'Inactive' }}</strong>
                </div>

                <div>
                    <span>Joined</span>
                    <strong>{{ $user->created_at?->format('d M Y, h:i A') }}</strong>
                </div>

            </div>

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/></svg>
                <h2>Quick Links</h2>
            </div>

            <div class="admin-actions">

                <a href="{{ route('admin.point-wallets.show', $user) }}" class="admin-action">
                    <span class="admin-action__icon">±</span>
                    <span class="admin-action__content">
                        <strong>Points Wallet</strong>
                        <small>Add or deduct points</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

                <a href="{{ route('admin.point-wallets.transactions', $user) }}" class="admin-action">
                    <span class="admin-action__icon">◷</span>
                    <span class="admin-action__content">
                        <strong>Transaction History</strong>
                        <small>Full points ledger</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

                <a href="{{ route('admin.bookings.index', ['search' => $user->email]) }}" class="admin-action">
                    <span class="admin-action__icon">✈</span>
                    <span class="admin-action__content">
                        <strong>View Bookings</strong>
                        <small>{{ $user->bookings_count ?? 0 }} total</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </a>

            </div>

        </section>

    </div>


</div>


<style>

.tp-page {
    max-width: 1360px;
    margin: 0 auto;
}

.tp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    color: #9aa2ae;
    font-size: 13px;
}

.tp-breadcrumb a {
    color: #7d8796;
    text-decoration: none;
}

.tp-breadcrumb a:hover {
    color: var(--admin-primary, #d97706);
}

.tp-breadcrumb strong {
    color: #344054;
}

.tp-hero {
    padding: 26px 28px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: linear-gradient(120deg, #0b302b 0%, #14532d 45%, #d97706 130%);
    color: #fff;
}

.tp-hero__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.tp-hero__eyebrow {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, .85);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.tp-hero__top h1 {
    margin: 0;
    color: #fff;
    font-size: 26px;
    font-weight: 800;
}

.tp-hero__top p {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, .9);
    font-size: 13.5px;
}

.tp-hero__top p span {
    margin: 0 6px;
    color: rgba(255, 255, 255, .55);
}

.tp-hero__actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tp-hero__btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .16);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 650;
    white-space: nowrap;
}

.tp-hero__btn:hover {
    background: rgba(255, 255, 255, .28);
}

.tp-hero__btn--solid {
    background: #fff;
    color: #0b302b;
}

.tp-hero__btn--solid:hover {
    background: #f1f3f5;
}

.bd-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 18px 0 20px;
}

.bd-stat {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 0;
    padding: 16px;
    border: 1px solid #ece3d6;
    border-radius: 13px;
    background: #fffaf3;
}

.bd-stat__icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.bd-stat__icon svg {
    width: 18px;
    height: 18px;
}

.bd-stat__icon--blue {
    background: #e8f1ff;
    color: #2563eb;
}

.bd-stat__icon--green {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.bd-stat__icon--orange {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.bd-stat__icon--gold {
    background: #fff8e1;
    color: #b7871a;
}

.bd-stat small {
    display: block;
    color: #8c95a2;
    font-size: 12px;
    font-weight: 650;
}

.bd-stat strong {
    display: block;
    margin-top: 3px;
    color: #202b3e;
    font-size: 18px;
    font-weight: 750;
}

.bd-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    margin-bottom: 0;
}

.bd-card {
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
    padding: 20px;
    margin-bottom: 18px;
}

.bd-card__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eef0f2;
}

.bd-card__header svg {
    width: 19px;
    height: 19px;
    flex: 0 0 19px;
}

.bd-card__header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--admin-text, #202b3e);
}

.bd-card__header--orange svg {
    color: var(--admin-primary, #d97706);
}

.bd-card__header--gold svg {
    color: #b7871a;
}

@media (max-width: 1150px) {
    .bd-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .bd-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

</style>

@endsection
