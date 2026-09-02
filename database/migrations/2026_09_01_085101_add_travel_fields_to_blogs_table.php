<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('destination', 180)
                ->nullable()
                ->after('content');

            $table->string('travel_type', 100)
                ->nullable()
                ->after('destination');

            $table->string('best_time_to_visit', 180)
                ->nullable()
                ->after('travel_type');

            $table->unsignedSmallInteger('duration_days')
                ->nullable()
                ->after('best_time_to_visit');

            $table->decimal('budget_min', 12, 2)
                ->unsigned()
                ->nullable()
                ->after('duration_days');

            $table->decimal('budget_max', 12, 2)
                ->unsigned()
                ->nullable()
                ->after('budget_min');

            $table->string('currency', 3)
                ->default('INR')
                ->after('budget_max');

            $table->string('featured_image_alt', 255)
                ->nullable()
                ->after('featured_image');

            $table->string('featured_image_caption', 500)
                ->nullable()
                ->after('featured_image_alt');

            $table->string('video_url', 1000)
                ->nullable()
                ->after('featured_image_caption');

            $table->timestamp('scheduled_at')
                ->nullable()
                ->after('published_at');

            $table->string('og_title', 255)
                ->nullable()
                ->after('canonical_url');

            $table->text('og_description')
                ->nullable()
                ->after('og_title');

            $table->string('og_image', 1000)
                ->nullable()
                ->after('og_description');

            $table->index('destination');

            $table->index('travel_type');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex([
                'blogs_destination_index',
            ]);

            $table->dropIndex([
                'blogs_travel_type_index',
            ]);

            $table->dropColumn([
                'destination',
                'travel_type',
                'best_time_to_visit',
                'duration_days',
                'budget_min',
                'budget_max',
                'currency',
                'featured_image_alt',
                'featured_image_caption',
                'video_url',
                'scheduled_at',
                'og_title',
                'og_description',
                'og_image',
            ]);
        });
    }
};