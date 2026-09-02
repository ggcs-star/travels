<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TourPackageImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'image_path',
        'alt_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(
            TourPackage::class,
            'tour_package_id'
        );
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (
            str_starts_with($this->image_path, 'http://') ||
            str_starts_with($this->image_path, 'https://') ||
            str_starts_with($this->image_path, '//')
        ) {
            return $this->image_path;
        }

        return Storage::disk('public')->url(
            $this->image_path
        );
    }
}