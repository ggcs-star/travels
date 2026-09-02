<?php

namespace App\Services\Tour;

use App\Models\TourCategory;
use App\Services\Media\TourCategoryImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class TourCategoryService
{
    private const IMAGE_FIELDS = [
        'image',
        'og_image',
    ];

    public function __construct(
        private readonly TourCategoryImageService $imageService
    ) {}

    public function create(array $data, int $userId): TourCategory
    {
        unset($data['remove_image'], $data['remove_og_image']);

        $newImagePaths = $this->storeUploadedImages($data);

        try {
            return DB::transaction(function () use ($data, $userId) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
                $data['featured'] = (bool) ($data['featured'] ?? false);
                $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
                $data['created_by'] = $userId;

                return TourCategory::create($data);
            });
        } catch (Throwable $exception) {
            $this->deleteImages($newImagePaths);

            throw $exception;
        }
    }

    public function update(
        TourCategory $category,
        array $data
    ): TourCategory {
        $pathsToDelete = $this->prepareImageChanges($category, $data);
        $newImagePaths = array_values(array_filter(
            array_intersect_key($data, array_flip(self::IMAGE_FIELDS))
        ));

        try {
            $updatedCategory = DB::transaction(function () use ($category, $data) {
                if (
                    isset($data['name']) &&
                    $data['name'] !== $category->name
                ) {
                    $data['slug'] = $this->generateUniqueSlug(
                        $data['name'],
                        $category->id
                    );
                }

                $data['featured'] = (bool) ($data['featured'] ?? false);
                $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

                $category->update($data);

                return $category->fresh();
            });
        } catch (Throwable $exception) {
            $this->deleteImages($newImagePaths);

            throw $exception;
        }

        $this->deleteImages(array_diff(
            $pathsToDelete,
            array_filter([
                $updatedCategory->image,
                $updatedCategory->og_image,
            ])
        ));

        return $updatedCategory;
    }

    public function duplicate(
        TourCategory $category
    ): TourCategory {
        return DB::transaction(function () use ($category) {
            $copy = $category->replicate();

            $copy->name = $category->name.' Copy';

            $copy->slug = $this->generateUniqueSlug(
                $copy->name
            );

            $copy->status = TourCategory::STATUS_DRAFT;
            $copy->featured = false;

            $copy->sort_order = $category->sort_order;

            $copy->created_by = auth()->id();

            $copy->save();

            return $copy;
        });
    }

    public function changeStatus(
        TourCategory $category,
        string $status
    ): TourCategory {
        if (! in_array($status, [
            TourCategory::STATUS_DRAFT,
            TourCategory::STATUS_ACTIVE,
            TourCategory::STATUS_INACTIVE,
        ], true)) {
            throw new \InvalidArgumentException(
                'Invalid category status.'
            );
        }

        $category->update([
            'status' => $status,
        ]);

        return $category->fresh();
    }

    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'tour-category';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            TourCategory::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreId
                    )
                )
                ->exists()
        ) {
            $counter++;

            $slug = $baseSlug.'-'.$counter;
        }

        return $slug;
    }

    private function prepareImageChanges(
        TourCategory $category,
        array &$data
    ): array {
        $pathsToDelete = [];
        $newImagePaths = [];

        try {
            foreach (self::IMAGE_FIELDS as $field) {
                $removeField = 'remove_'.$field;
                $currentPath = $category->{$field};

                if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                    $newImagePaths[] = $data[$field] = $this->imageService->store(
                        $data[$field]
                    );

                    if ($currentPath) {
                        $pathsToDelete[] = $currentPath;
                    }
                } elseif (! empty($data[$removeField])) {
                    $data[$field] = null;

                    if ($currentPath) {
                        $pathsToDelete[] = $currentPath;
                    }
                } else {
                    unset($data[$field]);
                }

                unset($data[$removeField]);
            }
        } catch (Throwable $exception) {
            $this->deleteImages($newImagePaths);

            throw $exception;
        }

        return $pathsToDelete;
    }

    private function storeUploadedImages(array &$data): array
    {
        $storedPaths = [];

        try {
            foreach (self::IMAGE_FIELDS as $field) {
                if (! isset($data[$field]) || ! $data[$field] instanceof UploadedFile) {
                    unset($data[$field]);

                    continue;
                }

                $storedPaths[] = $data[$field] = $this->imageService->store(
                    $data[$field]
                );
            }
        } catch (Throwable $exception) {
            $this->deleteImages($storedPaths);

            throw $exception;
        }

        return $storedPaths;
    }

    private function deleteImages(array $paths): void
    {
        foreach (array_unique(array_filter($paths)) as $path) {
            $this->imageService->delete($path);
        }
    }
}
