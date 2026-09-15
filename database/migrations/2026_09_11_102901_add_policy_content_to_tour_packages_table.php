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
        /*
        |--------------------------------------------------------------------------
        | Itinerary
        |--------------------------------------------------------------------------
        |
        | Your current database does not have this column yet.
        | Add it first so the policy fields can safely follow it.
        |
        */
        if (! Schema::hasColumn('tour_packages', 'itinerary')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->longText('itinerary')
                    ->nullable();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Important Notes
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasColumn('tour_packages', 'important_notes')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->longText('important_notes')
                    ->nullable();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Terms & Conditions
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasColumn('tour_packages', 'terms_conditions')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->longText('terms_conditions')
                    ->nullable();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Cancellation Policy
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasColumn('tour_packages', 'cancellation_policy')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->longText('cancellation_policy')
                    ->nullable();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Privacy Policy
        |--------------------------------------------------------------------------
        */
        if (! Schema::hasColumn('tour_packages', 'privacy_policy')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->longText('privacy_policy')
                    ->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $columns = [
                'itinerary',
                'important_notes',
                'terms_conditions',
                'cancellation_policy',
                'privacy_policy',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tour_packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};