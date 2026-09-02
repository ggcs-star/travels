<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->decimal('price', 12, 2)
                ->default(0)
                ->after('cover_image');

            $table->decimal('sale_price', 12, 2)
                ->nullable()
                ->after('price');

            $table->string('currency', 3)
                ->default('INR')
                ->after('sale_price');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'sale_price',
                'currency',
            ]);
        });
    }
};