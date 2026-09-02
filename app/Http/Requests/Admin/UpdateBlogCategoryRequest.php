<?php

namespace App\Http\Requests\Admin;

use App\Models\BlogCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var BlogCategory|null $category */
        $category = $this->route('blog_category');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('blog_categories', 'name')
                    ->ignore($category?->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:160',
                'alpha_dash',
                Rule::unique('blog_categories', 'slug')
                    ->ignore($category?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'slug' => $slug !== ''
                ? Str::slug($slug)
                : null,
            'sort_order' => $this->input('sort_order', 0),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}