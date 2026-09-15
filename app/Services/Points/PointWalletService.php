<?php

namespace App\Services\Points;

use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserPointWallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PointWalletService
{
    /**
     * Get or create the user's points wallet.
     */
    public function walletFor(User $user): UserPointWallet
    {
        return UserPointWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_redeemed' => 0,
                'total_expired' => 0,
                'total_adjusted' => 0,
            ]
        );
    }

    /**
     * Credit points to the user's wallet.
     */
    public function credit(
        User $user,
        int $points,
        string $source,
        ?string $description = null,
        ?Model $referenceModel = null,
        ?string $reference = null,
        array $metadata = []
    ): PointTransaction {
        $this->validatePoints($points);

        return DB::transaction(function () use (
            $user,
            $points,
            $source,
            $description,
            $referenceModel,
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

            $type = $this->creditType($source);

            $wallet->balance = $balanceAfter;

            if ($type === PointTransaction::TYPE_EARNED) {
                $wallet->total_earned += $points;
            } elseif ($type === PointTransaction::TYPE_EXPIRED) {
                /*
                 * Normally expiry should be handled by debit/expiry
                 * logic, not by this credit method.
                 *
                 * Kept here only for backward compatibility.
                 */
                $wallet->total_expired += $points;
            } else {
                $wallet->total_adjusted += $points;
            }

            $wallet->save();

            return $this->createTransaction(
                wallet: $wallet,
                user: $user,
                type: $type,
                direction: PointTransaction::DIRECTION_CREDIT,
                points: $points,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfter,
                source: $source,
                description: $description,
                referenceModel: $referenceModel,
                reference: $reference,
                metadata: $metadata,
            );
        });
    }

    /**
     * Credit points only once for a unique reference.
     *
     * This is used for rewards such as booking rewards where
     * the same business event must never award points twice.
     */
    public function creditOnce(
        User $user,
        int $points,
        string $source,
        string $reference,
        ?string $description = null,
        ?Model $referenceModel = null,
        array $metadata = []
    ): ?PointTransaction {
        $this->validatePoints($points);

        return DB::transaction(function () use (
            $user,
            $points,
            $source,
            $reference,
            $description,
            $referenceModel,
            $metadata
        ) {
            $existingTransaction = PointTransaction::query()
                ->where('reference', $reference)
                ->lockForUpdate()
                ->first();

            if ($existingTransaction) {
                return null;
            }

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

            $type = $this->creditType($source);

            $wallet->balance = $balanceAfter;

            if ($type === PointTransaction::TYPE_EARNED) {
                $wallet->total_earned += $points;
            } elseif ($type === PointTransaction::TYPE_EXPIRED) {
                $wallet->total_expired += $points;
            } else {
                $wallet->total_adjusted += $points;
            }

            $wallet->save();

            return $this->createTransaction(
                wallet: $wallet,
                user: $user,
                type: $type,
                direction: PointTransaction::DIRECTION_CREDIT,
                points: $points,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfter,
                source: $source,
                description: $description,
                referenceModel: $referenceModel,
                reference: $reference,
                metadata: $metadata,
            );
        });
    }

    /**
     * Debit points from the user's wallet.
     */
    public function debit(
        User $user,
        int $points,
        string $source,
        ?string $description = null,
        ?Model $referenceModel = null,
        ?string $reference = null,
        array $metadata = []
    ): PointTransaction {
        $this->validatePoints($points);

        return DB::transaction(function () use (
            $user,
            $points,
            $source,
            $description,
            $referenceModel,
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
            $wallet->total_redeemed += $points;

            $wallet->save();

            return $this->createTransaction(
                wallet: $wallet,
                user: $user,
                type: PointTransaction::TYPE_REDEEMED,
                direction: PointTransaction::DIRECTION_DEBIT,
                points: $points,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfter,
                source: $source,
                description: $description,
                referenceModel: $referenceModel,
                reference: $reference,
                metadata: $metadata,
            );
        });
    }

    /**
     * Get point transaction history.
     */
    public function transactions(
        User $user,
        int $perPage = 20
    ): LengthAwarePaginator {
        return PointTransaction::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Generate a unique transaction reference.
     */
    private function generateReference(): string
    {
        do {
            $reference = 'PTS-' . strtoupper(Str::random(16));
        } while (
            PointTransaction::query()
                ->where('reference', $reference)
                ->exists()
        );

        return $reference;
    }

    /**
     * Create immutable ledger transaction.
     */
    private function createTransaction(
        UserPointWallet $wallet,
        User $user,
        string $type,
        string $direction,
        int $points,
        int $balanceBefore,
        int $balanceAfter,
        string $source,
        ?string $description,
        ?Model $referenceModel,
        ?string $reference,
        array $metadata
    ): PointTransaction {
        return PointTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => $type,
            'direction' => $direction,
            'points' => $points,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'source' => $source,

            'reference_type' => $referenceModel?->getMorphClass(),
            'reference_id' => $referenceModel?->getKey(),

            'reference' => $reference ?? $this->generateReference(),

            'description' => $description,

            'status' => 'completed',

            'metadata' => $metadata ?: null,
        ]);
    }

    /**
     * Validate point amount.
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
     * Determine transaction type for credit.
     */
    private function creditType(string $source): string
    {
        return match ($source) {
            'expiry' => PointTransaction::TYPE_EXPIRED,

            'admin_adjustment' => PointTransaction::TYPE_ADJUSTMENT,

            default => PointTransaction::TYPE_EARNED,
        };
    }
}