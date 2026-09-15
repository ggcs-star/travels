<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourCategoryImageService
{
    private const DISK = 'public';

    private const DIRECTORY = 'tour-categories';

    public function store(UploadedFile $image): string
    {
        $extension = $image->extension() ?: 'jpg';

        return $image->storePubliclyAs(
            self::DIRECTORY,
            Str::uuid().'.'.$extension,
            self::DISK
        );
    }

    public function delete(?string $path): void
    {
        if (! $path || $this->isExternalUrl($path)) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    private function isExternalUrl(string $path): bool
    {
        return Str::startsWith($path, [
            'http://',
            'https://',
            '//',
        ]);
    }
}
