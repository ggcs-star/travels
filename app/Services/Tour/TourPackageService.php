<?php

namespace App\Services\Tour;

use App\Models\TourPackage;
use App\Models\TourDeparture;
use App\Services\Media\TourPackageImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class TourPackageService
{
    public function __construct(
        private readonly TourPackageImageService $imageService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        int $userId
    ): TourPackage {
        $uploadedPaths = [];

        $gallery = $data['gallery'] ?? [];

        unset($data['gallery']);

        try {
            return DB::transaction(function () use (
                $data,
                $gallery,
                $userId,
                &$uploadedPaths
            ) {
                $data = $this->preparePackageData($data);
                $data['itinerary'] = $this->processItineraryImages(
                    $data['itinerary'] ?? [],
                    $uploadedPaths
                );

                /*
                 * Always generate a unique package slug.
                 */
                $data['slug'] = $this->generateUniqueSlug(
                    $data['slug'] ?? $data['name']
                );

                /*
                 * SEO key fallback.
                 */
                $data['seo_key'] = $this->generateUniqueSeoKey(
                    $data['seo_key']
                        ?? $data['slug']
                        ?? $data['name']
                );

                /*
                 * Defaults.
                 */
                $data['featured'] = (bool) (
                    $data['featured'] ?? false
                );

                $data['robots'] = $data['robots']
                    ?? 'index,follow';

                $data['created_by'] = $userId;

                /*
                |--------------------------------------------------------------------------
                | Cover Image
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['cover_image']) &&
                    $data['cover_image'] instanceof UploadedFile
                ) {
                    $coverPath = $this->imageService->store(
                        $data['cover_image']
                    );

                    $uploadedPaths[] = $coverPath;

                    $data['cover_image'] = $coverPath;
                } else {
                    unset($data['cover_image']);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Package
                |--------------------------------------------------------------------------
                */

                $tour = TourPackage::create($data);

                /*
                |--------------------------------------------------------------------------
                | Gallery
                |--------------------------------------------------------------------------
                */

                $this->storeGalleryImages(
                    $tour,
                    $gallery,
                    $uploadedPaths
                );

                return $tour->fresh([
                    'category',
                    'images',
                    'departures',
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteUploadedFiles(
                $uploadedPaths
            );

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        TourPackage $tour,
        array $data
    ): TourPackage {
        $previousCoverImage = $tour->cover_image;

        $gallery = $data['gallery'] ?? [];

        $galleryOrder = $data['gallery_order'] ?? [];

        $removeGallery = $data['remove_gallery'] ?? [];
$hasDepartures = array_key_exists('departures', $data);
$departures = $data['departures'] ?? [];
        unset(
    $data['gallery'],
    $data['gallery_order'],
    $data['remove_gallery'],
    $data['departures']
);
        $uploadedPaths = [];

        $deletedGalleryPaths = [];

        try {
           $updatedTour = DB::transaction(function () use (
    $tour,
    $data,
    $gallery,
    $galleryOrder,
    $removeGallery,
    $hasDepartures,
    $departures,
    &$uploadedPaths,
    &$deletedGalleryPaths
) {
                $data = $this->preparePackageData(
                    $data
                );
                $data['itinerary'] = $this->processItineraryImages(
                    $data['itinerary'] ?? [],
                    $uploadedPaths,
                    $tour->itinerary ?? []
                );

                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['slug']) &&
                    trim($data['slug']) !== ''
                ) {
                    $data['slug'] = $this->generateUniqueSlug(
                        $data['slug'],
                        $tour->id
                    );
                } elseif (
                    isset($data['name']) &&
                    trim($data['name']) !== ''
                ) {
                    $data['slug'] = $this->generateUniqueSlug(
                        $data['name'],
                        $tour->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | SEO Key
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['seo_key']) &&
                    trim($data['seo_key']) !== ''
                ) {
                    $data['seo_key'] = $this->generateUniqueSeoKey(
                        $data['seo_key'],
                        $tour->id
                    );
                } elseif (
                    isset($data['slug']) &&
                    trim($data['slug']) !== ''
                ) {
                    $data['seo_key'] = $this->generateUniqueSeoKey(
                        $data['slug'],
                        $tour->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Featured
                |--------------------------------------------------------------------------
                */

                $data['featured'] = (bool) (
                    $data['featured']
                    ?? $tour->featured
                );

                /*
                |--------------------------------------------------------------------------
                | Robots
                |--------------------------------------------------------------------------
                */

                $data['robots'] = $data['robots']
                    ?? $tour->robots
                    ?? 'index,follow';

                /*
                |--------------------------------------------------------------------------
                | Cover Image
                |--------------------------------------------------------------------------
                */

                $removeCoverImage = (bool) (
                    $data['remove_cover_image']
                    ?? false
                );

                unset(
                    $data['remove_cover_image']
                );

                if (
                    isset($data['cover_image']) &&
                    $data['cover_image'] instanceof UploadedFile
                ) {
                    $newCoverPath = $this->imageService->store(
                        $data['cover_image']
                    );

                    $uploadedPaths[] = $newCoverPath;

                    $data['cover_image'] = $newCoverPath;
                } elseif ($removeCoverImage) {
                    $data['cover_image'] = null;
                } else {
                    unset($data['cover_image']);
                }

                /*
                |--------------------------------------------------------------------------
                | Update Package
                |--------------------------------------------------------------------------
                */

$tour->update($data);

if ($hasDepartures) {
    $this->syncDepartures(
        $tour,
        $departures
    );
}
                /*
                |--------------------------------------------------------------------------
                | Remove Gallery Images
                |--------------------------------------------------------------------------
                */

                if (! empty($removeGallery)) {
                    $removeGallery = array_map(
                        'intval',
                        $removeGallery
                    );

                    $imagesToRemove = $tour->images()
                        ->whereIn(
                            'id',
                            $removeGallery
                        )
                        ->get();

                    foreach ($imagesToRemove as $image) {
                        if ($image->image_path) {
                            $deletedGalleryPaths[] =
                                $image->image_path;
                        }

                        $image->delete();
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Reorder Existing Gallery
                |--------------------------------------------------------------------------
                */

                if (! empty($galleryOrder)) {
                    $galleryOrder = array_map(
                        'intval',
                        $galleryOrder
                    );

                    foreach (
                        $galleryOrder as $sortOrder => $imageId
                    ) {
                        $tour->images()
                            ->where(
                                'id',
                                $imageId
                            )
                            ->update([
                                'sort_order' => $sortOrder,
                            ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Add New Gallery Images
                |--------------------------------------------------------------------------
                */

                $this->storeGalleryImages(
                    $tour,
                    $gallery,
                    $uploadedPaths
                );

                return $tour->fresh([
                    'category',
                    'images',
                    'departures',
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | Delete Previous Cover
            |--------------------------------------------------------------------------
            */

            if (
                $previousCoverImage &&
                $previousCoverImage !==
                    $updatedTour->cover_image &&
                ! TourPackage::withTrashed()
                    ->where(
                        'cover_image',
                        $previousCoverImage
                    )
                    ->exists()
            ) {
                $this->imageService->delete(
                    $previousCoverImage
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Removed Gallery Files
            |--------------------------------------------------------------------------
            */

            foreach (
                $deletedGalleryPaths as $path
            ) {
                $this->imageService->delete(
                    $path
                );
            }

            return $updatedTour;
        } catch (Throwable $exception) {
            $this->deleteUploadedFiles(
                $uploadedPaths
            );

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        TourPackage $tour
    ): TourPackage {
        $tour->load([
            'category',
            'images',
        ]);

        $copiedPaths = [];

        try {
            return DB::transaction(
                function () use (
                    $tour,
                    &$copiedPaths
                ) {
                    $copy = $tour->replicate();

                    /*
                    |--------------------------------------------------------------------------
                    | Basic duplicate values
                    |--------------------------------------------------------------------------
                    */

                    $copy->package_code =
                        $this->generateUniquePackageCode(
                            $tour->package_code
                        );

                    $copy->name =
                        $tour->name . ' Copy';

                    $copy->slug =
                        $this->generateUniqueSlug(
                            $copy->name
                        );

                    $copy->seo_key =
                        $this->generateUniqueSeoKey(
                            $copy->seo_key
                                ?: $copy->slug
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate starts as draft
                    |--------------------------------------------------------------------------
                    */

                    $copy->status =
                        TourPackage::STATUS_DRAFT;

                    $copy->featured = false;

                    $copy->created_by =
                        auth()->id();

                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate Cover Image
                    |--------------------------------------------------------------------------
                    */

                    if ($tour->cover_image) {
                        $newCoverPath =
                            $this->copyMediaFile(
                                $tour->cover_image
                            );

                        if ($newCoverPath) {
                            $copiedPaths[] =
                                $newCoverPath;

                            $copy->cover_image =
                                $newCoverPath;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Save Package
                    |--------------------------------------------------------------------------
                    */

                    $copy->save();

                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate Gallery
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $tour->images as $image
                    ) {
                        $newPath =
                            $this->copyMediaFile(
                                $image->image_path
                            );

                        if (! $newPath) {
                            continue;
                        }

                        $copiedPaths[] =
                            $newPath;

                        $copy->images()->create([
                            'image_path' =>
                                $newPath,

                            'alt_text' =>
                                $image->alt_text
                                    ?: $copy->name,

                            'sort_order' =>
                                $image->sort_order,
                        ]);
                    }

                    return $copy->fresh([
                        'category',
                        'images',
                        'departures',
                    ]);
                }
            );
        } catch (Throwable $exception) {
            $this->deleteUploadedFiles(
                $copiedPaths
            );

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        TourPackage $tour,
        string $status
    ): TourPackage {
        if (
            ! in_array(
                $status,
                [
                    TourPackage::STATUS_DRAFT,
                    TourPackage::STATUS_PUBLISHED,
                    TourPackage::STATUS_INACTIVE,
                ],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                'Invalid tour package status.'
            );
        }

        $tour->update([
            'status' => $status,
        ]);

        return $tour->fresh([
            'category',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Gallery
    |--------------------------------------------------------------------------
    */

    private function storeGalleryImages(
        TourPackage $tour,
        array $gallery,
        array &$uploadedPaths
    ): void {
        if (empty($gallery)) {
            return;
        }

        $lastSortOrder = (int) (
            $tour->images()
                ->max('sort_order')
                ?? -1
        );

        foreach ($gallery as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $imagePath =
                $this->imageService->store(
                    $image
                );

            $uploadedPaths[] =
                $imagePath;

            $lastSortOrder++;

            $tour->images()->create([
                'image_path' =>
                    $imagePath,

                'alt_text' =>
                    $tour->name,

                'sort_order' =>
                    $lastSortOrder,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Package Data
    |--------------------------------------------------------------------------
    */

    private function preparePackageData(
        array $data
    ): array {
        unset(
            $data['remove_cover_image']
        );

        /*
        |--------------------------------------------------------------------------
        | Clean strings
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'package_code',
                'name',
                'slug',
                'seo_key',
                'destination',
                'starting_city',
                'ending_city',
                'short_description',
                'description',
                'difficulty_level',
                'best_time',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'robots',

                // Dynamic tour content / PDF sections.
                'important_notes',
                'terms_conditions',
                'cancellation_policy',
                'privacy_policy',
            ] as $field
        ) {
            if (
                array_key_exists(
                    $field,
                    $data
                ) &&
                is_string($data[$field])
            ) {
                $data[$field] =
                    trim($data[$field]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize slugs
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['slug']) &&
            $data['slug'] !== ''
        ) {
            $data['slug'] =
                Str::slug(
                    $data['slug']
                );
        }

        if (
            isset($data['seo_key']) &&
            $data['seo_key'] !== ''
        ) {
            $data['seo_key'] =
                Str::slug(
                    $data['seo_key']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Clean list fields
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'highlights',
                'included_items',
                'excluded_items',
            ] as $field
        ) {
            if (
                ! array_key_exists(
                    $field,
                    $data
                )
            ) {
                continue;
            }

            if (! is_array($data[$field])) {
                $data[$field] = [];

                continue;
            }

            $data[$field] = collect(
                $data[$field]
            )
                ->map(
                    fn ($item) =>
                        is_string($item)
                            ? trim($item)
                            : $item
                )
                ->filter(
                    fn ($item) =>
                        is_string($item)
                            ? $item !== ''
                            : ! is_null($item)
                )
                ->values()
                ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Itinerary
        |--------------------------------------------------------------------------
        |
        | Keep a predictable structure so the admin UI, storefront and
        | dynamic PDF all consume exactly the same database data.
        |
        */

        if (array_key_exists('itinerary', $data)) {
            if (! is_array($data['itinerary'])) {
                $data['itinerary'] = [];
            } else {
                $normalizedItinerary = [];

                foreach (
                    $data['itinerary'] as $index => $day
                ) {
                    if (! is_array($day)) {
                        continue;
                    }

                    $dayNumber = (int) (
                        $day['day']
                        ?? ($index + 1)
                    );

                    if ($dayNumber < 1) {
                        $dayNumber = $index + 1;
                    }

                    $title = trim(
                        (string) (
                            $day['title']
                            ?? $day['name']
                            ?? $day['heading']
                            ?? ''
                        )
                    );

                    $description = trim(
                        (string) (
                            $day['description']
                            ?? $day['details']
                            ?? $day['content']
                            ?? ''
                        )
                    );

                    $location = trim(
                        (string) (
                            $day['location']
                            ?? $day['places']
                            ?? ''
                        )
                    );

                    $image = $day['image']
                        ?? $day['image_path']
                        ?? null;

                    if (! $image instanceof UploadedFile && $image !== null) {
                        $image = trim((string) $image);
                    }

                    $activities = $day['activities']
                        ?? $day['items']
                        ?? [];

                    if (is_string($activities)) {
                        $activities = preg_split(
                            '/\R/',
                            $activities,
                            -1,
                            PREG_SPLIT_NO_EMPTY
                        );
                    }

                    if (! is_array($activities)) {
                        $activities = [];
                    }

                    $activities = collect($activities)
                        ->map(function ($activity) {
                            if (is_array($activity)) {
                                return trim(
                                    (string) (
                                        $activity['title']
                                        ?? $activity['name']
                                        ?? $activity['text']
                                        ?? ''
                                    )
                                );
                            }

                            return trim((string) $activity);
                        })
                        ->filter()
                        ->values()
                        ->all();

                    // Ignore completely empty itinerary rows.
                    if (
                        $title === ''
                        && $description === ''
                        && empty($activities)
                    ) {
                        continue;
                    }

                    $normalizedItinerary[] = [
                        'day' => $dayNumber,
                        'title' => $title,
                        'location' => $location,
                        'description' => $description,
                        'image' => $image,
                        'activities' => $activities,
                    ];
                }

                $data['itinerary'] = collect(
                    $normalizedItinerary
                )
                    ->sortBy('day')
                    ->values()
                    ->all();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Policy / Notes Fields
        |--------------------------------------------------------------------------
        |
        | These are intentionally kept as strings because they may contain
        | line breaks / HTML entered by the admin and will be rendered by
        | both the website and the PDF.
        |
        */

        foreach (
            [
                'important_notes',
                'terms_conditions',
                'cancellation_policy',
                'privacy_policy',
            ] as $field
        ) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            if ($data[$field] === null) {
                $data[$field] = null;

                continue;
            }

            $data[$field] = trim(
                (string) $data[$field]
            );
        }

        return $data;
    }

    /**
     * Store images uploaded for individual itinerary days.
     * Existing image paths are retained during updates when no replacement
     * image is uploaded.
     */
    private function processItineraryImages(
        array $itinerary,
        array &$uploadedPaths,
        array $existingItinerary = []
    ): array {
        $existingByDay = collect($existingItinerary)
            ->filter(fn ($day) => is_array($day))
            ->keyBy(fn ($day) => (int) ($day['day'] ?? 0));

        foreach ($itinerary as $index => &$day) {
            if (! is_array($day)) {
                continue;
            }

            $dayNumber = (int) ($day['day'] ?? ($index + 1));
            $uploadedImage = $day['image'] ?? null;

            if ($uploadedImage instanceof UploadedFile) {
                $path = $this->imageService->store($uploadedImage);
                $uploadedPaths[] = $path;
                $day['image'] = $path;
                continue;
            }

            $currentImage = is_array($existingByDay->get($dayNumber))
                ? ($existingByDay->get($dayNumber)['image']
                    ?? $existingByDay->get($dayNumber)['image_path']
                    ?? null)
                : null;

            if (! empty($currentImage)) {
                $day['image'] = $currentImage;
            } else {
                $day['image'] = null;
            }
        }

        unset($day);

        return array_values($itinerary);
    }

    /*
    |--------------------------------------------------------------------------
    | Unique Slug
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSlug(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'tour-package';
        }

        $slug = $baseSlug;

        $counter = 1;

        while (
            TourPackage::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoreId !== null,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $counter++;

            $slug =
                $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Unique SEO Key
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSeoKey(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseKey = Str::slug($value);

        if ($baseKey === '') {
            $baseKey = 'tour-package';
        }

        $seoKey = $baseKey;

        $counter = 1;

        while (
            TourPackage::withTrashed()
                ->where('seo_key', $seoKey)
                ->when(
                    $ignoreId !== null,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $counter++;

            $seoKey =
                $baseKey . '-' . $counter;
        }

        return $seoKey;
    }

    /*
    |--------------------------------------------------------------------------
    | Unique Package Code
    |--------------------------------------------------------------------------
    */

    private function generateUniquePackageCode(
        string $originalCode
    ): string {
        $baseCode =
            $originalCode . '-COPY';

        $code = $baseCode;

        $counter = 1;

        while (
            TourPackage::withTrashed()
                ->where(
                    'package_code',
                    $code
                )
                ->exists()
        ) {
            $counter++;

            $code =
                $baseCode . '-' . $counter;
        }

        return $code;
    }

    /*
    |--------------------------------------------------------------------------
    | Copy Media
    |--------------------------------------------------------------------------
    */

    private function copyMediaFile(
        ?string $path
    ): ?string {
        if (! $path) {
            return null;
        }

        if (
            Str::startsWith(
                $path,
                [
                    'http://',
                    'https://',
                    '//',
                ]
            )
        ) {
            return $path;
        }

        $disk = 'public';

        if (
            ! Storage::disk($disk)
                ->exists($path)
        ) {
            return null;
        }

        $extension =
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            );

        $newPath =
            'tour-packages/'
            . Str::uuid()
            . (
                $extension
                    ? '.' . $extension
                    : ''
            );

        Storage::disk($disk)->copy(
            $path,
            $newPath
        );

        return $newPath;
    }



    /*
|--------------------------------------------------------------------------
| Sync Departures
|--------------------------------------------------------------------------
*/

private function syncDepartures(
    TourPackage $tour,
    array $departures
): void {
    $submittedIds = [];

    foreach ($departures as $departureData) {
        $departureData = (array) $departureData;

        $departureId = isset($departureData['id'])
            ? (int) $departureData['id']
            : null;

        unset($departureData['id']);

        /*
        |--------------------------------------------------------------------------
        | Existing Departure
        |--------------------------------------------------------------------------
        */

        if ($departureId) {
            $departure = $tour->departures()
                ->whereKey($departureId)
                ->first();

            if (! $departure) {
                throw new \RuntimeException(
                    'The selected departure does not belong to this tour package.'
                );
            }

            $reservedSeats = $departure->bookings()
                ->reserving()
                ->sum('traveller_count');

            if (
                isset($departureData['capacity'])
                && (int) $departureData['capacity'] < $reservedSeats
            ) {
                throw new \RuntimeException(
                    "Capacity cannot be lower than the {$reservedSeats} reserved seats."
                );
            }

            $departure->update($departureData);

            $submittedIds[] = $departure->id;

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | New Departure
        |--------------------------------------------------------------------------
        */

        $newDeparture = $tour->departures()->create(
            $departureData
        );

        $submittedIds[] = $newDeparture->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Removed Departures
    |--------------------------------------------------------------------------
    */

    $existingDepartures = $tour->departures()->get();

    foreach ($existingDepartures as $departure) {
        if (in_array($departure->id, $submittedIds, true)) {
            continue;
        }

        $hasBookings = $departure->bookings()->exists();

        if ($hasBookings) {
            $departure->update([
                'status' => TourDeparture::STATUS_CLOSED,
            ]);

            continue;
        }

        $departure->delete();
    }
}


    /*
    |--------------------------------------------------------------------------
    | Delete Uploaded Files
    |--------------------------------------------------------------------------
    */

    private function deleteUploadedFiles(
        array $paths
    ): void {
        foreach (
            array_unique($paths)
            as $path
        ) {
            $this->imageService->delete(
                $path
            );
        }
    }
}