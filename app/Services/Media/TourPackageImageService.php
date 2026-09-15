<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourPackageImageService
{
    private const DISK = 'public';

    private const DIRECTORY = 'tour-packages';

    public function store(UploadedFile $image): string
    {
        return $image->storePubliclyAs(
            self::DIRECTORY,
            Str::uuid().'.'.($image->extension() ?: 'jpg'),
            self::DISK
        );
    }

    public function delete(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }
}
