<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAGE LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $query = Page::query()
            ->with('seo')
            ->latest('id');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MENU LOCATION
        |--------------------------------------------------------------------------
        */

        if ($request->filled('menu_location')) {
            $query->where(
                'menu_location',
                $request->input('menu_location')
            );
        }

        $pages = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.pages.index',
            compact('pages')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $parentPages = Page::query()
            ->published()
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'slug',
            ]);

        return view(
            'admin.pages.create',
            compact('parentPages')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->rules()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $page = new Page();

            $this->fillPage(
                $page,
                $request
            );

            $page->created_by = auth()->id();
            $page->updated_by = auth()->id();

            $page->save();

            $this->saveSeo(
                $page,
                $request
            );
        });

        return redirect()
            ->route('admin.pages.index')
            ->with(
                'success',
                'Page created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Page $page): View
    {
        $page->load('seo');

        $parentPages = Page::query()
            ->whereKeyNot($page->id)
            ->published()
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'slug',
            ]);

        return view(
            'admin.pages.edit',
            compact(
                'page',
                'parentPages'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Page $page
    ): RedirectResponse {
        $validator = Validator::make(
            $request->all(),
            $this->rules($page)
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use (
            $request,
            $page
        ) {
            $oldImage = $page->featured_image;

            $this->fillPage(
                $page,
                $request
            );

            $page->updated_by = auth()->id();

            $page->save();

            if (
                $oldImage &&
                $oldImage !== $page->featured_image &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete(
                    $oldImage
                );
            }

            $this->saveSeo(
                $page,
                $request
            );
        });

        return redirect()
            ->route(
                'admin.pages.edit',
                $page
            )
            ->with(
                'success',
                'Page updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Page $page): RedirectResponse
    {
        DB::transaction(function () use ($page) {
            $page->delete();
        });

        return redirect()
            ->route('admin.pages.index')
            ->with(
                'success',
                'Page deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function status(Page $page): RedirectResponse
    {
        $page->status = $page->status === 'published'
            ? 'draft'
            : 'published';

        $page->updated_by = auth()->id();

        $page->save();

        return back()->with(
            'success',
            $page->status === 'published'
                ? 'Page published successfully.'
                : 'Page moved to draft.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC PAGE
    |--------------------------------------------------------------------------
    */

    public function show(string $slug): View
    {
        $slug = trim(
            $slug,
            '/'
        );

        $page = Page::query()
            ->with('seo')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view(
            'pages.show',
            compact('page')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function rules(
        ?Page $page = null
    ): array {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9\/_-]+$/',
                Rule::unique('pages', 'slug')
                    ->ignore(
                        $page?->id
                    ),
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'image_alt' => [
                'nullable',
                'string',
                'max:255',
            ],

            'template' => [
                'required',
                Rule::in([
                    'default',
                    'full-width',
                    'landing',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                ]),
            ],

            'menu_location' => [
                'required',
                Rule::in([
                    'none',
                    'header',
                    'footer',
                    'both',
                ]),
            ],

            'header_position' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'header_parent_id' => [
                'nullable',
                'integer',
                'exists:pages,id',
            ],

            'footer_column' => [
                'nullable',
                'string',
                'max:100',
            ],

            'footer_position' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
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
                'max:5000',
            ],

            'keywords' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'canonical_url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'robots' => [
                'nullable',
                'string',
                'max:100',
            ],

            'og_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'og_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'og_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'twitter_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'twitter_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'twitter_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FILL PAGE
    |--------------------------------------------------------------------------
    */

    protected function fillPage(
        Page $page,
        Request $request
    ): void {
        $page->title = trim(
            $request->input('title')
        );

        $page->slug = trim(
            $request->input('slug'),
            '/'
        );

        $page->excerpt =
            $request->input('excerpt');

        $page->content =
            $request->input('content');

        $page->image_alt =
            $request->input('image_alt');

        $page->template =
            $request->input('template', 'default');

        $page->status =
            $request->input('status', 'draft');

        $page->menu_location =
            $request->input(
                'menu_location',
                'none'
            );

        $page->header_position =
            (int) $request->input(
                'header_position',
                0
            );

        $page->header_parent_id =
            $request->filled('header_parent_id')
                ? (int) $request->input(
                    'header_parent_id'
                )
                : null;

        $page->footer_column =
            $request->input('footer_column');

        $page->footer_position =
            (int) $request->input(
                'footer_position',
                0
            );

        $page->sort_order =
            (int) $request->input(
                'sort_order',
                0
            );

        /*
        |--------------------------------------------------------------------------
        | FEATURED IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('featured_image')
        ) {
            $page->featured_image =
                $request
                    ->file('featured_image')
                    ->store(
                        'pages',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            $request->boolean('remove_featured_image')
        ) {
            if (
                $page->featured_image &&
                Storage::disk('public')->exists(
                    $page->featured_image
                )
            ) {
                Storage::disk('public')->delete(
                    $page->featured_image
                );
            }

            $page->featured_image = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    protected function saveSeo(
        Page $page,
        Request $request
    ): void {
        $seo = $page->seo;

        if (!$seo) {
            $seo = $page->seo()->make();
        }

        $seo->meta_title =
            $request->input('meta_title');

        $seo->meta_description =
            $request->input('meta_description');

        $seo->keywords =
            $request->input('keywords');

        $seo->canonical_url =
            $request->input('canonical_url');

        $seo->robots =
            $request->input(
                'robots',
                'index,follow'
            );

        $seo->og_title =
            $request->input('og_title');

        $seo->og_description =
            $request->input('og_description');

        $seo->twitter_title =
            $request->input('twitter_title');

        $seo->twitter_description =
            $request->input(
                'twitter_description'
            );

        if (
            $request->hasFile('og_image')
        ) {
            if (
                $seo->og_image &&
                Storage::disk('public')->exists(
                    $seo->og_image
                )
            ) {
                Storage::disk('public')->delete(
                    $seo->og_image
                );
            }

            $seo->og_image =
                $request
                    ->file('og_image')
                    ->store(
                        'pages/seo',
                        'public'
                    );
        }

        if (
            $request->hasFile('twitter_image')
        ) {
            if (
                $seo->twitter_image &&
                Storage::disk('public')->exists(
                    $seo->twitter_image
                )
            ) {
                Storage::disk('public')->delete(
                    $seo->twitter_image
                );
            }

            $seo->twitter_image =
                $request
                    ->file('twitter_image')
                    ->store(
                        'pages/seo',
                        'public'
                    );
        }

        $seo->save();
    }
}