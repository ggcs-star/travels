<?php

namespace App\Http\Requests\Admin;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin()
            || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        $blog = $this->route('blog');

        $blogId = $blog instanceof Blog
            ? $blog->id
            : $blog;

        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            'category_id' => [
                'nullable',
                'integer',
                'exists:blog_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blogs', 'slug')
                    ->ignore($blogId),
            ],

            'excerpt' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'content' => [
                'required',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Travel Information
            |--------------------------------------------------------------------------
            */

            'destination' => [
                'nullable',
                'string',
                'max:180',
            ],

            'travel_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'best_time_to_visit' => [
                'nullable',
                'string',
                'max:180',
            ],

            'duration_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],

            'budget_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'budget_max' => [
                'nullable',
                'numeric',
                'gte:budget_min',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
                'alpha',
            ],

            /*
            |--------------------------------------------------------------------------
            | Featured Image
            |--------------------------------------------------------------------------
            */

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
            ],

            'featured_image_alt' => [
                'nullable',
                'string',
                'max:255',
            ],

            'featured_image_caption' => [
                'nullable',
                'string',
                'max:500',
            ],

            'remove_featured_image' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Video
            |--------------------------------------------------------------------------
            */

            'video_url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                Rule::in([
                    Blog::STATUS_DRAFT,
                    Blog::STATUS_PUBLISHED,
                    Blog::STATUS_SCHEDULED,
                    Blog::STATUS_INACTIVE,
                ]),
            ],

            'featured' => [
                'nullable',
                'boolean',
            ],

            'published_at' => [
                'nullable',
                'date',
            ],

            'scheduled_at' => [
                'nullable',
                'date',
                'after:now',
            ],

            /*
            |--------------------------------------------------------------------------
            | Tags
            |--------------------------------------------------------------------------
            */

            'tags' => [
                'nullable',
                'array',
                'max:30',
            ],

            'tags.*' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | New Gallery Images
            |--------------------------------------------------------------------------
            */

            'gallery' => [
                'nullable',
                'array',
                'max:20',
            ],

            'gallery.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
            ],

            'gallery_alt' => [
                'nullable',
                'array',
            ],

            'gallery_alt.*' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Existing Gallery Management
            |--------------------------------------------------------------------------
            */

            'remove_images' => [
                'nullable',
                'array',
            ],

            'remove_images.*' => [
                'integer',
                'exists:blog_images,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAQs
            |--------------------------------------------------------------------------
            */

            'faqs' => [
                'nullable',
                'array',
                'max:30',
            ],

            'faqs.*.question' => [
                'required',
                'string',
                'max:500',
            ],

            'faqs.*.answer' => [
                'required',
                'string',
                'max:5000',
            ],

            'faqs.*.is_active' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Videos
            |--------------------------------------------------------------------------
            */

            'videos' => [
                'nullable',
                'array',
                'max:10',
            ],

            'videos.*.url' => [
                'required',
                'url',
                'max:1000',
            ],

            'videos.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'videos.*.thumbnail' => [
                'nullable',
                'string',
                'max:500',
            ],

            'videos.*.is_active' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Related Tours
            |--------------------------------------------------------------------------
            */

            'related_tours' => [
                'nullable',
                'array',
                'max:20',
            ],

            'related_tours.*' => [
                'integer',
                'distinct',
                'exists:tour_packages,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'seo_key' => [
                'nullable',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blogs', 'seo_key')
                    ->ignore($blogId),
            ],

            'robots' => [
                'nullable',
                Rule::in([
                    'index,follow',
                    'index,nofollow',
                    'noindex,follow',
                    'noindex,nofollow',
                ]),
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'meta_keywords' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'canonical_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Open Graph
            |--------------------------------------------------------------------------
            */

            'og_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'og_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'og_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
            ],

            'remove_og_image' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Input
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => strtoupper(
                (string) $this->input(
                    'currency',
                    'INR'
                )
            ),

            'featured' => $this->boolean(
                'featured'
            ),

            'remove_featured_image' => $this->boolean(
                'remove_featured_image'
            ),

            'remove_og_image' => $this->boolean(
                'remove_og_image'
            ),

            'robots' => $this->input(
                'robots',
                'index,follow'
            ),
        ]);
    }
}