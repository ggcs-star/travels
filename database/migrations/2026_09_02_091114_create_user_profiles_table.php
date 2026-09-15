<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('phone', 40)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();

            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->string('profile_photo', 500)->nullable();

            $table->timestamps();

            $table->index('phone');
            $table->index(['country', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};