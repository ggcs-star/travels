<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('wallet_id')
                ->constrained('user_point_wallets')
                ->cascadeOnDelete();

            $table->enum('type', [
                'earned',
                'redeemed',
                'expired',
                'adjustment',
                'reversal',
            ]);

            $table->enum('direction', [
                'credit',
                'debit',
            ]);

            $table->unsignedBigInteger('points');

            $table->unsignedBigInteger('balance_before');

            $table->unsignedBigInteger('balance_after');

            $table->string('source', 50);

            $table->string('reference_type', 100)->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('reference', 100)->nullable()->unique();

            $table->string('description', 500)->nullable();

            $table->enum('status', [
                'pending',
                'completed',
                'reversed',
            ])->default('completed');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['wallet_id', 'created_at']);
            $table->index(['type', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};