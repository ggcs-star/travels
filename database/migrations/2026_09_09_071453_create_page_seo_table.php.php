<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_id')
                ->unique()
                ->constrained('pages')
                ->cascadeOnDelete();

            $table->string('meta_title', 255)->nullable();

            $table->text('meta_description')->nullable();

            $table->text('keywords')->nullable();

            $table->string('canonical_url', 1000)->nullable();

            $table->string('robots', 100)->default('index,follow');

            /*
            |--------------------------------------------------------------------------
            | OPEN GRAPH
            |--------------------------------------------------------------------------
            */

            $table->string('og_title', 255)->nullable();

            $table->text('og_description')->nullable();

            $table->string('og_image', 500)->nullable();

            /*
            |--------------------------------------------------------------------------
            | TWITTER
            |--------------------------------------------------------------------------
            */

            $table->string('twitter_title', 255)->nullable();

            $table->text('twitter_description')->nullable();

            $table->string('twitter_image', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seo');
    }
};