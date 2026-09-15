<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_number', 32)->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->restrictOnDelete();

            $table->foreignId('tour_departure_id')
                ->constrained('tour_departures')
                ->restrictOnDelete();

            $table->string('contact_name', 150);
            $table->string('contact_email', 255);
            $table->string('contact_phone', 40);
            $table->string('country', 100)->nullable();
            $table->text('special_requests')->nullable();

            $table->unsignedTinyInteger('traveller_count');

            $table->decimal('subtotal', 12, 2)->unsigned();
            $table->decimal('tax_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('total_amount', 12, 2)->unsigned();

            $table->string('currency', 3)->default('INR');
            $table->string('status', 24)->default('pending_payment');
            $table->string('payment_status', 24)->default('unpaid');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['tour_departure_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};