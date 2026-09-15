<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Blog extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'category_id',
        'author_id',

        'title',
        'slug',
        'excerpt',
        'content',

        'destination',
        'travel_type',
        'best_time_to_visit',
        'duration_days',
        'budget_min',
        'budget_max',
        'currency',

        'featured_image',
        'featured_image_alt',
        'featured_image_caption',

        'video_url',

        'status',
        'featured',
        'reading_time',
        'views',

        'published_at',
        'scheduled_at',

        'seo_key',
        'robots',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',

        'og_title',
        'og_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',

            'views' => 'integer',

            'duration_days' => 'integer',

            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',

            'reading_time' => 'integer',

            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            BlogCategory::class,
            'category_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'author_id'
        );
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogTag::class,
            'blog_blog_tag',
            'blog_id',
            'blog_tag_id'
        )->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(
            BlogImage::class
        )->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(
            BlogFaq::class
        )->orderBy('sort_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(
            BlogVideo::class
        )->orderBy('sort_order');
    }

    public function relatedTours(): BelongsToMany
    {
        return $this->belongsToMany(
            TourPackage::class,
            'blog_tour',
            'blog_id',
            'tour_package_id'
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

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere(
                        'published_at',
                        '<=',
                        now()
                    );
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search) {
            $query
                ->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'excerpt',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'destination',
                    'like',
                    "%{$search}%"
                );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPublished(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        return ! $this->published_at
            || $this->published_at->lte(now());
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->scheduled_at
            && $this->scheduled_at->isFuture();
    }

    public function getReadingTimeLabelAttribute(): string
    {
        $minutes = max(
            1,
            (int) ($this->reading_time ?: 1)
        );

        return $minutes.' '.(
            $minutes === 1
                ? 'min read'
                : 'mins read'
        );
    }

    public function getBudgetLabelAttribute(): ?string
    {
        if (
            $this->budget_min === null
            && $this->budget_max === null
        ) {
            return null;
        }

        $currency = $this->currency ?: 'INR';

        if ($this->budget_min !== null && $this->budget_max !== null) {
            return sprintf(
                '%s %s - %s',
                $currency,
                number_format((float) $this->budget_min, 0),
                number_format((float) $this->budget_max, 0)
            );
        }

        $amount = $this->budget_min
            ?? $this->budget_max;

        return sprintf(
            '%s %s',
            $currency,
            number_format((float) $amount, 0)
        );
    }

    public function getPublicUrlAttribute(): string
    {
        return route(
            'blog.show',
            $this->slug
        );
    }
}