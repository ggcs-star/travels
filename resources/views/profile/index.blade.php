@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-page">

    <section class="profile-hero">
        <div class="profile-hero__image"></div>
        <div class="profile-hero__overlay"></div>

        <div class="profile-hero__content">
            <div class="profile-hero__inner">
                <div class="profile-breadcrumb">
                    <span>Account</span>
                    <span>/</span>
                    <strong>Profile</strong>
                </div>

                <span class="profile-hero__eyebrow">YOUR TRAVEL ACCOUNT</span>
                <h1>My Profile</h1>
                <p>Manage your personal details, travel points and account activity.</p>
            </div>
        </div>
    </section>

    <main class="profile-content">

        @if(session('success'))
            <div class="profile-alert success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="profile-alert error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- PROFILE IDENTITY --}}
        <section class="account-profile-card">
            <div class="account-profile-main">
                <div class="account-avatar">
                    @if($profile->profile_photo)
                        <img
                            src="{{ asset('storage/' . $profile->profile_photo) }}"
                            alt="{{ $user->name ?: $user->username }} profile photo"
                        >
                    @else
                        {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
                    @endif
                </div>

                <div class="account-profile-info">
                    <span class="account-profile-label">TRAVEL MEMBER</span>
                    <h2>{{ $user->name ?: 'Member' }}</h2>
                    <p class="account-username">{{ '@' . $user->username }}</p>
                    <p class="account-email">{{ $user->email }}</p>
                </div>
            </div>

            <div class="account-profile-actions">
                <a href="{{ route('profile.edit') }}"
                   class="profile-edit-button"
                   aria-label="Edit profile">
                    <span aria-hidden="true">✎</span>
                    Edit profile
                </a>

                <div class="account-profile-status">
                    <span class="account-status-dot"></span>
                    <span>Active account</span>
                </div>
            </div>
        </section>

        {{-- ACCOUNT INFORMATION --}}
        <section class="account-section">
            <div class="account-section-heading account-section-heading--with-action">
                <div>
                    <span>ACCOUNT INFORMATION</span>
                    <h2>Personal details</h2>
                </div>

                <a href="{{ route('profile.edit') }}" class="profile-section-action">
                    Edit details
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="account-details-grid">
                <div class="account-detail">
                    <span>Full name</span>
                    <strong>{{ $user->name ?: 'Not added yet' }}</strong>
                </div>

                <div class="account-detail">
                    <span>Username</span>
                    <strong>{{ '@' . $user->username }}</strong>
                </div>

                <div class="account-detail">
                    <span>Email address</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="account-detail">
                    <span>Phone number</span>
                    <strong class="{{ $profile->phone ? '' : 'is-muted' }}">
                        {{ $profile->phone ?: 'Not added yet' }}
                    </strong>
                </div>
            </div>
        </section>

        {{-- CLICKABLE WALLET --}}
        <a href="{{ route('profile.points') }}"
           class="travel-wallet travel-wallet--clickable"
           aria-label="View your complete travel points wallet">

            <div class="travel-wallet__top">
                <div class="travel-wallet__intro">
                    <span class="travel-wallet__eyebrow">TRAVEL REWARDS</span>
                    <h2>My Travel Points</h2>
                    <p>See your balance, earned points, redeemed points and complete point history.</p>
                </div>

                <div class="travel-wallet__balance">
                    <span>AVAILABLE BALANCE</span>
                    <strong>{{ number_format($wallet->balance ?? 0) }}</strong>
                    <small>points</small>
                </div>
            </div>

            <div class="travel-wallet__stats">
                <div>
                    <span>Total earned</span>
                    <strong>{{ number_format($wallet->total_earned ?? 0) }}</strong>
                </div>

                <div>
                    <span>Total redeemed</span>
                    <strong>{{ number_format($wallet->total_redeemed ?? 0) }}</strong>
                </div>

                <div>
                    <span>Total expired</span>
                    <strong>{{ number_format($wallet->total_expired ?? 0) }}</strong>
                </div>
            </div>

            <div class="travel-wallet__footer">
                <span>Your points activity is updated automatically.</span>
                <span class="travel-wallet__view-link">
                    View full wallet <span aria-hidden="true">→</span>
                </span>
            </div>
        </a>

        {{-- RECENT ACTIVITY --}}
        <section class="activity-section">
            <div class="activity-section__heading">
                <div>
                    <span>ACCOUNT ACTIVITY</span>
                    <h2>Recent point activity</h2>
                </div>

                <a href="{{ route('profile.points') }}" class="activity-view-all">
                    View all <span aria-hidden="true">→</span>
                </a>
            </div>

            @if($recentTransactions->isNotEmpty())
                <div class="profile-activity-table-wrap">
                    <table class="profile-activity-table">
                        <thead>
                            <tr>
                                <th scope="col">Date &amp; Time</th>
                                <th scope="col">Transaction</th>
                                <th scope="col">Type</th>
                                <th scope="col">Points</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($recentTransactions as $transaction)
                                @php
                                    $isCredit = $transaction->direction === 'credit';
                                    $transactionTitle = $transaction->source
                                        ? ucwords(str_replace('_', ' ', $transaction->source))
                                        : 'Points transaction';
                                @endphp

                                <tr>
                                    <td data-label="Date &amp; Time">
                                        <span class="profile-activity-date">
                                            {{ $transaction->created_at?->format('d M Y') ?? '—' }}
                                        </span>
                                        <small class="profile-activity-time">
                                            {{ $transaction->created_at?->format('h:i A') ?? '' }}
                                        </small>
                                    </td>

                                    <td data-label="Transaction">
                                        <span class="profile-activity-title">{{ $transactionTitle }}</span>
                                        <small class="profile-activity-description">
                                            {{ $transaction->description ?: 'Points transaction' }}
                                        </small>
                                    </td>

                                    <td data-label="Type">
                                        <span class="profile-activity-type {{ $isCredit ? 'is-credit' : 'is-debit' }}">
                                            {{ $isCredit ? 'Credit' : 'Debit' }}
                                        </span>
                                    </td>

                                    <td data-label="Points">
                                        <strong class="profile-activity-points {{ $isCredit ? 'is-credit' : 'is-debit' }}">
                                            {{ $isCredit ? '+' : '−' }}{{ number_format($transaction->points) }}
                                            <small>pts</small>
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="activity-empty">
                    <div class="activity-empty__icon">—</div>
                    <h3>No point activity yet</h3>
                    <p>Your earned, redeemed and expired points will appear here.</p>
                </div>
            @endif
        </section>

    </main>
</div>
@endsection
