<?php

namespace App\Http\Requests\Admin;

use App\Models\TourDeparture;
use App\Models\TourPackage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreTourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            'category_id' => [
                'required',
                'integer',
                'exists:tour_categories,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Basic Package Information
            |--------------------------------------------------------------------------
            */

            'package_code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:tour_packages,package_code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:tour_packages,slug',
            ],

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

            'destination' => [
                'required',
                'string',
                'max:150',
            ],

            'starting_city' => [
                'required',
                'string',
                'max:150',
            ],

            'ending_city' => [
                'required',
                'string',
                'max:150',
            ],

            /*
            |--------------------------------------------------------------------------
            | Content
            |--------------------------------------------------------------------------
            */

            'short_description' => [
                'required',
                'string',
                'max:500',
            ],

            'description' => [
                'required',
                'string',
                'max:20000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Duration
            |--------------------------------------------------------------------------
            */

            'duration_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'duration_nights' => [
                'required',
                'integer',
                'min:0',
                'max:364',
                'lte:duration_days',
            ],

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'difficulty_level' => [
                'nullable',
                'string',
                Rule::in([
                    'easy',
                    'moderate',
                    'challenging',
                    'difficult',
                ]),
            ],

            'age_min' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'age_max' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
                'gte:age_min',
            ],

            'best_time' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Highlights
            |--------------------------------------------------------------------------
            */

            'highlights' => [
                'nullable',
                'array',
                'max:30',
            ],

            'highlights.*' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Included Items
            |--------------------------------------------------------------------------
            */

            'included_items' => [
                'nullable',
                'array',
                'max:30',
            ],

            'included_items.*' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Excluded Items
            |--------------------------------------------------------------------------
            */

            'excluded_items' => [
                'nullable',
                'array',
                'max:30',
            ],

            'excluded_items.*' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Cover Image
            |--------------------------------------------------------------------------
            */

            'cover_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:5120',
                'dimensions:min_width=800,min_height=500,max_width=4096,max_height=4096',
            ],

            /*
            |--------------------------------------------------------------------------
            | Gallery
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
            | Package Controls
            |--------------------------------------------------------------------------
            */

            'featured' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'required',
                Rule::in([
                    TourPackage::STATUS_DRAFT,
                    TourPackage::STATUS_PUBLISHED,
                    TourPackage::STATUS_INACTIVE,
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'seo_key' => [
                'required',
                'string',
                'max:180',
                'alpha_dash',
                'unique:tour_packages,seo_key',
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:500',
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
                Rule::in([
                    'index,follow',
                    'index,nofollow',
                    'noindex,follow',
                    'noindex,nofollow',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | STEP 09 — TOUR DEPARTURES
            |--------------------------------------------------------------------------
            */

            'departures' => [
                'required',
                'array',
                'min:1',
            ],

            'departures.*.departure_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'departures.*.return_date' => [
                'required',
                'date',
                'after_or_equal:departures.*.departure_date',
            ],

            'departures.*.capacity' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],

            'departures.*.price' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'departures.*.sale_price' => [
                'nullable',
                'decimal:0,2',
                'min:0',
                'lt:departures.*.price',
            ],

            'departures.*.currency' => [
                'required',
                'string',
                'size:3',
                'alpha',
            ],

            'departures.*.meeting_point' => [
                'nullable',
                'string',
                'max:255',
            ],

            'departures.*.status' => [
                'required',
                Rule::in([
                    TourDeparture::STATUS_OPEN,
                    TourDeparture::STATUS_CLOSED,
                    TourDeparture::STATUS_CANCELLED,
                ]),
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
        $data = [];

        /*
        |--------------------------------------------------------------------------
        | Package code
        |--------------------------------------------------------------------------
        */

        if ($this->filled('package_code')) {
            $data['package_code'] = strtoupper(
                trim($this->input('package_code'))
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        if ($this->filled('slug')) {
            $data['slug'] = Str::slug(
                $this->input('slug')
            );
        } elseif ($this->filled('name')) {
            $data['slug'] = Str::slug(
                $this->input('name')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SEO key
        |--------------------------------------------------------------------------
        */

        if ($this->filled('seo_key')) {
            $data['seo_key'] = Str::slug(
                $this->input('seo_key')
            );
        } elseif ($this->filled('slug')) {
            $data['seo_key'] = Str::slug(
                $this->input('slug')
            );
        } elseif ($this->filled('name')) {
            $data['seo_key'] = Str::slug(
                $this->input('name')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Boolean
        |--------------------------------------------------------------------------
        */

        $data['featured'] = $this->boolean('featured');

        /*
        |--------------------------------------------------------------------------
        | Default robots
        |--------------------------------------------------------------------------
        */

        $data['robots'] = $this->input('robots')
            ?: 'index,follow';

        /*
        |--------------------------------------------------------------------------
        | Convert textarea lists into arrays.
        |--------------------------------------------------------------------------
        */

        foreach ([
            'highlights',
            'included_items',
            'excluded_items',
        ] as $field) {
            $value = $this->input($field);

            if (! is_string($value)) {
                continue;
            }

            $items = preg_split(
                '/\R/',
                $value,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $data[$field] = collect($items)
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | Departure normalization
        |--------------------------------------------------------------------------
        */

        $departures = $this->input('departures', []);

        if (is_array($departures)) {
            foreach ($departures as $index => $departure) {
                if (! is_array($departure)) {
                    continue;
                }

                $departures[$index]['currency'] = strtoupper(
                    trim(
                        (string) (
                            $departure['currency'] ?? 'INR'
                        )
                    )
                );

                $departures[$index]['status'] = strtolower(
                    trim(
                        (string) (
                            $departure['status']
                            ?? TourDeparture::STATUS_OPEN
                        )
                    )
                );

                if (
                    isset($departure['meeting_point'])
                    && is_string($departure['meeting_point'])
                ) {
                    $departures[$index]['meeting_point'] =
                        trim($departure['meeting_point']);
                }
            }
        }

        $data['departures'] = $departures;

        $this->merge($data);
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Validation
    |--------------------------------------------------------------------------
    */

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $departures = $this->input(
                'departures',
                []
            );

            if (! is_array($departures)) {
                return;
            }

            $dates = [];

            foreach ($departures as $index => $departure) {

                if (! is_array($departure)) {
                    continue;
                }

                $date = $departure['departure_date'] ?? null;

                if (! $date) {
                    continue;
                }

                if (in_array($date, $dates, true)) {
                    $validator->errors()->add(
                        "departures.{$index}.departure_date",
                        'The departure date must be unique for this tour package.'
                    );
                }

                $dates[] = $date;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [
            'category_id.required' =>
                'Please select a tour category.',

            'category_id.exists' =>
                'The selected tour category is invalid.',

            'package_code.required' =>
                'Package code is required.',

            'package_code.unique' =>
                'This package code is already in use.',

            'package_code.alpha_dash' =>
                'Package code may only contain letters, numbers, dashes and underscores.',

            'name.required' =>
                'Package name is required.',

            'slug.required' =>
                'Package URL slug is required.',

            'slug.unique' =>
                'This package URL is already in use.',

            'destination.required' =>
                'Destination is required.',

            'starting_city.required' =>
                'Starting city is required.',

            'ending_city.required' =>
                'Ending city is required.',

            'short_description.required' =>
                'Short description is required.',

            'description.required' =>
                'Package description is required.',

            'duration_days.required' =>
                'Duration in days is required.',

            'duration_days.min' =>
                'Duration must be at least 1 day.',

            'duration_nights.required' =>
                'Duration in nights is required.',

            'duration_nights.lte' =>
                'Duration nights cannot exceed duration days.',

            'age_max.gte' =>
                'Maximum age must be greater than or equal to minimum age.',

            'cover_image.required' =>
                'Main package image is required.',

            'cover_image.image' =>
                'Main package image must be a valid image.',

            'cover_image.max' =>
                'Main package image may not be larger than 5 MB.',

            'cover_image.dimensions' =>
                'Main package image must be between 800×500 and 4096×4096 pixels.',

            'gallery.max' =>
                'You can upload a maximum of 20 gallery images.',

            'gallery.*.image' =>
                'Each gallery file must be a valid image.',

            'gallery.*.max' =>
                'Each gallery image may not be larger than 5 MB.',

            'seo_key.required' =>
                'SEO key is required.',

            'seo_key.unique' =>
                'This SEO key is already in use.',

            'seo_key.alpha_dash' =>
                'SEO key may only contain letters, numbers, dashes and underscores.',

            'canonical_url.url' =>
                'Please enter a valid canonical URL.',

            'status.required' =>
                'Package status is required.',

            /*
            |--------------------------------------------------------------------------
            | Departure Messages
            |--------------------------------------------------------------------------
            */

            'departures.required' =>
                'Please add at least one tour departure.',

            'departures.min' =>
                'Please add at least one tour departure.',

            'departures.*.departure_date.required' =>
                'Departure date is required.',

            'departures.*.departure_date.after_or_equal' =>
                'Departure date cannot be in the past.',

            'departures.*.return_date.required' =>
                'Return date is required.',

            'departures.*.return_date.after_or_equal' =>
                'Return date must be on or after the departure date.',

            'departures.*.capacity.required' =>
                'Departure capacity is required.',

            'departures.*.capacity.min' =>
                'Departure capacity must be at least 1.',

            'departures.*.capacity.max' =>
                'Departure capacity cannot exceed 1,000 travellers.',

            'departures.*.price.required' =>
                'Departure price is required.',

            'departures.*.price.min' =>
                'Departure price cannot be negative.',

            'departures.*.sale_price.lt' =>
                'Sale price must be lower than the regular price.',

            'departures.*.currency.required' =>
                'Departure currency is required.',

            'departures.*.currency.size' =>
                'Currency must be a 3-letter code.',

            'departures.*.status.required' =>
                'Departure status is required.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [
            'category_id' => 'tour category',

            'package_code' => 'package code',

            'starting_city' => 'starting city',

            'ending_city' => 'ending city',

            'short_description' => 'short description',

            'duration_days' => 'duration days',

            'duration_nights' => 'duration nights',

            'difficulty_level' => 'difficulty level',

            'age_min' => 'minimum age',

            'age_max' => 'maximum age',

            'best_time' => 'best time',

            'cover_image' => 'main image',

            'gallery.*' => 'gallery image',

            'seo_key' => 'SEO key',

            'meta_title' => 'meta title',

            'meta_description' => 'meta description',

            'meta_keywords' => 'meta keywords',

            'canonical_url' => 'canonical URL',

            'robots' => 'robots directive',

            'departures.*.departure_date' =>
                'departure date',

            'departures.*.return_date' =>
                'return date',

            'departures.*.capacity' =>
                'departure capacity',

            'departures.*.price' =>
                'departure price',

            'departures.*.sale_price' =>
                'departure sale price',

            'departures.*.currency' =>
                'departure currency',

            'departures.*.meeting_point' =>
                'meeting point',

            'departures.*.status' =>
                'departure status',
        ];
    }
}