<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Package Identity
            |--------------------------------------------------------------------------
            */
            $table->string('package_code', 50)->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();

            /*
            |--------------------------------------------------------------------------
            | Package Classification
            |--------------------------------------------------------------------------
            */
            $table->string('tour_type', 50);

            /*
            |--------------------------------------------------------------------------
            | Destination
            |--------------------------------------------------------------------------
            */
            $table->string('destination', 150);
            $table->string('starting_city', 150)->nullable();
            $table->string('ending_city', 150)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Package Content
            |--------------------------------------------------------------------------
            */
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Duration
            |--------------------------------------------------------------------------
            */
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedSmallInteger('duration_nights');

            /*
            |--------------------------------------------------------------------------
            | Traveller Information
            |--------------------------------------------------------------------------
            */
            $table->string('difficulty_level', 30)->nullable();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('best_time', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Package Highlights
            |--------------------------------------------------------------------------
            */
            $table->json('highlights')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Inclusions / Exclusions
            |--------------------------------------------------------------------------
            */
            $table->json('included_items')->nullable();
            $table->json('excluded_items')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */
            $table->string('cover_image', 500)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Availability Window
            |--------------------------------------------------------------------------
            |
            | Example:
            | available_from  = 2026-08-28
            | available_until = 2026-09-27
            |
            | Package remains available through 27 Sep.
            | From 28 Sep it becomes expired/unavailable.
            |
            */
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */
            $table->boolean('featured')->default(false);
            $table->string('status', 20)->default('draft');

            /*
            |--------------------------------------------------------------------------
            | Ownership
            |--------------------------------------------------------------------------
            */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Timestamps / Soft Delete
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('tour_type');
            $table->index('destination');
            $table->index('starting_city');
            $table->index('status');
            $table->index('featured');
            $table->index('available_from');
            $table->index('available_until');
            $table->index('created_by');

            $table->index([
                'status',
                'available_from',
                'available_until',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};