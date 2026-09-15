<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_settings', function (Blueprint $table) {
            $table->id();

            $table->string('key', 100)->unique();

            $table->string('name', 150);

            $table->text('description')->nullable();

            $table->boolean('enabled')->default(true);

            $table->unsignedBigInteger('points')->default(0);

            $table->unsignedBigInteger('minimum_amount')
                ->nullable();

            $table->unsignedBigInteger('amount_unit')
                ->nullable();

            $table->timestamps();

            $table->index(['enabled', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_settings');
    }
};