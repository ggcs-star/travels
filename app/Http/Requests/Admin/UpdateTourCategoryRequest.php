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
        $category = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
            ],

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

            'icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'active',
                    'inactive',
                ]),
            ],

            'featured' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
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
        ];
    }
}
