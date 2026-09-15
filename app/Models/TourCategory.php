<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tour_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'image',
        'og_image',
        'description',
        'short_description',
        'sort_order',
        'status',
        'featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'created_by',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
        'featured' => 'boolean',
        'created_by' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Parent Category
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Holidays
    |   └── Honeymoon
    |
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Child Categories
    |--------------------------------------------------------------------------
    */

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Tour Packages
    |--------------------------------------------------------------------------
    |
    | Existing mapping:
    |
    | tour_categories.id
    |        ↓
    | tour_packages.category_id
    |
    */

    public function packages(): HasMany
    {
        return $this->hasMany(
            TourPackage::class,
            'category_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    |
    | Mapping:
    |
    | tour_categories.created_by
    |        ↓
    | users.id
    |
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->orderBy('sort_order');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query
            ->where('featured', true)
            ->where('status', true)
            ->orderBy('sort_order');
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function hasPackages(): bool
    {
        return $this->packages()->exists();
    }
}