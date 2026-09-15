<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->string('provider', 40);
            $table->string('provider_order_id', 100)->nullable()->unique();
            $table->string('provider_payment_id', 100)->nullable()->unique();

            $table->decimal('amount', 12, 2)->unsigned();

            $table->string('currency', 3)->default('INR');
            $table->string('status', 24)->default('created');
            $table->string('signature', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};