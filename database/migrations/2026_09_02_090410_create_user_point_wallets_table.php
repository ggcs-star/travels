<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_point_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('balance')
                ->default(0);

            $table->unsignedBigInteger('total_earned')
                ->default(0);

            $table->unsignedBigInteger('total_redeemed')
                ->default(0);

            $table->unsignedBigInteger('total_expired')
                ->default(0);

            $table->unsignedBigInteger('total_adjusted')
                ->default(0);

            $table->timestamps();

            $table->index('balance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_point_wallets');
    }
};