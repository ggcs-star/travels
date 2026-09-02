<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateTourPackageRequest extends StoreTourPackageRequest
{
    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $tour = $this->route('tour');

        $tourId = is_object($tour)
            ? $tour->id
            : $tour;

        return array_replace_recursive(
            parent::rules(),
            [
                /*
                |--------------------------------------------------------------------------
                | Package Code
                |--------------------------------------------------------------------------
                */

                'package_code' => [
                    'required',
                    'string',
                    'max:50',
                    'alpha_dash',
                    Rule::unique('tour_packages', 'package_code')
                        ->ignore($tourId),
                ],

                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'alpha_dash',
                    Rule::unique('tour_packages', 'slug')
                        ->ignore($tourId),
                ],

                /*
                |--------------------------------------------------------------------------
                | SEO Key
                |--------------------------------------------------------------------------
                */

                'seo_key' => [
                    'required',
                    'string',
                    'max:180',
                    'alpha_dash',
                    Rule::unique('tour_packages', 'seo_key')
                        ->ignore($tourId),
                ],

                /*
                |--------------------------------------------------------------------------
                | Cover Image
                |--------------------------------------------------------------------------
                */

                'cover_image' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp,avif',
                    'max:5120',
                    'dimensions:min_width=800,min_height=500,max_width=4096,max_height=4096',
                ],

                /*
                |--------------------------------------------------------------------------
                | Remove Cover Image
                |--------------------------------------------------------------------------
                */

                'remove_cover_image' => [
                    'nullable',
                    'boolean',
                ],

                /*
                |--------------------------------------------------------------------------
                | Gallery Images
                |--------------------------------------------------------------------------
                */

                'gallery' => [
                    'nullable',
                    'array',
                    'max:20',
                ],

                'gallery.*' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp,avif',
                    'max:5120',
                ],

                /*
                |--------------------------------------------------------------------------
                | Existing Gallery Order
                |--------------------------------------------------------------------------
                */

                'gallery_order' => [
                    'nullable',
                    'array',
                    'max:20',
                ],

                'gallery_order.*' => [
                    'integer',
                    'distinct',
                    'exists:tour_package_images,id',
                ],

                /*
                |--------------------------------------------------------------------------
                | Remove Gallery
                |--------------------------------------------------------------------------
                */

                'remove_gallery' => [
                    'nullable',
                    'array',
                    'max:20',
                ],

                'remove_gallery.*' => [
                    'integer',
                    'distinct',
                    'exists:tour_package_images,id',
                ],
            ]
        );
    }

    /**
     * Additional validation after normal rules.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tour = $this->route('tour');

            if (! $tour) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Get images belonging to this package
            |--------------------------------------------------------------------------
            */

            $tourImageIds = $tour->images()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Validate gallery order
            |--------------------------------------------------------------------------
            */

            foreach (
                (array) $this->input('gallery_order', [])
                as $imageId
            ) {
                if (
                    ! in_array(
                        (int) $imageId,
                        $tourImageIds,
                        true
                    )
                ) {
                    $validator->errors()->add(
                        'gallery_order',
                        'One or more selected gallery images do not belong to this tour package.'
                    );

                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Validate gallery deletion
            |--------------------------------------------------------------------------
            */

            foreach (
                (array) $this->input('remove_gallery', [])
                as $imageId
            ) {
                if (
                    ! in_array(
                        (int) $imageId,
                        $tourImageIds,
                        true
                    )
                ) {
                    $validator->errors()->add(
                        'remove_gallery',
                        'One or more selected gallery images do not belong to this tour package.'
                    );

                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent deleting and keeping the same image
            |--------------------------------------------------------------------------
            */

            $galleryOrder = collect(
                (array) $this->input('gallery_order', [])
            )
                ->map(fn ($id) => (int) $id)
                ->unique();

            $removeGallery = collect(
                (array) $this->input('remove_gallery', [])
            )
                ->map(fn ($id) => (int) $id)
                ->unique();

            $conflictingImages = $galleryOrder
                ->intersect($removeGallery);

            if ($conflictingImages->isNotEmpty()) {
                $validator->errors()->add(
                    'remove_gallery',
                    'An image cannot be kept and deleted at the same time.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Cover Image Removal Protection
            |--------------------------------------------------------------------------
            |
            | If no replacement image is uploaded, do not allow the package
            | to become without a cover image.
            |
            */

            if (
                $this->boolean('remove_cover_image')
                && ! $this->hasFile('cover_image')
                && ! $tour->cover_image
            ) {
                $validator->errors()->add(
                    'remove_cover_image',
                    'This package does not have a cover image to remove.'
                );
            }
        });
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            [
                'slug.unique' =>
                    'This package URL is already in use.',

                'seo_key.unique' =>
                    'This SEO key is already in use.',

                'remove_cover_image.boolean' =>
                    'Invalid cover image removal option.',

                'gallery_order.max' =>
                    'You can manage a maximum of 20 gallery images at once.',

                'remove_gallery.max' =>
                    'You can remove a maximum of 20 gallery images at once.',
            ]
        );
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return array_merge(
            parent::attributes(),
            [
                'slug' => 'package URL',
                'seo_key' => 'SEO key',
                'meta_title' => 'meta title',
                'meta_description' => 'meta description',
                'meta_keywords' => 'meta keywords',
                'canonical_url' => 'canonical URL',
                'robots' => 'robots directive',
                'remove_cover_image' => 'remove cover image',
                'gallery_order.*' => 'gallery image',
                'remove_gallery.*' => 'gallery image',
            ]
        );
    }
}