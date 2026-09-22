<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | BASIC CONTENT
            |--------------------------------------------------------------------------
            */

            $table->string('title', 255);

            $table->string('slug', 191)->unique();

            $table->text('excerpt')->nullable();

            $table->longText('content')->nullable();

            /*
            |--------------------------------------------------------------------------
            | MEDIA
            |--------------------------------------------------------------------------
            */

            $table->string('featured_image', 500)->nullable();

            $table->string('image_alt', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | DISPLAY
            |--------------------------------------------------------------------------
            */

            $table->string('template', 50)->default('default');

            $table->string('status', 20)->default('draft');

            /*
            |--------------------------------------------------------------------------
            | MENU LOCATION
            |--------------------------------------------------------------------------
            |
            | none   = not shown in menus
            | header = header only
            | footer = footer only
            | both   = header + footer
            |
            */

            $table->string('menu_location', 20)->default('none');

            /*
            |--------------------------------------------------------------------------
            | HEADER MENU
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('header_position')->default(0);

            $table->foreignId('header_parent_id')
                ->nullable()
                ->constrained('pages')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | FOOTER MENU
            |--------------------------------------------------------------------------
            */

            $table->string('footer_column', 100)->nullable();

            $table->unsignedInteger('footer_position')->default(0);

            /*
            |--------------------------------------------------------------------------
            | GENERAL SORTING
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')->default(0);

            /*
            |--------------------------------------------------------------------------
            | OWNERSHIP
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index('status');
            $table->index('menu_location');
            $table->index('header_parent_id');
            $table->index('footer_column');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};