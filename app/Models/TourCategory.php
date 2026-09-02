<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourCategory extends Model
{
    use HasFactory;

    protected $table = 'tour_categories';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
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
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->orderBy('sort_order');
    }
}