<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(
            Blog::class,
            'blog_blog_tag',
            'blog_tag_id',
            'blog_id'
        )->withTimestamps();
    }

    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(
            'name',
            'like',
            "%{$search}%"
        );
    }
}