<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('blog_categories')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Author
            |--------------------------------------------------------------------------
            */

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Basic Content
            |--------------------------------------------------------------------------
            */

            $table->string('title', 200);

            $table->string('slug', 220)
                ->unique();

            $table->text('excerpt')
                ->nullable();

            $table->longText('content');

            /*
            |--------------------------------------------------------------------------
            | Featured Image
            |--------------------------------------------------------------------------
            */

            $table->string('featured_image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */

            $table->string('status', 20)
                ->default('draft');

            $table->boolean('featured')
                ->default(false);

            $table->unsignedInteger('reading_time')
                ->nullable();

            $table->unsignedBigInteger('views')
                ->default(0);

            $table->dateTime('published_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('seo_key', 160)
                ->unique();

            $table->string('robots', 50)
                ->default('index,follow');

            $table->string('meta_title')
                ->nullable();

            $table->text('meta_description')
                ->nullable();

            $table->text('meta_keywords')
                ->nullable();

            $table->string('canonical_url', 500)
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'status',
                'published_at',
            ]);

            $table->index([
                'featured',
                'status',
            ]);

            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};