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
             * Parent / Child category mapping
             */
            if (! Schema::hasColumn('tour_categories', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tour_categories')
                    ->nullOnDelete();

                $table->index('parent_id');
            }

            /*
             * Category image
             */
            if (! Schema::hasColumn('tour_categories', 'image')) {
                $table->string('image', 500)
                    ->nullable()
                    ->after('icon');
            }

            /*
             * Short description for cards/listings
             */
            if (! Schema::hasColumn('tour_categories', 'short_description')) {
                $table->string('short_description', 500)
                    ->nullable()
                    ->after('description');
            }

            /*
             * Featured category
             */
            if (! Schema::hasColumn('tour_categories', 'featured')) {
                $table->boolean('featured')
                    ->default(false)
                    ->after('status');

                $table->index(['status', 'featured']);
            }

            /*
             * SEO
             */
            if (! Schema::hasColumn('tour_categories', 'meta_title')) {
                $table->string('meta_title', 255)
                    ->nullable()
                    ->after('featured');
            }

            if (! Schema::hasColumn('tour_categories', 'meta_description')) {
                $table->text('meta_description')
                    ->nullable()
                    ->after('meta_title');
            }

            if (! Schema::hasColumn('tour_categories', 'meta_keywords')) {
                $table->text('meta_keywords')
                    ->nullable()
                    ->after('meta_description');
            }

            if (! Schema::hasColumn('tour_categories', 'canonical_url')) {
                $table->string('canonical_url', 500)
                    ->nullable()
                    ->after('meta_keywords');
            }

            if (! Schema::hasColumn('tour_categories', 'robots')) {
                $table->string('robots', 50)
                    ->default('index,follow')
                    ->after('canonical_url');
            }
        });

        /*
         * Soft delete support.
         */
        if (! Schema::hasColumn('tour_categories', 'deleted_at')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        /*
         * Remove soft deletes.
         */
        if (Schema::hasColumn('tour_categories', 'deleted_at')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        /*
         * Remove SEO fields.
         */
        $seoColumns = [
            'robots',
            'canonical_url',
            'meta_keywords',
            'meta_description',
            'meta_title',
        ];

        foreach ($seoColumns as $column) {
            if (Schema::hasColumn('tour_categories', $column)) {
                Schema::table('tour_categories', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        /*
         * Remove featured.
         */
        if (Schema::hasColumn('tour_categories', 'featured')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->dropColumn('featured');
            });
        }

        /*
         * Remove short description.
         */
        if (Schema::hasColumn('tour_categories', 'short_description')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->dropColumn('short_description');
            });
        }

        /*
         * Remove image.
         */
        if (Schema::hasColumn('tour_categories', 'image')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }

        /*
         * Remove parent mapping.
         */
        if (Schema::hasColumn('tour_categories', 'parent_id')) {
            Schema::table('tour_categories', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropIndex(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
};