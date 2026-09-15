<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * =========================================================
         * 1. Create tour_categories table
         * =========================================================
         */
        if (! Schema::hasTable('tour_categories')) {
            Schema::create('tour_categories', function (Blueprint $table) {
                $table->id();

                $table->string('name', 100);
                $table->string('slug', 120)->unique();

                $table->string('icon', 100)->nullable();

                $table->text('description')->nullable();

                $table->unsignedInteger('sort_order')->default(0);

                $table->boolean('status')->default(true);

                $table->timestamps();

                $table->index(['status', 'sort_order']);
            });
        }

        /*
         * =========================================================
         * 2. Add default categories
         * =========================================================
         */
        $categories = [
            [
                'name' => 'Spiritual & Pilgrimage',
                'slug' => 'spiritual-pilgrimage',
                'sort_order' => 1,
            ],
            [
                'name' => 'Holidays',
                'slug' => 'holidays',
                'sort_order' => 2,
            ],
            [
                'name' => 'School & College',
                'slug' => 'school-college',
                'sort_order' => 3,
            ],
            [
                'name' => 'Business Trips',
                'slug' => 'business-trips',
                'sort_order' => 4,
            ],
            [
                'name' => 'Monthly Tours',
                'slug' => 'monthly-tours',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            \DB::table('tour_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
         * =========================================================
         * 3. Add category_id to tour_packages
         * =========================================================
         */
        if (! Schema::hasColumn('tour_packages', 'category_id')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tour_categories')
                    ->restrictOnDelete();
            });
        }

        /*
         * =========================================================
         * 4. SEO fields
         * =========================================================
         */
        Schema::table('tour_packages', function (Blueprint $table) {
            if (! Schema::hasColumn('tour_packages', 'seo_key')) {
                $table->string('seo_key', 180)
                    ->nullable()
                    ->unique()
                    ->after('slug');
            }

            if (! Schema::hasColumn('tour_packages', 'meta_title')) {
                $table->string('meta_title', 255)
                    ->nullable()
                    ->after('seo_key');
            }

            if (! Schema::hasColumn('tour_packages', 'meta_description')) {
                $table->text('meta_description')
                    ->nullable()
                    ->after('meta_title');
            }

            if (! Schema::hasColumn('tour_packages', 'meta_keywords')) {
                $table->text('meta_keywords')
                    ->nullable()
                    ->after('meta_description');
            }

            if (! Schema::hasColumn('tour_packages', 'canonical_url')) {
                $table->string('canonical_url', 500)
                    ->nullable()
                    ->after('meta_keywords');
            }

            if (! Schema::hasColumn('tour_packages', 'robots')) {
                $table->string('robots', 50)
                    ->default('index,follow')
                    ->after('canonical_url');
            }
        });

        /*
         * =========================================================
         * 5. Remove old package-level type
         * =========================================================
         */
        if (Schema::hasColumn('tour_packages', 'tour_type')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropColumn('tour_type');
            });
        }

        /*
         * =========================================================
         * 6. Remove package-level availability
         *
         * Availability belongs to tour_departures.
         * =========================================================
         */
        if (Schema::hasColumn('tour_packages', 'available_from')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropColumn('available_from');
            });
        }

        if (Schema::hasColumn('tour_packages', 'available_until')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropColumn('available_until');
            });
        }
    }

    public function down(): void
    {
        /*
         * Remove FK + category_id
         */
        if (Schema::hasColumn('tour_packages', 'category_id')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        /*
         * Remove SEO
         */
        $seoColumns = [
            'robots',
            'canonical_url',
            'meta_keywords',
            'meta_description',
            'meta_title',
        ];

        foreach ($seoColumns as $column) {
            if (Schema::hasColumn('tour_packages', $column)) {
                Schema::table('tour_packages', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('tour_packages', 'seo_key')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropUnique(['seo_key']);
                $table->dropColumn('seo_key');
            });
        }

        /*
         * Restore old fields
         */
        if (! Schema::hasColumn('tour_packages', 'tour_type')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->string('tour_type', 50)
                    ->nullable()
                    ->after('slug');
            });
        }

        if (! Schema::hasColumn('tour_packages', 'available_from')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->date('available_from')
                    ->nullable()
                    ->after('cover_image');
            });
        }

        if (! Schema::hasColumn('tour_packages', 'available_until')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->date('available_until')
                    ->nullable()
                    ->after('available_from');
            });
        }

        /*
         * Drop categories table
         */
        if (Schema::hasTable('tour_categories')) {
            Schema::drop('tour_categories');
        }
    }
};