<?php

namespace App\Services\Tour;

use App\Models\TourCategory;
use App\Services\Media\TourCategoryImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class TourCategoryService
{
    /**
     * Image fields managed by this service.
     */
    private const IMAGE_FIELDS = [
        'image',
        'og_image',
    ];

    public function __construct(
        private readonly TourCategoryImageService $imageService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        int $userId
    ): TourCategory {
        unset(
            $data['remove_image'],
            $data['remove_og_image']
        );

        $this->validateParentCategory(
            $data['parent_id'] ?? null
        );

        $newImagePaths = $this->storeUploadedImages($data);

        try {
            return DB::transaction(
                function () use ($data, $userId): TourCategory {
                    /*
                     * Always generate the final unique slug
                     * from the category name.
                     */
                    $data['slug'] = $this->generateUniqueSlug(
                        $data['name']
                    );

                    $data['featured'] = (bool) (
                        $data['featured'] ?? false
                    );

                    $data['status'] = (bool) (
                        $data['status'] ?? true
                    );

                    $data['sort_order'] = (int) (
                        $data['sort_order'] ?? 0
                    );

                    $data['parent_id'] = $data['parent_id'] ?? null;

                    $data['created_by'] = $userId;

                    return TourCategory::create($data);
                }
            );
        } catch (Throwable $exception) {
            /*
             * Database failed after files were uploaded.
             * Remove newly uploaded files to prevent orphan files.
             */
            $this->deleteImages($newImagePaths);

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        TourCategory $category,
        array $data
    ): TourCategory {
        $this->validateParentCategory(
            $data['parent_id'] ?? null,
            $category->id
        );

        /*
         * Store new images / prepare removals.
         */
        $pathsToDelete = $this->prepareImageChanges(
            $category,
            $data
        );

        /*
         * Keep track of newly uploaded files.
         * If DB update fails, these files must be deleted.
         */
        $newImagePaths = [];

        foreach (self::IMAGE_FIELDS as $field) {
            if (
                isset($data[$field]) &&
                is_string($data[$field])
            ) {
                $newImagePaths[] = $data[$field];
            }
        }

        try {
            $updatedCategory = DB::transaction(
                function () use (
                    $category,
                    $data
                ): TourCategory {
                    /*
                     * Generate a new slug only when the
                     * category name actually changes.
                     */
                    if (
                        isset($data['name']) &&
                        trim($data['name']) !== $category->name
                    ) {
                        $data['slug'] = $this->generateUniqueSlug(
                            $data['name'],
                            $category->id
                        );
                    } elseif (
                        array_key_exists('slug', $data) &&
                        filled($data['slug'])
                    ) {
                        /*
                         * Keep manually supplied slug when
                         * it is present and validated.
                         */
                        $data['slug'] = Str::slug(
                            $data['slug']
                        );
                    } else {
                        /*
                         * Never accidentally clear the
                         * existing slug.
                         */
                        unset($data['slug']);
                    }

                    $data['featured'] = (bool) (
                        $data['featured'] ?? false
                    );

                    $data['status'] = (bool) (
                        $data['status'] ?? false
                    );

                    $data['sort_order'] = (int) (
                        $data['sort_order'] ?? 0
                    );

                    $data['parent_id'] = $data['parent_id'] ?? null;

                    /*
                     * Do not allow created_by to be changed
                     * during normal category editing.
                     */
                    unset($data['created_by']);

                    $category->update($data);

                    return $category->fresh();
                }
            );
        } catch (Throwable $exception) {
            /*
             * If the database update fails, delete files
             * that were uploaded during this request.
             */
            $this->deleteImages($newImagePaths);

            throw $exception;
        }

        /*
         * Delete old image files only after the database
         * update has completed successfully.
         */
        $this->deleteImages(
            array_diff(
                $pathsToDelete,
                array_filter([
                    $updatedCategory->image,
                    $updatedCategory->og_image,
                ])
            )
        );

        return $updatedCategory;
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        TourCategory $category
    ): TourCategory {
        return DB::transaction(
            function () use ($category): TourCategory {
                $copy = $category->replicate();

                $copy->name = $category->name . ' Copy';

                $copy->slug = $this->generateUniqueSlug(
                    $copy->name
                );

                /*
                 * Boolean status architecture:
                 *
                 * false = inactive
                 * true  = active
                 *
                 * Duplicate starts inactive so it does not
                 * immediately become visible on the website.
                 */
                $copy->status = false;

                $copy->featured = false;

                $copy->sort_order = $category->sort_order;

                $copy->created_by = auth()->id();

                /*
                 * Parent relationship is intentionally
                 * preserved.
                 */
                $copy->parent_id = $category->parent_id;

                $copy->save();

                return $copy;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        TourCategory $category,
        bool $status
    ): TourCategory {
        $category->update([
            'status' => $status,
        ]);

        return $category->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Unique Slug
    |--------------------------------------------------------------------------
    */

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
                    $ignoreId !== null,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreId
                    )
                )
                ->exists()
        ) {
            $counter++;

            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Category Validation
    |--------------------------------------------------------------------------
    */

    private function validateParentCategory(
        mixed $parentId,
        ?int $categoryId = null
    ): void {
        if (
            $parentId === null ||
            $parentId === ''
        ) {
            return;
        }

        $parentId = (int) $parentId;

        /*
         * A category cannot be its own parent.
         */
        if (
            $categoryId !== null &&
            $parentId === $categoryId
        ) {
            throw new InvalidArgumentException(
                'A category cannot be its own parent.'
            );
        }

        /*
         * Parent must exist, be active and not deleted.
         */
        $parentExists = TourCategory::query()
            ->whereKey($parentId)
            ->where('status', true)
            ->exists();

        if (! $parentExists) {
            throw new InvalidArgumentException(
                'Selected parent category is invalid.'
            );
        }

        /*
         * Prevent circular hierarchy.
         *
         * Example:
         *
         * Holidays
         *   └── Honeymoon
         *
         * Honeymoon cannot become parent of Holidays.
         */
        if ($categoryId !== null) {
            $this->ensureNoCircularParent(
                $parentId,
                $categoryId
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Circular Parent Protection
    |--------------------------------------------------------------------------
    */

    private function ensureNoCircularParent(
        int $parentId,
        int $categoryId
    ): void {
        $visited = [];

        $currentParentId = $parentId;

        while ($currentParentId !== null) {
            if (
                in_array(
                    $currentParentId,
                    $visited,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Invalid category hierarchy detected.'
                );
            }

            $visited[] = $currentParentId;

            /*
             * If the selected parent eventually points
             * back to the category being edited, this
             * would create a circular hierarchy.
             */
            if ($currentParentId === $categoryId) {
                throw new InvalidArgumentException(
                    'A category cannot be assigned under one of its own child categories.'
                );
            }

            $currentParentId = TourCategory::query()
                ->whereKey($currentParentId)
                ->value('parent_id');

            if ($currentParentId !== null) {
                $currentParentId = (int) $currentParentId;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Image Changes
    |--------------------------------------------------------------------------
    */

    private function prepareImageChanges(
        TourCategory $category,
        array &$data
    ): array {
        $pathsToDelete = [];
        $newImagePaths = [];

        try {
            foreach (self::IMAGE_FIELDS as $field) {
                $removeField = 'remove_' . $field;

                $currentPath = $category->{$field};

                /*
                 * New image uploaded.
                 */
                if (
                    isset($data[$field]) &&
                    $data[$field] instanceof UploadedFile
                ) {
                    $storedPath = $this->imageService->store(
                        $data[$field]
                    );

                    $data[$field] = $storedPath;

                    $newImagePaths[] = $storedPath;

                    if ($currentPath) {
                        $pathsToDelete[] = $currentPath;
                    }

                    unset($data[$removeField]);

                    continue;
                }

                /*
                 * Existing image explicitly removed.
                 */
                if (
                    ! empty($data[$removeField])
                ) {
                    $data[$field] = null;

                    if ($currentPath) {
                        $pathsToDelete[] = $currentPath;
                    }

                    unset($data[$removeField]);

                    continue;
                }

                /*
                 * No image change.
                 */
                unset($data[$field]);
                unset($data[$removeField]);
            }
        } catch (Throwable $exception) {
            /*
             * Remove any files that were uploaded before
             * another image failed.
             */
            $this->deleteImages($newImagePaths);

            throw $exception;
        }

        return $pathsToDelete;
    }

    /*
    |--------------------------------------------------------------------------
    | Store Uploaded Images
    |--------------------------------------------------------------------------
    */

    private function storeUploadedImages(
        array &$data
    ): array {
        $storedPaths = [];

        try {
            foreach (self::IMAGE_FIELDS as $field) {
                if (
                    ! isset($data[$field]) ||
                    ! $data[$field] instanceof UploadedFile
                ) {
                    unset($data[$field]);

                    continue;
                }

                $storedPath = $this->imageService->store(
                    $data[$field]
                );

                $data[$field] = $storedPath;

                $storedPaths[] = $storedPath;
            }
        } catch (Throwable $exception) {
            $this->deleteImages($storedPaths);

            throw $exception;
        }

        return $storedPaths;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Images
    |--------------------------------------------------------------------------
    */

    private function deleteImages(
        array $paths
    ): void {
        foreach (
            array_unique(
                array_filter($paths)
            ) as $path
        ) {
            $this->imageService->delete($path);
        }
    }
}