<?php

namespace App\Services\Points;

use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserPointWallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminPointWalletService
{
    /**
     * Get paginated users with their point wallets.
     *
     * Users without a wallet are also included.
     */
    public function users(
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return User::query()
            ->with('pointWallet')
            ->when(
                filled($search),
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get one user's wallet.
     *
     * A wallet is created when required.
     */
    public function walletFor(User $user): UserPointWallet
    {
        return app(PointWalletService::class)
            ->walletFor($user);
    }

    /**
     * Get a user's wallet and recent transactions.
     */
    public function walletDetails(
        User $user,
        int $perPage = 10
    ): array {
        $wallet = $this->walletFor($user);

        $transactions = PointTransaction::query()
            ->where('user_id', $user->id)
            ->where('wallet_id', $wallet->id)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return [
            'user' => $user,
            'wallet' => $wallet,
            'transactions' => $transactions,
        ];
    }

    /**
     * Add points as an admin adjustment.
     */
    public function addPoints(
        User $user,
        int $points,
        string $reason,
        ?string $reference = null,
        array $metadata = []
    ): PointTransaction {
        $this->validatePoints($points);
        $this->validateReason($reason);

        return DB::transaction(function () use (
            $user,
            $points,
            $reason,
            $reference,
            $metadata
        ) {
            $wallet = UserPointWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                $wallet = UserPointWallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'total_earned' => 0,
                    'total_redeemed' => 0,
                    'total_expired' => 0,
                    'total_adjusted' => 0,
                ]);

                $wallet = UserPointWallet::query()
                    ->whereKey($wallet->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $balanceBefore = (int) $wallet->balance;
            $balanceAfter = $balanceBefore + $points;

            $wallet->balance = $balanceAfter;
            $wallet->total_adjusted += $points;
            $wallet->save();

            return PointTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => PointTransaction::TYPE_ADJUSTMENT,
                'direction' => PointTransaction::DIRECTION_CREDIT,
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source' => 'admin_adjustment',
                'reference_type' => null,
                'reference_id' => null,
                'reference' => $reference
                    ?? $this->generateReference('ADMIN-ADD'),
                'description' => $reason,
                'status' => 'completed',
                'metadata' => $metadata ?: [
                    'admin_user_id' => auth()->id(),
                    'adjustment' => 'credit',
                ],
            ]);
        });
    }

    /**
     * Deduct points as an admin adjustment.
     */
    public function deductPoints(
        User $user,
        int $points,
        string $reason,
        ?string $reference = null,
        array $metadata = []
    ): PointTransaction {
        $this->validatePoints($points);
        $this->validateReason($reason);

        return DB::transaction(function () use (
            $user,
            $points,
            $reason,
            $reference,
            $metadata
        ) {
            $wallet = UserPointWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                throw new RuntimeException(
                    'Points wallet does not exist for this user.'
                );
            }

            $balanceBefore = (int) $wallet->balance;

            if ($balanceBefore < $points) {
                throw new RuntimeException(
                    'Insufficient points balance.'
                );
            }

            $balanceAfter = $balanceBefore - $points;

            $wallet->balance = $balanceAfter;
            $wallet->total_adjusted += $points;
            $wallet->save();

            return PointTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => PointTransaction::TYPE_ADJUSTMENT,
                'direction' => PointTransaction::DIRECTION_DEBIT,
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source' => 'admin_adjustment',
                'reference_type' => null,
                'reference_id' => null,
                'reference' => $reference
                    ?? $this->generateReference('ADMIN-REMOVE'),
                'description' => $reason,
                'status' => 'completed',
                'metadata' => $metadata ?: [
                    'admin_user_id' => auth()->id(),
                    'adjustment' => 'debit',
                ],
            ]);
        });
    }

    /**
     * Validate adjustment amount.
     */
    private function validatePoints(int $points): void
    {
        if ($points <= 0) {
            throw new RuntimeException(
                'Points must be greater than zero.'
            );
        }
    }

    /**
     * Validate admin adjustment reason.
     */
    private function validateReason(string $reason): void
    {
        if (trim($reason) === '') {
            throw new RuntimeException(
                'A reason is required for an admin points adjustment.'
            );
        }

        if (mb_strlen($reason) > 500) {
            throw new RuntimeException(
                'The adjustment reason cannot exceed 500 characters.'
            );
        }
    }

    /**
     * Generate a unique admin adjustment reference.
     */
    private function generateReference(string $prefix): string
    {
        do {
            $reference = $prefix . '-' . strtoupper(
                bin2hex(random_bytes(8))
            );
        } while (
            PointTransaction::query()
                ->where('reference', $reference)
                ->exists()
        );

        return $reference;
    }
}