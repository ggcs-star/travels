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
                'icon' => '↘',
            ];
        }

        if ($source === 'booking_reward' || $source === 'booking_payment') {
            return [
                'title' => 'Points earned from a booking',
                'subtitle' => 'You received these points as a booking reward.',
                'icon' => '↗',
            ];
        }

        if ($source === 'admin_adjustment') {
            if ($direction === 'debit') {
                return [
                    'title' => 'Points removed by an account adjustment',
                    'subtitle' => $description !== '' ? $description : 'An account adjustment reduced your available points.',
                    'icon' => '−',
                ];
            }

            return [
                'title' => 'Points added by an account adjustment',
                'subtitle' => $description !== '' ? $description : 'An account adjustment increased your available points.',
                'icon' => '+',
            ];
        }

        if ($source === 'expiry' || ($transaction->type ?? '') === 'expired') {
            return [
                'title' => 'Points expired',
                'subtitle' => $description !== '' ? $description : 'These points are no longer available.',
                'icon' => '⌛',
            ];
        }

        $sourceLabel = ucwords(str_replace('_', ' ', $source ?: 'point activity'));

        return [
            'title' => $direction === 'credit'
                ? 'Points added to your wallet'
                : 'Points used from your wallet',
            'subtitle' => $description !== '' ? $description : $sourceLabel . ' activity.',
            'icon' => $direction === 'credit' ? '↗' : '↘',
        ];
    };
@endphp

<div class="friendly-wallet-page">

    {{-- HERO --}}
    <section class="friendly-wallet-hero">
        <div class="friendly-wallet-hero__inner">
            <a href="{{ route('profile') }}" class="friendly-wallet-back">
                <span>←</span>
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
                    <span>✓</span>
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
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--earned">+</div>
                    <div>
                        <span>Points earned</span>
                        <strong>{{ $formatPoints($wallet->total_earned) }}</strong>
                        <small>Points you received over time.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--used">−</div>
                    <div>
                        <span>Points used</span>
                        <strong>{{ $formatPoints($wallet->total_redeemed) }}</strong>
                        <small>Points used toward bookings or rewards.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--adjusted">↕</div>
                    <div>
                        <span>Account adjustments</span>
                        <strong>{{ $formatPoints($wallet->total_adjusted) }}</strong>
                        <small>Points added or removed through account adjustments.</small>
                    </div>
                </div>

                <div class="friendly-wallet-summary-card">
                    <div class="friendly-wallet-summary-icon friendly-wallet-summary-icon--expired">⌛</div>
                    <div>
                        <span>Points expired</span>
                        <strong>{{ $formatPoints($wallet->total_expired) }}</strong>
                        <small>Points that are no longer available.</small>
                    </div>
                </div>

            </div>

            <div class="friendly-wallet-how-it-works">
                <div class="friendly-wallet-how-it-works__icon">i</div>

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
                <div class="friendly-wallet-activity-list">

                    @foreach($transactions as $transaction)
                        @php
                            $activity = $friendlyTransaction($transaction);
                            $isCredit = $transaction->direction === 'credit';
                            $pointsClass = $isCredit ? 'is-positive' : 'is-negative';
                            $pointsPrefix = $isCredit ? '+' : '-';
                        @endphp

                        <article class="friendly-wallet-activity">

                            <div class="friendly-wallet-activity__icon {{ $pointsClass }}">
                                {{ $activity['icon'] }}
                            </div>

                            <div class="friendly-wallet-activity__main">

                                <div class="friendly-wallet-activity__top">
                                    <div>
                                        <h3>{{ $activity['title'] }}</h3>

                                        <div class="friendly-wallet-activity__date">
                                            {{ $transaction->created_at?->format('d M Y, h:i A') }}
                                        </div>
                                    </div>

                                    <div class="friendly-wallet-activity__points {{ $pointsClass }}">
                                        {{ $pointsPrefix }}{{ $formatPoints($transaction->points) }}
                                        <small>points</small>
                                    </div>
                                </div>

                                <p class="friendly-wallet-activity__description">
                                    {{ $activity['subtitle'] }}
                                </p>

                                <div class="friendly-wallet-activity__bottom">
                                    <div class="friendly-wallet-activity__balance">
                                        <span>Balance after this activity</span>
                                        <strong>{{ $formatPoints($transaction->balance_after) }} points</strong>
                                    </div>

                                    @if($transaction->reference)
                                        <details class="friendly-wallet-reference">
                                            <summary>View details</summary>

                                            <div>
                                                <span>Reference</span>
                                                <code>{{ $transaction->reference }}</code>
                                            </div>

                                            @if($transaction->status)
                                                <div>
                                                    <span>Status</span>
                                                    <strong>{{ ucfirst($transaction->status) }}</strong>
                                                </div>
                                            @endif
                                        </details>
                                    @endif
                                </div>

                            </div>
                        </article>
                    @endforeach

                </div>

                @if($transactions->hasPages())
                    <div class="friendly-wallet-pagination">
                        {{ $transactions->links() }}
                    </div>
                @endif

            @else

                <div class="friendly-wallet-empty">
                    <div class="friendly-wallet-empty__icon">◎</div>

                    <h3>No point activity yet</h3>

                    <p>
                        Once you earn or use points, every activity will appear here.
                    </p>

                    <a href="{{ route('profile') }}">
                        Back to profile
                        <span>→</span>
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
                <span>→</span>
            </a>
        </section>

    </main>
</div>
@endsection
