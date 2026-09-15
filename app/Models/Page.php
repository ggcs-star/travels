<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'image_alt',
        'template',
        'status',
        'menu_location',
        'header_position',
        'header_parent_id',
        'footer_column',
        'footer_position',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'header_position' => 'integer',
        'header_parent_id' => 'integer',
        'footer_position' => 'integer',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function seo()
    {
        return $this->hasOne(PageSeo::class);
    }

    public function headerParent()
    {
        return $this->belongsTo(Page::class, 'header_parent_id');
    }

    public function headerChildren()
    {
        return $this->hasMany(Page::class, 'header_parent_id')
            ->orderBy('header_position')
            ->orderBy('title');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeHeaderMenu(Builder $query): Builder
    {
        return $query
            ->whereIn('menu_location', ['header', 'both'])
            ->published()
            ->orderBy('header_position')
            ->orderBy('title');
    }

    public function scopeFooterMenu(Builder $query): Builder
    {
        return $query
            ->whereIn('menu_location', ['footer', 'both'])
            ->published()
            ->orderBy('footer_position')
            ->orderBy('title');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): string
    {
        return url('/' . ltrim($this->slug, '/'));
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isInHeader(): bool
    {
        return in_array($this->menu_location, ['header', 'both'], true);
    }

    public function isInFooter(): bool
    {
        return in_array($this->menu_location, ['footer', 'both'], true);
    }
}