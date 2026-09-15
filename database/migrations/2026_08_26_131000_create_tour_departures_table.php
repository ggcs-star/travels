<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->cascadeOnDelete();

            $table->date('departure_date');
            $table->date('return_date');

            $table->unsignedSmallInteger('capacity');

            $table->decimal('price', 12, 2)->unsigned();
            $table->decimal('sale_price', 12, 2)->unsigned()->nullable();

            $table->string('currency', 3)->default('INR');
            $table->string('meeting_point', 255)->nullable();
            $table->string('status', 20)->default('open');

            $table->timestamps();

            $table->unique([
                'tour_package_id',
                'departure_date'
            ]);

            $table->index([
                'status',
                'departure_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};