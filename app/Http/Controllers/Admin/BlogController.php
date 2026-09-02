<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\TourPackage;
use App\Services\Blog\BlogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogService $blogService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $blogs = Blog::query()
            ->with([
                'category:id,name,slug',
                'author:id,username',
            ])
            ->withCount([
                'tags',
                'images',
                'faqs',
                'videos',
                'relatedTours',
            ])
            ->search(
                $request->input('search')
            )
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status',
                        $request->input('status')
                    )
            )
            ->when(
                $request->filled('category_id'),
                fn ($query) =>
                    $query->where(
                        'category_id',
                        $request->integer('category_id')
                    )
            )
            ->when(
                $request->filled('featured'),
                fn ($query) =>
                    $query->where(
                        'featured',
                        $request->boolean('featured')
                    )
            )
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = BlogCategory::query()
            ->ordered()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return view(
            'admin.blog.index',
            compact(
                'blogs',
                'categories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $categories = BlogCategory::query()
            ->active()
            ->ordered()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tags = BlogTag::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tours = TourPackage::query()
            ->where(
                'status',
                TourPackage::STATUS_PUBLISHED
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'destination',
            ]);

        return view(
            'admin.blog.create',
            compact(
                'categories',
                'tags',
                'tours'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreBlogRequest $request
    ): RedirectResponse {
        $blog = $this->blogService->create(
            $request->validated(),
            (int) auth()->id()
        );

        return redirect()
            ->route(
                'admin.blog.edit',
                $blog
            )
            ->with(
                'success',
                'Blog post created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Blog $blog
    ): View {
        $blog->load([
            'category',
            'author',
            'tags',
            'images',
            'faqs',
            'videos',
            'relatedTours',
        ]);

        return view(
            'admin.blog.show',
            compact('blog')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Blog $blog
    ): View {
        $blog->load([
            'category',
            'author',
            'tags',
            'images',
            'faqs',
            'videos',
            'relatedTours',
        ]);

        $categories = BlogCategory::query()
            ->active()
            ->ordered()
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tags = BlogTag::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $tours = TourPackage::query()
            ->where(
                'status',
                TourPackage::STATUS_PUBLISHED
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'destination',
            ]);

        return view(
            'admin.blog.edit',
            compact(
                'blog',
                'categories',
                'tags',
                'tours'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateBlogRequest $request,
        Blog $blog
    ): RedirectResponse {
        $blog = $this->blogService->update(
            $blog,
            $request->validated()
        );

        return redirect()
            ->route(
                'admin.blog.edit',
                $blog
            )
            ->with(
                'success',
                'Blog post updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Blog $blog
    ): RedirectResponse {
        $this->blogService->delete(
            $blog
        );

        return redirect()
            ->route(
                'admin.blog.index'
            )
            ->with(
                'success',
                'Blog post deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        Blog $blog
    ): RedirectResponse {
        $data = $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', [
                    Blog::STATUS_DRAFT,
                    Blog::STATUS_PUBLISHED,
                    Blog::STATUS_SCHEDULED,
                    Blog::STATUS_INACTIVE,
                ]),
            ],
        ]);

        $this->blogService->changeStatus(
            $blog,
            $data['status']
        );

        return back()->with(
            'success',
            'Blog status updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Featured
    |--------------------------------------------------------------------------
    */

    public function featured(
        Request $request,
        Blog $blog
    ): RedirectResponse {
        $data = $request->validate([
            'featured' => [
                'required',
                'boolean',
            ],
        ]);

        $blog->update([
            'featured' => $data['featured'],
        ]);

        return back()->with(
            'success',
            'Featured status updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        Blog $blog
    ): RedirectResponse {
        $copy = $this->blogService->duplicate(
            $blog
        );

        return redirect()
            ->route(
                'admin.blog.edit',
                $copy
            )
            ->with(
                'success',
                'Blog post duplicated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(
        Blog $blog
    ): View {
        $blog->load([
            'category',
            'author',
            'tags',
            'images',
            'faqs',
            'videos',
            'relatedTours',
        ]);

        return view(
            'blog.show',
            compact('blog')
        );
    }
}