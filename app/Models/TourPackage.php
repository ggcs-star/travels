<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourPackage extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_INACTIVE = 'inactive';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        // Classification
        'category_id',

        // Basic package information
        'package_code',
        'name',
        'slug',

        // Location
        'destination',
        'starting_city',
        'ending_city',

        // Content
        'short_description',
        'description',

        // Duration
        'duration_days',
        'duration_nights',

        // Additional information
        'difficulty_level',
        'age_min',
        'age_max',
        'best_time',

        // Package content
        'highlights',
        'included_items',
        'excluded_items',
        'itinerary',
        'important_notes',
        'terms_conditions',
        'cancellation_policy',
        'privacy_policy',

        // Media
        'cover_image',

        // Package controls
        'featured',
        'status',

        // SEO
        'seo_key',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',

        // Audit
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'category_id' => 'integer',

        'duration_days' => 'integer',
        'duration_nights' => 'integer',

        'age_min' => 'integer',
        'age_max' => 'integer',

        'highlights' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'itinerary' => 'array',

        'featured' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Package category.
     *
     * Example:
     * Holidays
     * Monthly Tours
     * School & College
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            TourCategory::class,
            'category_id'
        );
    }

    /**
     * Admin/user who created the package.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Package departures/trips.
     */
    public function departures(): HasMany
    {
        return $this->hasMany(
            TourDeparture::class,
            'tour_package_id'
        );
    }

    /**
     * Package gallery images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(
            TourPackageImage::class,
            'tour_package_id'
        )->orderBy('sort_order');
    }

    /**
     * Package bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'tour_package_id'
        );
    }


    /**
 * Related blog posts.
 */
public function blogs()
{
    return $this->belongsToMany(
        Blog::class,
        'blog_tour',
        'tour_package_id',
        'blog_id'
    )
        ->withPivot('sort_order')
        ->withTimestamps()
        ->orderBy('blog_tour.sort_order');
}

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Published packages only.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PUBLISHED
        );
    }

    /**
     * Featured packages only.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where(
            'featured',
            true
        );
    }

    /**
     * Packages having at least one upcoming open departure.
     *
     * Availability is controlled by departures.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where(
                'status',
                self::STATUS_PUBLISHED
            )
            ->whereHas(
                'departures',
                function (Builder $departureQuery) {
                    $departureQuery
                        ->where(
                            'status',
                            'open'
                        )
                        ->whereDate(
                            'departure_date',
                            '>=',
                            now()->toDateString()
                        );
                }
            );
    }

    /**
     * Search packages.
     */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        if (
            ! $search ||
            ! trim($search)
        ) {
            return $query;
        }

        $search = trim($search);

        return $query->where(
            function (Builder $q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'package_code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'destination',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'starting_city',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'ending_city',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'seo_key',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'category',
                    function (
                        Builder $categoryQuery
                    ) use ($search) {

                        $categoryQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            }
        );
    }

    /**
     * Filter packages by category ID.
     */
    public function scopeInCategory(
        Builder $query,
        int|string|null $categoryId
    ): Builder {
        if (
            $categoryId === null ||
            $categoryId === ''
        ) {
            return $query;
        }

        return $query->where(
            'category_id',
            $categoryId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Availability
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether the package is available for booking.
     *
     * A package is available only when:
     *
     * 1. Package is published.
     * 2. At least one departure is open.
     * 3. Departure date is today or in the future.
     */
    public function isAvailable(): bool
    {
        if (
            $this->status !==
            self::STATUS_PUBLISHED
        ) {
            return false;
        }

        return $this->hasUpcomingDeparture();
    }

    /**
     * Check whether package has an upcoming open departure.
     */
    public function hasUpcomingDeparture(): bool
    {
        return $this
            ->departures()
            ->where(
                'status',
                'open'
            )
            ->whereDate(
                'departure_date',
                '>=',
                now()->toDateString()
            )
            ->exists();
    }

    /**
     * Get next available departure.
     */
    public function nextDeparture()
    {
        return $this
            ->departures()
            ->where(
                'status',
                'open'
            )
            ->whereDate(
                'departure_date',
                '>=',
                now()->toDateString()
            )
            ->orderBy(
                'departure_date'
            )
            ->first();
    }

    /**
     * Get lowest upcoming departure price.
     *
     * Sale price is preferred when available.
     */
    public function lowestUpcomingPrice(): ?float
    {
        $departure = $this
            ->departures()
            ->where(
                'status',
                'open'
            )
            ->whereDate(
                'departure_date',
                '>=',
                now()->toDateString()
            )
            ->orderByRaw(
                'COALESCE(sale_price, price) ASC'
            )
            ->first();

        if (! $departure) {
            return null;
        }

        return (float) (
            $departure->sale_price
            ?? $departure->price
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Cover image URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (
            Str::startsWith(
                $this->cover_image,
                [
                    'http://',
                    'https://',
                    '//',
                ]
            )
        ) {
            return $this->cover_image;
        }

        return Storage::disk(
            'public'
        )->url(
            $this->cover_image
        );
    }

    /**
     * SEO title fallback.
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title
            ?: $this->name;
    }

    /**
     * SEO description fallback.
     */
    public function getSeoDescriptionAttribute(): ?string
    {
        return $this->meta_description
            ?: $this->short_description;
    }

    /**
     * Canonical URL.
     */
    public function getCanonicalUrlAttribute(
        $value
    ): string {
        if ($value) {
            return $value;
        }

        return route(
            'tours.show',
            $this->slug
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}