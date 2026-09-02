<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = BlogCategory::query()
            ->withCount('blogs')
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim(
                        (string) $request->input('search')
                    );

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'is_active',
                        $request->input('status') === 'active'
                    );
                }
            )
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.blog.categories.index',
            compact('categories')
        );
    }

    public function create(): View
    {
        return view('admin.blog.categories.create');
    }

    public function store(
        StoreBlogCategoryRequest $request
    ): RedirectResponse|JsonResponse {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('blog/categories', 'public');
        }

        $category = BlogCategory::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Blog category created successfully.',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'sort_order' => $category->sort_order,
                    'is_active' => $category->is_active,
                ],
            ], 201);
        }

        return redirect()
            ->route('admin.blog-categories.index')
            ->with(
                'success',
                'Blog category created successfully.'
            );
    }

    public function edit(
        BlogCategory $blog_category
    ): View {
        return view(
            'admin.blog.categories.edit',
            [
                'category' => $blog_category,
            ]
        );
    }

    public function update(
        UpdateBlogCategoryRequest $request,
        BlogCategory $blog_category
    ): RedirectResponse {
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($blog_category->image) {
                Storage::disk('public')->delete(
                    $blog_category->image
                );
            }

            $data['image'] = $request
                ->file('image')
                ->store('blog/categories', 'public');
        }

        $blog_category->update($data);

        return redirect()
            ->route('admin.blog-categories.index')
            ->with(
                'success',
                'Blog category updated successfully.'
            );
    }

    public function destroy(
        BlogCategory $blog_category
    ): RedirectResponse {
        if ($blog_category->blogs()->exists()) {
            return back()->with(
                'error',
                'This category cannot be deleted because blogs are assigned to it.'
            );
        }

        if ($blog_category->image) {
            Storage::disk('public')->delete(
                $blog_category->image
            );
        }

        $blog_category->delete();

        return redirect()
            ->route('admin.blog-categories.index')
            ->with(
                'success',
                'Blog category deleted successfully.'
            );
    }

    public function status(
        Request $request,
        BlogCategory $blog_category
    ): RedirectResponse {
        $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $blog_category->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            'Blog category status updated successfully.'
        );
    }
}
