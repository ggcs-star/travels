<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
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
                    ->whereNull('deleted_at'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Category Mapping
            |--------------------------------------------------------------------------
            */

            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('tour_categories', 'id')
                    ->where(function ($query) {
                        $query->where('status', true)
                            ->whereNull('deleted_at');
                    }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
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
            'name' => is_string($this->name)
                ? trim($this->name)
                : $this->name,

            'slug' => filled($this->slug)
                ? strtolower(trim($this->slug))
                : null,

            'icon' => filled($this->icon)
                ? trim($this->icon)
                : null,

            'short_description' => filled($this->short_description)
                ? trim($this->short_description)
                : null,

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

            'status' => $this->boolean('status'),

            'featured' => $this->boolean('featured'),
        ]);
    }
}