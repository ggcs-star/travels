<?php

namespace App\Services\Blog;

use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogTag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BlogService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        int $authorId
    ): Blog {
        $uploadedPaths = [];

        $gallery = $data['gallery'] ?? [];
        $galleryAlt = $data['gallery_alt'] ?? [];
        $tags = $data['tags'] ?? [];
        $faqs = $data['faqs'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedTours = $data['related_tours'] ?? [];

        unset(
            $data['gallery'],
            $data['gallery_alt'],
            $data['tags'],
            $data['faqs'],
            $data['videos'],
            $data['related_tours']
        );

        try {
            $blog = DB::transaction(function () use (
                $data,
                $authorId,
                $gallery,
                $galleryAlt,
                $tags,
                $faqs,
                $videos,
                $relatedTours,
                &$uploadedPaths
            ) {
                $data = $this->prepareBlogData($data);

                $data['author_id'] = $authorId;

                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                $data['slug'] = $this->generateUniqueSlug(
                    $data['slug'] ?? $data['title']
                );

                /*
                |--------------------------------------------------------------------------
                | SEO Key
                |--------------------------------------------------------------------------
                */

                $data['seo_key'] = $this->generateUniqueSeoKey(
                    $data['seo_key'] ?? $data['slug']
                );

                /*
                |--------------------------------------------------------------------------
                | Defaults
                |--------------------------------------------------------------------------
                */

                $data['status'] = $data['status']
                    ?? Blog::STATUS_DRAFT;

                $data['featured'] = (bool) (
                    $data['featured'] ?? false
                );

                $data['robots'] = $data['robots']
                    ?? 'index,follow';

                $data['currency'] = $data['currency']
                    ?? 'INR';

                /*
                |--------------------------------------------------------------------------
                | Featured Image
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['featured_image'])
                    && $data['featured_image'] instanceof UploadedFile
                ) {
                    $path = $this->storeImage(
                        $data['featured_image'],
                        'blogs'
                    );

                    $uploadedPaths[] = $path;

                    $data['featured_image'] = $path;
                } else {
                    unset($data['featured_image']);
                }

                /*
                |--------------------------------------------------------------------------
                | OG Image
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['og_image'])
                    && $data['og_image'] instanceof UploadedFile
                ) {
                    $path = $this->storeImage(
                        $data['og_image'],
                        'blogs/og'
                    );

                    $uploadedPaths[] = $path;

                    $data['og_image'] = $path;
                } else {
                    unset($data['og_image']);
                }

                /*
                |--------------------------------------------------------------------------
                | Reading Time
                |--------------------------------------------------------------------------
                */

                $data['reading_time'] =
                    $this->calculateReadingTime(
                        $data['content'] ?? ''
                    );

                /*
                |--------------------------------------------------------------------------
                | Publishing
                |--------------------------------------------------------------------------
                */

                $this->preparePublishingData($data);

                /*
                |--------------------------------------------------------------------------
                | Create
                |--------------------------------------------------------------------------
                */

                $blog = Blog::create($data);

                /*
                |--------------------------------------------------------------------------
                | Gallery
                |--------------------------------------------------------------------------
                */

                $this->storeGallery(
                    $blog,
                    $gallery,
                    $galleryAlt,
                    $uploadedPaths
                );

                /*
                |--------------------------------------------------------------------------
                | Tags
                |--------------------------------------------------------------------------
                */

                $this->syncTags(
                    $blog,
                    $tags
                );

                /*
                |--------------------------------------------------------------------------
                | FAQs
                |--------------------------------------------------------------------------
                */

                $this->syncFaqs(
                    $blog,
                    $faqs
                );

                /*
                |--------------------------------------------------------------------------
                | Videos
                |--------------------------------------------------------------------------
                */

                $this->syncVideos(
                    $blog,
                    $videos
                );

                /*
                |--------------------------------------------------------------------------
                | Related Tours
                |--------------------------------------------------------------------------
                */

                $this->syncRelatedTours(
                    $blog,
                    $relatedTours
                );

                return $blog->fresh([
                    'category',
                    'author',
                    'tags',
                    'images',
                    'faqs',
                    'videos',
                    'relatedTours',
                ]);
            });

            return $blog;
        } catch (Throwable $exception) {
            $this->deleteUploadedFiles(
                $uploadedPaths
            );

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Blog $blog,
        array $data
    ): Blog {
        $oldFeaturedImage =
            $blog->featured_image;

        $oldOgImage =
            $blog->og_image;

        $gallery = $data['gallery'] ?? [];
        $galleryAlt = $data['gallery_alt'] ?? [];
        $tags = $data['tags'] ?? [];
        $faqs = $data['faqs'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedTours = $data['related_tours'] ?? [];
        $removeImages = $data['remove_images'] ?? [];

        $removeFeaturedImage = (bool) (
            $data['remove_featured_image'] ?? false
        );

        $removeOgImage = (bool) (
            $data['remove_og_image'] ?? false
        );

        unset(
            $data['gallery'],
            $data['gallery_alt'],
            $data['tags'],
            $data['faqs'],
            $data['videos'],
            $data['related_tours'],
            $data['remove_images'],
            $data['remove_featured_image'],
            $data['remove_og_image']
        );

        $uploadedPaths = [];
        $deletedPaths = [];

        try {
            $updatedBlog = DB::transaction(function () use (
                $blog,
                $data,
                $gallery,
                $galleryAlt,
                $tags,
                $faqs,
                $videos,
                $relatedTours,
                $removeImages,
                $removeFeaturedImage,
                $removeOgImage,
                &$uploadedPaths,
                &$deletedPaths
            ) {
                $data = $this->prepareBlogData(
                    $data
                );

                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['slug'])
                    && trim($data['slug']) !== ''
                ) {
                    $data['slug'] =
                        $this->generateUniqueSlug(
                            $data['slug'],
                            $blog->id
                        );
                } elseif (
                    isset($data['title'])
                    && trim($data['title']) !== ''
                ) {
                    $data['slug'] =
                        $this->generateUniqueSlug(
                            $data['title'],
                            $blog->id
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | SEO Key
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['seo_key'])
                    && trim($data['seo_key']) !== ''
                ) {
                    $data['seo_key'] =
                        $this->generateUniqueSeoKey(
                            $data['seo_key'],
                            $blog->id
                        );
                } elseif (
                    isset($data['slug'])
                    && trim($data['slug']) !== ''
                ) {
                    $data['seo_key'] =
                        $this->generateUniqueSeoKey(
                            $data['slug'],
                            $blog->id
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Reading Time
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'content',
                        $data
                    )
                ) {
                    $data['reading_time'] =
                        $this->calculateReadingTime(
                            $data['content']
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Featured
                |--------------------------------------------------------------------------
                */

                $data['featured'] = (bool) (
                    $data['featured']
                    ?? $blog->featured
                );

                /*
                |--------------------------------------------------------------------------
                | Robots
                |--------------------------------------------------------------------------
                */

                $data['robots'] =
                    $data['robots']
                    ?? $blog->robots
                    ?? 'index,follow';

                /*
                |--------------------------------------------------------------------------
                | Featured Image
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['featured_image'])
                    && $data['featured_image']
                        instanceof UploadedFile
                ) {
                    $path = $this->storeImage(
                        $data['featured_image'],
                        'blogs'
                    );

                    $uploadedPaths[] = $path;

                    $data['featured_image'] = $path;
                } elseif ($removeFeaturedImage) {
                    $data['featured_image'] = null;
                } else {
                    unset(
                        $data['featured_image']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | OG Image
                |--------------------------------------------------------------------------
                */

                if (
                    isset($data['og_image'])
                    && $data['og_image']
                        instanceof UploadedFile
                ) {
                    $path = $this->storeImage(
                        $data['og_image'],
                        'blogs/og'
                    );

                    $uploadedPaths[] = $path;

                    $data['og_image'] = $path;
                } elseif ($removeOgImage) {
                    $data['og_image'] = null;
                } else {
                    unset(
                        $data['og_image']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Publishing
                |--------------------------------------------------------------------------
                */

                $this->preparePublishingData(
                    $data,
                    $blog
                );

                /*
                |--------------------------------------------------------------------------
                | Update
                |--------------------------------------------------------------------------
                */

                $blog->update($data);

                /*
                |--------------------------------------------------------------------------
                | Remove Gallery Images
                |--------------------------------------------------------------------------
                */

                if (! empty($removeImages)) {
                    $imageIds = array_map(
                        'intval',
                        $removeImages
                    );

                    $images = $blog->images()
                        ->whereIn(
                            'id',
                            $imageIds
                        )
                        ->get();

                    foreach ($images as $image) {
                        if ($image->path) {
                            $deletedPaths[] =
                                $image->path;
                        }

                        $image->delete();
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Add New Gallery Images
                |--------------------------------------------------------------------------
                */

                $this->storeGallery(
                    $blog,
                    $gallery,
                    $galleryAlt,
                    $uploadedPaths
                );

                /*
                |--------------------------------------------------------------------------
                | Tags
                |--------------------------------------------------------------------------
                */

                $this->syncTags(
                    $blog,
                    $tags
                );

                /*
                |--------------------------------------------------------------------------
                | FAQs
                |--------------------------------------------------------------------------
                */

                $this->syncFaqs(
                    $blog,
                    $faqs
                );

                /*
                |--------------------------------------------------------------------------
                | Videos
                |--------------------------------------------------------------------------
                */

                $this->syncVideos(
                    $blog,
                    $videos
                );

                /*
                |--------------------------------------------------------------------------
                | Related Tours
                |--------------------------------------------------------------------------
                */

                $this->syncRelatedTours(
                    $blog,
                    $relatedTours
                );

                return $blog->fresh([
                    'category',
                    'author',
                    'tags',
                    'images',
                    'faqs',
                    'videos',
                    'relatedTours',
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | Delete Old Featured Image
            |--------------------------------------------------------------------------
            */

            if (
                $oldFeaturedImage
                && $oldFeaturedImage
                    !== $updatedBlog->featured_image
                && ! $this->mediaStillUsed(
                    $oldFeaturedImage
                )
            ) {
                $this->deleteMedia(
                    $oldFeaturedImage
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Old OG Image
            |--------------------------------------------------------------------------
            */

            if (
                $oldOgImage
                && $oldOgImage
                    !== $updatedBlog->og_image
                && ! $this->mediaStillUsed(
                    $oldOgImage
                )
            ) {
                $this->deleteMedia(
                    $oldOgImage
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Removed Gallery Files
            |--------------------------------------------------------------------------
            */

            foreach (
                array_unique($deletedPaths)
                as $path
            ) {
                $this->deleteMedia(
                    $path
                );
            }

            return $updatedBlog;
        } catch (Throwable $exception) {
            $this->deleteUploadedFiles(
                $uploadedPaths
            );

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(
        Blog $blog
    ): void {
        $featuredImage =
            $blog->featured_image;

        $ogImage =
            $blog->og_image;

        $galleryImages =
            $blog->images()
                ->pluck('path')
                ->filter()
                ->values()
                ->all();

        DB::transaction(function () use ($blog) {
            /*
            |--------------------------------------------------------------------------
            | Detach Relations
            |--------------------------------------------------------------------------
            */

            $blog->tags()->detach();

            $blog->relatedTours()->detach();

            /*
            |--------------------------------------------------------------------------
            | Delete Child Records
            |--------------------------------------------------------------------------
            */

            $blog->images()->delete();

            $blog->faqs()->delete();

            $blog->videos()->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Blog
            |--------------------------------------------------------------------------
            */

            $blog->delete();
        });

        /*
        |--------------------------------------------------------------------------
        | Delete Media
        |--------------------------------------------------------------------------
        */

        $paths = array_merge(
            array_filter([
                $featuredImage,
                $ogImage,
            ]),
            $galleryImages
        );

        foreach (
            array_unique($paths)
            as $path
        ) {
            $this->deleteMedia(
                $path
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DUPLICATE
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        Blog $blog
    ): Blog {
        $blog->load([
            'tags',
            'images',
            'faqs',
            'videos',
            'relatedTours',
        ]);

        $uploadedPaths = [];

        try {
            return DB::transaction(function () use (
                $blog,
                &$uploadedPaths
            ) {
                /*
                |--------------------------------------------------------------------------
                | Duplicate Basic Blog
                |--------------------------------------------------------------------------
                */

                $copy = $blog->replicate();

                $copy->title =
                    $blog->title
                    . ' (Copy)';

                $copy->slug =
                    $this->generateUniqueSlug(
                        $copy->title
                    );

                $copy->seo_key =
                    $this->generateUniqueSeoKey(
                        $copy->slug
                    );

                $copy->status =
                    Blog::STATUS_DRAFT;

                $copy->featured = false;

                $copy->published_at = null;

                $copy->scheduled_at = null;

                $copy->views = 0;

                $copy->author_id =
                    auth()->id()
                    ?: $blog->author_id;

                /*
                |--------------------------------------------------------------------------
                | Duplicate Featured Image
                |--------------------------------------------------------------------------
                */

                if ($blog->featured_image) {
                    $newPath =
                        $this->duplicateMedia(
                            $blog->featured_image,
                            'blogs'
                        );

                    if ($newPath) {
                        $uploadedPaths[] =
                            $newPath;

                        $copy->featured_image =
                            $newPath;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Duplicate OG Image
                |--------------------------------------------------------------------------
                */

                if ($blog->og_image) {
                    $newPath =
                        $this->duplicateMedia(
                            $blog->og_image,
                            'blogs/og'
                        );

                    if ($newPath) {
                        $uploadedPaths[] =
                            $newPath;

                        $copy->og_image =
                            $newPath;
                    }
                }

                $copy->save();

                /*
                |--------------------------------------------------------------------------
                | Duplicate Gallery
                |--------------------------------------------------------------------------
                */

                foreach (
                    $blog->images
                    as $image
                ) {
                    $newPath =
                        $this->duplicateMedia(
                            $image->path,
                            'blogs/gallery'
                        );

                    if (! $newPath) {
                        continue;
                    }

                    $uploadedPaths[] =
                        $newPath;

                    $copy->images()->create([
                        'path' => $newPath,

                        'alt_text' =>
                            $image->alt_text,

                        'caption' =>
                            $image->caption,

                        'sort_order' =>
                            $image->sort_order,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Tags
                |--------------------------------------------------------------------------
                */

                $copy->tags()->sync(
                    $blog->tags
                        ->pluck('id')
                        ->all()
                );

                /*
                |--------------------------------------------------------------------------
                | FAQs
                |--------------------------------------------------------------------------
                */

                foreach (
                    $blog->faqs
                    as $faq
                ) {
                    $copy->faqs()->create([
                        'question' =>
                            $faq->question,

                        'answer' =>
                            $faq->answer,

                        'sort_order' =>
                            $faq->sort_order,

                        'is_active' =>
                            $faq->is_active,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Videos
                |--------------------------------------------------------------------------
                */

                foreach (
                    $blog->videos
                    as $video
                ) {
                    $copy->videos()->create([
                        'url' =>
                            $video->url,

                        'title' =>
                            $video->title,

                        'thumbnail' =>
                            $video->thumbnail,

                        'sort_order' =>
                            $video->sort_order,

                        'is_active' =>
                            $video->is_active,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Related Tours
                |--------------------------------------------------------------------------
                */

                $tourSync = [];

                foreach (
                    $blog->relatedTours
                    as $tour
                ) {
                    $tourSync[$tour->id] = [
                        'sort_order' =>
                            $tour->pivot->sort_order
                            ?? 0,
                    ];
                }

                $copy->relatedTours()->sync(
                    $tourSync
                );

                return $copy->fresh([
                    'category',
                    'author',
                    'tags',
                    'images',
                    'faqs',
                    'videos',
                    'relatedTours',
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
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        Blog $blog,
        string $status
    ): Blog {
        $allowedStatuses = [
            Blog::STATUS_DRAFT,
            Blog::STATUS_PUBLISHED,
            Blog::STATUS_SCHEDULED,
            Blog::STATUS_INACTIVE,
        ];

        if (
            ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                'Invalid blog status.'
            );
        }

        $data = [
            'status' => $status,
        ];

        /*
        |--------------------------------------------------------------------------
        | Published
        |--------------------------------------------------------------------------
        */

        if (
            $status === Blog::STATUS_PUBLISHED
        ) {
            $data['published_at'] =
                $blog->published_at
                ?? now();

            $data['scheduled_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Scheduled
        |--------------------------------------------------------------------------
        */

        if (
            $status === Blog::STATUS_SCHEDULED
        ) {
            if (
                ! $blog->scheduled_at
            ) {
                throw new \InvalidArgumentException(
                    'A scheduled date is required before scheduling the blog.'
                );
            }

            $data['published_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Draft / Inactive
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $status,
                [
                    Blog::STATUS_DRAFT,
                    Blog::STATUS_INACTIVE,
                ],
                true
            )
        ) {
            $data['published_at'] = null;
            $data['scheduled_at'] = null;
        }

        $blog->update(
            $data
        );

        return $blog->fresh([
            'category',
            'author',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARE BLOG DATA
    |--------------------------------------------------------------------------
    */

    private function prepareBlogData(
        array $data
    ): array {
        $stringFields = [
            'title',
            'slug',
            'excerpt',
            'destination',
            'travel_type',
            'best_time_to_visit',
            'currency',
            'featured_image_alt',
            'featured_image_caption',
            'video_url',
            'robots',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'canonical_url',
            'og_title',
            'og_description',
        ];

        foreach (
            $stringFields
            as $field
        ) {
            if (
                array_key_exists(
                    $field,
                    $data
                )
                && is_string($data[$field])
            ) {
                $data[$field] =
                    trim(
                        $data[$field]
                    );
            }
        }

        if (
            isset($data['slug'])
            && $data['slug'] !== ''
        ) {
            $data['slug'] =
                Str::slug(
                    $data['slug']
                );
        }

        if (
            isset($data['seo_key'])
            && $data['seo_key'] !== ''
        ) {
            $data['seo_key'] =
                Str::slug(
                    $data['seo_key']
                );
        }

        if (
            isset($data['currency'])
            && $data['currency'] !== ''
        ) {
            $data['currency'] =
                strtoupper(
                    $data['currency']
                );
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISHING DATA
    |--------------------------------------------------------------------------
    */

    private function preparePublishingData(
        array &$data,
        ?Blog $blog = null
    ): void {
        $status =
            $data['status']
            ?? $blog?->status
            ?? Blog::STATUS_DRAFT;

        if (
            $status === Blog::STATUS_PUBLISHED
        ) {
            $data['published_at'] =
                $data['published_at']
                ?? $blog?->published_at
                ?? now();

            $data['scheduled_at'] = null;

            return;
        }

        if (
            $status === Blog::STATUS_SCHEDULED
        ) {
            $data['published_at'] = null;

            if (
                empty($data['scheduled_at'])
                && $blog?->scheduled_at
            ) {
                $data['scheduled_at'] =
                    $blog->scheduled_at;
            }

            return;
        }

        $data['published_at'] = null;

        $data['scheduled_at'] = null;
    }

    /*
    |--------------------------------------------------------------------------
    | READING TIME
    |--------------------------------------------------------------------------
    */

    private function calculateReadingTime(
        string $content
    ): int {
        $plainText = trim(
            strip_tags(
                $content
            )
        );

        if (
            $plainText === ''
        ) {
            return 1;
        }

        $wordCount =
            str_word_count(
                $plainText
            );

        return max(
            1,
            (int) ceil(
                $wordCount / 200
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GALLERY
    |--------------------------------------------------------------------------
    */

    private function storeGallery(
        Blog $blog,
        array $gallery,
        array $galleryAlt,
        array &$uploadedPaths
    ): void {
        if (
            empty($gallery)
        ) {
            return;
        }

        $lastSortOrder =
            (int) (
                $blog->images()
                    ->max('sort_order')
                ?? -1
            );

        foreach (
            $gallery
            as $index => $image
        ) {
            if (
                ! $image instanceof UploadedFile
            ) {
                continue;
            }

            $path =
                $this->storeImage(
                    $image,
                    'blogs/gallery'
                );

            $uploadedPaths[] =
                $path;

            $lastSortOrder++;

            $blog->images()->create([
                'path' => $path,

                'alt_text' =>
                    $galleryAlt[$index]
                    ?? $blog->title,

                'caption' => null,

                'sort_order' =>
                    $lastSortOrder,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TAGS
    |--------------------------------------------------------------------------
    */

    private function syncTags(
        Blog $blog,
        array $tags
    ): void {
        $tagIds = [];

        foreach (
            $tags
            as $tag
        ) {
            if (
                ! is_string($tag)
            ) {
                continue;
            }

            $name =
                trim($tag);

            if (
                $name === ''
            ) {
                continue;
            }

            $slug =
                Str::slug($name);

            if (
                $slug === ''
            ) {
                continue;
            }

            $tagModel =
                BlogTag::query()
                    ->where(
                        'slug',
                        $slug
                    )
                    ->first();

            if (
                ! $tagModel
            ) {
                $tagModel =
                    BlogTag::create([
                        'name' => $name,

                        'slug' =>
                            $this->generateUniqueTagSlug(
                                $slug
                            ),
                    ]);
            }

            $tagIds[] =
                $tagModel->id;
        }

        $blog->tags()->sync(
            array_values(
                array_unique(
                    $tagIds
                )
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FAQS
    |--------------------------------------------------------------------------
    */

    private function syncFaqs(
        Blog $blog,
        array $faqs
    ): void {
        $blog->faqs()->delete();

        foreach (
            array_values($faqs)
            as $sortOrder => $faq
        ) {
            if (
                ! is_array($faq)
            ) {
                continue;
            }

            $question =
                trim(
                    (string) (
                        $faq['question']
                        ?? ''
                    )
                );

            $answer =
                trim(
                    (string) (
                        $faq['answer']
                        ?? ''
                    )
                );

            if (
                $question === ''
                || $answer === ''
            ) {
                continue;
            }

            $blog->faqs()->create([
                'question' =>
                    $question,

                'answer' =>
                    $answer,

                'sort_order' =>
                    $sortOrder,

                'is_active' =>
                    (bool) (
                        $faq['is_active']
                        ?? true
                    ),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VIDEOS
    |--------------------------------------------------------------------------
    */

    private function syncVideos(
        Blog $blog,
        array $videos
    ): void {
        $blog->videos()->delete();

        foreach (
            array_values($videos)
            as $sortOrder => $video
        ) {
            if (
                ! is_array($video)
            ) {
                continue;
            }

            $url =
                trim(
                    (string) (
                        $video['url']
                        ?? ''
                    )
                );

            if (
                $url === ''
            ) {
                continue;
            }

            $blog->videos()->create([
                'url' =>
                    $url,

                'title' =>
                    isset($video['title'])
                        ? trim(
                            (string) $video['title']
                        )
                        : null,

                'thumbnail' =>
                    $video['thumbnail']
                    ?? null,

                'sort_order' =>
                    $sortOrder,

                'is_active' =>
                    (bool) (
                        $video['is_active']
                        ?? true
                    ),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATED TOURS
    |--------------------------------------------------------------------------
    */

    private function syncRelatedTours(
        Blog $blog,
        array $tourIds
    ): void {
        $syncData = [];

        $tourIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $tourIds
                )
            )
        );

        foreach (
            $tourIds
            as $sortOrder => $tourId
        ) {
            if (
                $tourId <= 0
            ) {
                continue;
            }

            $syncData[$tourId] = [
                'sort_order' =>
                    $sortOrder,
            ];
        }

        $blog->relatedTours()->sync(
            $syncData
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE BLOG SLUG
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSlug(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseSlug =
            Str::slug($value);

        if (
            $baseSlug === ''
        ) {
            $baseSlug =
                'travel-blog';
        }

        $slug =
            $baseSlug;

        $counter = 1;

        while (
            Blog::query()
                ->where(
                    'slug',
                    $slug
                )
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
                $baseSlug
                . '-'
                . $counter;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE SEO KEY
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSeoKey(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseKey =
            Str::slug($value);

        if (
            $baseKey === ''
        ) {
            $baseKey =
                'travel-blog';
        }

        $seoKey =
            $baseKey;

        $counter = 1;

        while (
            Blog::query()
                ->where(
                    'seo_key',
                    $seoKey
                )
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
                $baseKey
                . '-'
                . $counter;
        }

        return $seoKey;
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE TAG SLUG
    |--------------------------------------------------------------------------
    */

    private function generateUniqueTagSlug(
        string $baseSlug
    ): string {
        $slug =
            $baseSlug;

        $counter = 1;

        while (
            BlogTag::query()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $counter++;

            $slug =
                $baseSlug
                . '-'
                . $counter;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | STORE IMAGE
    |--------------------------------------------------------------------------
    */

    private function storeImage(
        UploadedFile $file,
        string $directory
    ): string {
        return $file->store(
            $directory,
            'public'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DUPLICATE MEDIA
    |--------------------------------------------------------------------------
    */

    private function duplicateMedia(
        ?string $path,
        string $directory
    ): ?string {
        if (
            ! $path
        ) {
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

        if (
            ! Storage::disk('public')
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
            $directory
            . '/'
            . Str::uuid()
            . (
                $extension
                    ? '.'.$extension
                    : ''
            );

        Storage::disk('public')->copy(
            $path,
            $newPath
        );

        return $newPath;
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA STILL USED
    |--------------------------------------------------------------------------
    */

    private function mediaStillUsed(
        string $path
    ): bool {
        return Blog::query()
            ->where(
                'featured_image',
                $path
            )
            ->orWhere(
                'og_image',
                $path
            )
            ->exists()
            || BlogImage::query()
                ->where(
                    'path',
                    $path
                )
                ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE MEDIA
    |--------------------------------------------------------------------------
    */

    private function deleteMedia(
        ?string $path
    ): void {
        if (
            ! $path
        ) {
            return;
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
            return;
        }

        Storage::disk('public')
            ->delete($path);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE UPLOADED FILES
    |--------------------------------------------------------------------------
    */

    private function deleteUploadedFiles(
        array $paths
    ): void {
        foreach (
            array_unique($paths)
            as $path
        ) {
            $this->deleteMedia(
                $path
            );
        }
    }
}