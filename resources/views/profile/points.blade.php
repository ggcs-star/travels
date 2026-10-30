@extends('layouts.app')

@section('title', 'My Travel Wallet')

@section('content')
@php
    $formatPoints = fn ($value) => number_format((int) $value);

    $friendlyTransaction = function ($transaction) {
        $source = strtolower((string) ($transaction->source ?? ''));
        $direction = strtolower((string) ($transaction->direction ?? ''));
        $description = trim((string) ($transaction->description ?? ''));

        if ($source === 'booking_redemption') {
            return [
                'title' => 'Points used for a booking',
                'subtitle' => 'You used these points toward a travel booking.',
                'icon' => 'booking_redemption',
            ];
        }

        if ($source === 'booking_reward' || $source === 'booking_payment') {
            return [
                'title' => 'Points earned from a booking',
                'subtitle' => 'You received these points as a booking reward.',
                'icon' => 'booking_reward',
            ];
        }

        if ($source === 'admin_adjustment') {
            if ($direction === 'debit') {
                return [
                    'title' => 'Points removed by an account adjustment',
                    'subtitle' => $description !== '' ? $description : 'An account adjustment reduced your available points.',
                    'icon' => 'admin_debit',
                ];
            }

            return [
                'title' => 'Points added by an account adjustment',
                'subtitle' => $description !== '' ? $description : 'An account adjustment increased your available points.',
                'icon' => 'admin_credit',
            ];
        }

        if ($source === 'expiry' || ($transaction->type ?? '') === 'expired') {
            return [
                'title' => 'Points expired',
                'subtitle' => $description !== '' ? $description : 'These points are no longer available.',
                'icon' => 'expiry',
            ];
        }

        $sourceLabel = ucwords(str_replace('_', ' ', $source ?: 'point activity'));

        return [
            'title' => $direction === 'credit'
                ? 'Points added to your wallet'
                : 'Points used from your wallet',
            'subtitle' => $description !== '' ? $description : $sourceLabel . ' activity.',
            'icon' => $direction === 'credit' ? 'credit' : 'debit',
        ];
    };
@endphp

