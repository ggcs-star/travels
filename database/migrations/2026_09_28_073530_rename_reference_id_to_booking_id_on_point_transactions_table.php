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
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropColumn('reference_type');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->renameColumn('reference_id', 'booking_id');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->nullOnDelete();

            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropIndex(['booking_id']);
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->renameColumn('booking_id', 'reference_id');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->string('reference_type', 100)
                ->nullable()
                ->after('source');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->index(['reference_type', 'reference_id']);
        });
    }
};
