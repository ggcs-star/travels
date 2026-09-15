<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_settings', function (Blueprint $table) {
            $table->string('reward_type', 30)
                ->default('fixed')
                ->after('enabled');

            $table->string('calculation_basis', 30)
                ->nullable()
                ->after('reward_type');

            $table->boolean('redemption_enabled')
                ->default(false)
                ->after('calculation_basis');

            $table->decimal('point_value', 12, 4)
                ->nullable()
                ->after('amount_unit');

            $table->decimal('max_redemption_percent', 5, 2)
                ->nullable()
                ->after('point_value');

            $table->unsignedBigInteger('max_points_per_booking')
                ->nullable()
                ->after('max_redemption_percent');

            $table->boolean('expiry_enabled')
                ->default(false)
                ->after('max_points_per_booking');

            $table->unsignedInteger('expiry_days')
                ->nullable()
                ->after('expiry_enabled');

            $table->index(['key', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::table('point_settings', function (Blueprint $table) {
            $table->dropIndex(['key', 'enabled']);

            $table->dropColumn([
                'reward_type',
                'calculation_basis',
                'redemption_enabled',
                'point_value',
                'max_redemption_percent',
                'max_points_per_booking',
                'expiry_enabled',
                'expiry_days',
            ]);
        });
    }
};