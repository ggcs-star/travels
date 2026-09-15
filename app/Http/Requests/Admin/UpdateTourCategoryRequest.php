<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $category = $this->route('tourCategory')
            ?? $this->route('category');

        $categoryId = $category?->id;

        return [
            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tour_categories', 'slug')
                    ->ignore($categoryId)
                    ->whereNull('deleted_at'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Parent / Child Mapping
            |--------------------------------------------------------------------------
            */

            'parent_id' => [
                'nullable',
                'integer',
                Rule::notIn([$categoryId]),
                Rule::exists('tour_categories', 'id')
                    ->where(function ($query) use ($categoryId) {
                        $query
                            ->where('id', '!=', $categoryId)
                            ->where('status', true)
                            ->whereNull('deleted_at');
                    }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Content
            |--------------------------------------------------------------------------
            */

            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Category Icon
            |--------------------------------------------------------------------------
            */

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Category Image
            |--------------------------------------------------------------------------
            */

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
                'dimensions:max_width=4096,max_height=4096',
            ],

            'remove_image' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Open Graph Image
            |--------------------------------------------------------------------------
            */

            'og_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
                'dimensions:max_width=4096,max_height=4096',
            ],

            'remove_og_image' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'featured' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

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
                'max:1000',
            ],

            'canonical_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'robots' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            /*
            |--------------------------------------------------------------------------
            | Basic
            |--------------------------------------------------------------------------
            */

            'name' => is_string($this->name)
                ? trim($this->name)
                : $this->name,

            'slug' => filled($this->slug)
                ? strtolower(trim($this->slug))
                : null,

            /*
            |--------------------------------------------------------------------------
            | Content
            |--------------------------------------------------------------------------
            */

            'short_description' => filled($this->short_description)
                ? trim($this->short_description)
                : null,

            'icon' => filled($this->icon)
                ? trim($this->icon)
                : null,

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'meta_title' => filled($this->meta_title)
                ? trim($this->meta_title)
                : null,

            'meta_description' => filled($this->meta_description)
                ? trim($this->meta_description)
                : null,

            'meta_keywords' => filled($this->meta_keywords)
                ? trim($this->meta_keywords)
                : null,

            'canonical_url' => filled($this->canonical_url)
                ? trim($this->canonical_url)
                : null,

            'robots' => filled($this->robots)
                ? trim($this->robots)
                : null,

            /*
            |--------------------------------------------------------------------------
            | Boolean Fields
            |--------------------------------------------------------------------------
            */

            'status' => $this->boolean('status'),

            'featured' => $this->boolean('featured'),

            'remove_image' => $this->boolean('remove_image'),

            'remove_og_image' => $this->boolean('remove_og_image'),
        ]);
    }
}