<div class="friendly-wallet-page">

    {{-- HERO --}}
    <section class="friendly-wallet-hero">
        <div class="friendly-wallet-hero__inner">
            <a href="{{ route('profile') }}" class="friendly-wallet-back">
                <svg class="friendly-wallet-inline-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"/>
                    <path d="m12 19-7-7 7-7"/>
                </svg>
                Back to profile
            </a>

            <span class="friendly-wallet-eyebrow">YOUR TRAVEL REWARDS</span>

            <h1>My Travel Wallet</h1>

            <p>
                See how many points you have, where they came from, and when you used them.
            </p>
        </div>
    </section>

    <main class="friendly-wallet-content">

        {{-- CURRENT BALANCE --}}
        <section class="friendly-wallet-balance-card">

            <div class="friendly-wallet-balance-copy">
                <span class="friendly-wallet-label">POINTS YOU CAN USE NOW</span>

                <h2>
                    {{ $formatPoints($wallet->balance) }}
                    <small>points</small>
                </h2>

                <p>
                    This is the amount currently available for eligible travel bookings and rewards.
                </p>

                <div class="friendly-wallet-balance-note">
                    <svg class="friendly-wallet-inline-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m5 12 4 4L19 6"/>
                    </svg>
                    <span>Your balance updates automatically when points are earned or used.</span>
                </div>
            </div>

            <div class="friendly-wallet-balance-visual" aria-hidden="true">
                <span>TRAVEL</span>
                <strong>PTS</strong>
            </div>
        </section>

        {{-- WHAT THE NUMBERS MEAN --}}
        <section class="friendly-wallet-explainer">

            <div class="friendly-wallet-section-heading">
                <span class="friendly-wallet-eyebrow">UNDERSTAND YOUR POINTS</span>

                <h2>What these numbers mean</h2>

                <p>
                    Your wallet keeps a running record of everything that happens to your points.
                </p>
            </div>

            <div class="friendly-wallet-summary-grid">

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--earned" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>
                        </svg>
                    </div>
                    <div>
                        <span>Points earned</span>
                        <strong>{{ $formatPoints($wallet->total_earned) }}</strong>
                        <small>Points you received over time.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--used" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h18v13H3z"/>
                            <path d="M3 10h18"/>
                            <path d="M7 15h3"/>
                            <path d="M16 3 12 7l4 4"/>
                        </svg>
                    </div>
                    <div>
                        <span>Points used</span>
                        <strong>{{ $formatPoints($wallet->total_redeemed) }}</strong>
                        <small>Points used toward bookings or rewards.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--adjusted" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 4v16"/>
                            <path d="m4 8 4-4 4 4"/>
                            <path d="M16 20V4"/>
                            <path d="m12 16 4 4 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <span>Account adjustments</span>
                        <strong>{{ $formatPoints($wallet->total_adjusted) }}</strong>
                        <small>Points added or removed through account adjustments.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--expired" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                            <path d="M8 2h8"/>
                        </svg>
                    </div>
                    <div>
                        <span>Points expired</span>
                        <strong>{{ $formatPoints($wallet->total_expired) }}</strong>
                        <small>Points that are no longer available.</small>
                    </div>
                </div>

            </div>

            <div class="friendly-wallet-how-it-works">
                <div class="friendly-wallet-how-it-works__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 11v5"/>
                        <path d="M12 8h.01"/>
                    </svg>
                </div>

                <div>
                    <strong>How your available balance works</strong>
                    <p>
                        Your available balance is the amount left in your wallet after all recorded
                        additions, bookings, adjustments and expirations are taken into account.
                    </p>
                </div>
            </div>
        </section>

        {{-- HISTORY --}}
        <section class="friendly-wallet-history">

            <div class="friendly-wallet-history-head">
                <div>
                    <span class="friendly-wallet-eyebrow">YOUR ACTIVITY</span>
                    <h2>Where your points went</h2>
                    <p>
                        Every recorded point change is shown below in simple terms.
                    </p>
                </div>

                <div class="friendly-wallet-count">
                    <strong>{{ $transactions->total() }}</strong>
                    <span>recorded activities</span>
                </div>
            </div>

            @if($transactions->count())
                <div class="friendly-wallet-table-shell">
                    <div class="friendly-wallet-table-scroll" role="region"
                         aria-label="Travel points transaction history" tabindex="0">
                        <table class="friendly-wallet-table">
                            <thead>
                                <tr>
                                    <th scope="col">Date &amp; time</th>
                                    <th scope="col">Transaction</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Points</th>
                                    <th scope="col">Balance after</th>
                                    <th scope="col">Reference / status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    @php
                                        $activity = $friendlyTransaction($transaction);
                                        $isCredit = $transaction->direction === 'credit';
                                        $pointsClass = $isCredit ? 'is-positive' : 'is-negative';
                                        $pointsPrefix = $isCredit ? '+' : '−';
                                    @endphp

                                    <tr>
                                        <td data-label="Date &amp; time">
                                            <span class="friendly-wallet-table-date">
                                                {{ $transaction->created_at?->format('d M Y') ?? '—' }}
                                            </span>
                                            <small class="friendly-wallet-table-muted">
                                                {{ $transaction->created_at?->format('h:i A') ?? '' }}
                                            </small>
                                        </td>

                                        <td data-label="Transaction">
                                            <div class="friendly-wallet-table-transaction">
                                                <span class="friendly-wallet-table-icon {{ $pointsClass }}" aria-hidden="true">
                                                    @switch($activity['icon'])
                                                        @case('booking_reward')
                                                            {{-- Gift: points earned as a booking reward --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M20 12v9H4v-9"/>
                                                                <path d="M2 7h20v5H2z"/>
                                                                <path d="M12 21V7"/>
                                                                <path d="M12 7H7.5a2.5 2.5 0 1 1 2.5-2.5C10 6 12 7 12 7Z"/>
                                                                <path d="M12 7h4.5A2.5 2.5 0 1 0 14 4.5C14 6 12 7 12 7Z"/>
                                                            </svg>
                                                            @break

                                                        @case('booking_redemption')
                                                            {{-- Ticket: points used toward a booking --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 7a2 2 0 0 0 0 4v2a2 2 0 0 0 0 4h18v-4a2 2 0 0 1 0-4V5H3v2Z"/>
                                                                <path d="M13 5v2"/>
                                                                <path d="M13 11v2"/>
                                                                <path d="M13 17v2"/>
                                                            </svg>
                                                            @break

                                                        @case('admin_credit')
                                                            {{-- User shield/check: account credit --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="9" cy="8" r="3"/>
                                                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"/>
                                                                <path d="m16 14 2 2 4-4"/>
                                                            </svg>
                                                            @break

                                                        @case('admin_debit')
                                                            {{-- User with minus: account debit --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="9" cy="8" r="3"/>
                                                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"/>
                                                                <path d="M16 15h6"/>
                                                            </svg>
                                                            @break

                                                        @case('expiry')
                                                            {{-- Clock: expired points --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="9"/>
                                                                <path d="M12 7v5l3 2"/>
                                                                <path d="m5 5 2 2"/>
                                                            </svg>
                                                            @break

                                                        @case('credit')
                                                            {{-- Wallet with plus: other credited points --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <rect x="3" y="6" width="18" height="15" rx="2"/>
                                                                <path d="M3 10h18"/>
                                                                <path d="M12 13v5"/>
                                                                <path d="M9.5 15.5h5"/>
                                                                <path d="M6 6V4h13"/>
                                                            </svg>
                                                            @break

                                                        @default
                                                            {{-- Wallet with arrow: points used --}}
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                <rect x="3" y="6" width="18" height="15" rx="2"/>
                                                                <path d="M3 10h18"/>
                                                                <path d="M12 13v5"/>
                                                                <path d="m9 16 3 3 3-3"/>
                                                                <path d="M6 6V4h13"/>
                                                            </svg>
                                                    @endswitch
                                                </span>
                                                <span class="friendly-wallet-table-copy">
                                                    <strong>{{ $activity['title'] }}</strong>
                                                    <small>{{ $activity['subtitle'] }}</small>
                                                </span>
                                            </div>
                                        </td>

                                        <td data-label="Type">
                                            <span class="friendly-wallet-table-type {{ $pointsClass }}">
                                                {{ $isCredit ? 'Credit' : 'Debit' }}
                                            </span>
                                        </td>

                                        <td data-label="Points">
                                            <strong class="friendly-wallet-table-points {{ $pointsClass }}">
                                                {{ $pointsPrefix }}{{ $formatPoints($transaction->points) }}
                                                <small>pts</small>
                                            </strong>
                                        </td>

                                        <td data-label="Balance after">
                                            <strong class="friendly-wallet-table-balance">
                                                {{ $formatPoints($transaction->balance_after) }}
                                            </strong>
                                            <small class="friendly-wallet-table-muted">points</small>
                                        </td>

                                        <td data-label="Reference / status">
                                            @if($transaction->reference)
                                                <code class="friendly-wallet-table-reference">
                                                    {{ $transaction->reference }}
                                                </code>
                                            @else
                                                <span class="friendly-wallet-table-muted">—</span>
                                            @endif

                                            @if($transaction->status)
                                                <span class="friendly-wallet-table-status">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($transactions->hasPages())
                    <div class="friendly-wallet-pagination">
                        {{ $transactions->links() }}
                    </div>
                @endif

            @else

                <div class="friendly-wallet-empty">
                    <div class="friendly-wallet-empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                    </div>

                    <h3>No point activity yet</h3>

                    <p>
                        Once you earn or use points, every activity will appear here.
                    </p>

                    <a href="{{ route('profile') }}">
                        Back to profile
                        <svg class="friendly-wallet-inline-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/>
                            <path d="m13 6 6 6-6 6"/>
                        </svg>
                    </a>
                </div>

            @endif
        </section>

        {{-- SIMPLE HELP --}}
        <section class="friendly-wallet-help">
            <div>
                <span class="friendly-wallet-eyebrow">NEED A QUICK EXPLANATION?</span>
                <h2>Not sure why your balance changed?</h2>
                <p>
                    Open any activity above to see what happened and what your balance became after it.
                </p>
            </div>

            <a href="{{ route('profile') }}">
                Return to profile
                <svg class="friendly-wallet-inline-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/>
                    <path d="m13 6 6 6-6 6"/>
                </svg>
            </a>
        </section>

    </main>
</div>
@endsection
