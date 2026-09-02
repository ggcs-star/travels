<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'url',
        'title',
        'thumbnail',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(
            Blog::class
        );
    }
}