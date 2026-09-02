<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_id')
                ->constrained('blogs')
                ->cascadeOnDelete();

            $table->string('path', 500);

            $table->string('alt_text', 255)
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index([
                'blog_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_images');
    }
};