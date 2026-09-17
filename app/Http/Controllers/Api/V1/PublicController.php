<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Blog;
use App\Models\Page;
use App\Models\TourCategory;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Services\AllowedFileExtensionsService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class PublicController extends ApiController
{

    public function destinations()
    {
        $destinations = TourPackage::query()
            ->available()
            ->whereNotNull('destination')
            ->where('destination', '!=', '')
            ->selectRaw('MIN(id) as id, destination')
            ->groupBy('destination')
            ->orderBy('destination')
            ->get();

        return $this->success(
            $destinations,
            'Destinations retrieved successfully.'
        );
    }

    public function categories()
    {
        $categories = TourCategory::query()
            ->active()
            ->withCount(['packages' => fn ($q) => $q->available()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug', 'icon', 'image', 'description']);

        return $this->success($categories, 'Categories retrieved successfully.');
    }

    public function tours(Request $request)
    {
        $perPage = min(50, max(1, $request->integer('per_page', 12)));

        $tours = TourPackage::query()
            ->available()
            ->when($request->filled('search'), fn ($q) => $q->search($request->string('search')->toString()))
            ->when($request->filled('category'), function ($q) use ($request) {
                $category = trim($request->string('category')->toString());
                $q->whereHas('category', fn ($cq) => $cq->where('slug', $category));
            })
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->with([
                'category:id,name,slug',
                'images' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'departures' => fn ($q) => $q->bookable()->orderBy('departure_date')->limit(1),
            ])
            ->orderByDesc('featured')
            ->latest('id')
            ->paginate($perPage);

        return $this->success($tours, 'Tours retrieved successfully.');
    }

    public function tour(TourPackage $tour)
    {
        abort_unless($tour->isAvailable(), 404);

        $tour->load([
            'category',
            'images' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'departures' => fn ($q) => $q->bookable()->orderBy('departure_date'),
        ]);

        return $this->success($tour, 'Tour details retrieved successfully.');
    }

    public function departure(TourDeparture $departure)
    {
        $departure->load('tourPackage:id,name,slug,status');

        abort_unless(
            $departure->tourPackage
            && $departure->tourPackage->isAvailable()
            && $departure->status === TourDeparture::STATUS_OPEN
            && $departure->departure_date?->isFuture(),
            404
        );

        return $this->success([
            'id' => $departure->id,
            'tour_package_id' => $departure->tour_package_id,
            'departure_date' => $departure->departure_date?->toDateString(),
            'return_date' => $departure->return_date?->toDateString(),
            'capacity' => $departure->capacity,
            'available_seats' => $departure->available_seats,
            'price' => $departure->effective_price,
            'currency' => $departure->currency,
            'meeting_point' => $departure->meeting_point,
            'status' => $departure->status,
            'tour' => $departure->tourPackage,
        ], 'Departure details retrieved successfully.');
    }

    public function blogs(Request $request)
    {
        $perPage = min(50, max(1, $request->integer('per_page', 12)));

        $blogs = Blog::query()
            ->published()
            ->when($request->filled('search'), fn ($q) => $q->search($request->string('search')->toString()))
            ->with([
                'category:id,name,slug',
                'author:id,username',
            ])
            ->latest('published_at')
            ->paginate($perPage);

        return $this->success($blogs, 'Blog posts retrieved successfully.');
    }

    public function blog(string $slug)
    {
        $blog = Blog::query()
            ->published()
            ->with([
                'category:id,name,slug',
                'author:id,username',
                'images',
                'tags',
                'faqs',
                'videos',
                'relatedTours.departures',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->success($blog, 'Blog post retrieved successfully.');
    }

    public function page(string $slug)
    {
        $page = Page::query()
            ->published()
            ->with('seo')
            ->where('slug', ltrim($slug, '/'))
            ->firstOrFail();

        return $this->success($page, 'Page retrieved successfully.');
    }

    public function publicSettings(SettingsService $settings)
    {
        $keys = [
            'site.name',
            'site.tagline',
            'site.description',
            'site.phone',
            'site.email',
            'site.whatsapp',
            'site.address',
            'site.working_hours',
            'visual.primary_color',
            'visual.secondary_color',
            'visual.accent_color',
            'visual.text_color',
            'visual.background_color',
            'visual.container_width',
            'visual.border_radius',
            'visual.button_style',
            'visual.card_style',
            'visual.theme_mode',
            'visual.body_font',
            'visual.heading_font',
            'visual.nav_font',
            'visual.button_font',
            'visual.body_weight',
            'visual.heading_weight',
            'visual.font_size',
            'visual.line_height',
            'visual.logo',
            'visual.favicon',
            'header.logo',
            'header.logo_alt',
            'footer.logo',
            'footer.logo_alt',
            'seo.title',
            'seo.home_title',
            'seo.description',
            'seo.keywords',
            'seo.robots',
            'seo.og_title',
            'seo.og_description',
            'seo.twitter_card',
            'uploads.allowed_extensions',
        ];

        $all = $settings->all();

        $data = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $all)) {
                $data[$key] = $all[$key];
            }
        }

        if (! isset($data['uploads.allowed_extensions'])) {
            $data['uploads.allowed_extensions'] =
                app(AllowedFileExtensionsService::class)->get();
        }

        return $this->success($data, 'Public settings retrieved successfully.');
    }
}
