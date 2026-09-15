<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_categories', function (Blueprint $table) {
            /*
             * Open Graph image
             */
            if (! Schema::hasColumn('tour_categories', 'og_image')) {
                $table->string('og_image', 500)
                    ->nullable()
                    ->after('image');
            }

            /*
             * Admin/user who created the category
             */
            if (! Schema::hasColumn('tour_categories', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('robots')
                    ->constrained('users')
                    ->nullOnDelete();

                $table->index('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_categories', function (Blueprint $table) {
            if (Schema::hasColumn('tour_categories', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropIndex(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('tour_categories', 'og_image')) {
                $table->dropColumn('og_image');
            }
        });
    }
};