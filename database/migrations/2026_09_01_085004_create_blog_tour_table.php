<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tour', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_id')
                ->constrained('blogs')
                ->cascadeOnDelete();

            $table->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'blog_id',
                'tour_package_id',
            ]);

            $table->index([
                'blog_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tour');
    }
};