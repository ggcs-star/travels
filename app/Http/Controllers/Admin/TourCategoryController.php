<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourCategoryRequest;
use App\Http\Requests\Admin\UpdateTourCategoryRequest;
use App\Models\TourCategory;
use App\Services\Tour\TourCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourCategoryController extends Controller
{
    public function __construct(
        private readonly TourCategoryService $categoryService
    ) {}

    /**
     * Display tour categories.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $search = trim((string) $request->input('search', ''));

        $status = $request->input('status');

        $featured = $request->input('featured');


        /*
        |--------------------------------------------------------------------------
        | Category Query
        |--------------------------------------------------------------------------
        */

        $categories = TourCategory::query()
            ->with([
                'parent:id,name',
            ])
            ->withCount([
                'packages',
                'children',
            ])

            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {

                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere(
                                'short_description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->when(
                $status !== null && $status !== '',
                function ($query) use ($status) {
                    $query->where(
                        'status',
                        (bool) $status
                    );
                }
            )

            ->when(
                $featured !== null && $featured !== '',
                function ($query) use ($featured) {
                    $query->where(
                        'featured',
                        (bool) $featured
                    );
                }
            )

            ->orderBy('sort_order')
            ->orderBy('name')

            ->paginate(10)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Global Statistics
        |--------------------------------------------------------------------------
        |
        | These are calculated independently from pagination.
        | Therefore page 2/3/etc. will not change the statistics.
        |
        */

        $totalCategories = TourCategory::query()->count();

        $activeCategories = TourCategory::query()
            ->where('status', true)
            ->count();

        $inactiveCategories = TourCategory::query()
            ->where('status', false)
            ->count();

        $featuredCategories = TourCategory::query()
            ->where('featured', true)
            ->count();

        $assignedPackages = TourCategory::query()
            ->withCount('packages')
            ->get()
            ->sum('packages_count');


        return view(
            'admin.tour-categories.index',
            compact(
                'categories',
                'search',
                'status',
                'featured',
                'totalCategories',
                'activeCategories',
                'inactiveCategories',
                'featuredCategories',
                'assignedPackages'
            )
        );
    }


    /**
     * Show create form.
     */
    public function create(): View
    {
        $parentCategories = TourCategory::query()
            ->whereNull('parent_id')
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view(
            'admin.tour-categories.create',
            compact('parentCategories')
        );
    }


    /**
     * Store category.
     */
    public function store(
        StoreTourCategoryRequest $request
    ): RedirectResponse {
        $this->categoryService->create(
            $request->validated(),
            (int) $request->user()->id
        );

        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category created successfully.'
            );
    }


    /**
     * Show category.
     */
    public function show(
        TourCategory $category
    ): View {
        $category->load([
            'parent:id,name',

            'children' => function ($query) {
                $query
                    ->withCount('packages')
                    ->orderBy('sort_order')
                    ->orderBy('name');
            },
        ]);

        $category->loadCount('packages');

        return view(
            'admin.tour-categories.show',
            [
                'tourCategory' => $category,
            ]
        );
    }


    /**
     * Show edit form.
     */
    public function edit(
        TourCategory $category
    ): View {
        $parentCategories = TourCategory::query()
            ->whereNull('parent_id')
            ->where('status', true)
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view(
            'admin.tour-categories.edit',
            [
                'tourCategory' => $category,
                'parentCategories' => $parentCategories,
            ]
        );
    }


    /**
     * Update category.
     */
    public function update(
        UpdateTourCategoryRequest $request,
        TourCategory $category
    ): RedirectResponse {
        $this->categoryService->update(
            $category,
            $request->validated()
        );

        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category updated successfully.'
            );
    }


    /**
     * Duplicate category.
     */
    public function duplicate(
        TourCategory $category
    ): RedirectResponse {
        $this->categoryService->duplicate(
            $category
        );

        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category duplicated successfully.'
            );
    }


    /**
     * Change category status.
     */
    public function status(
        Request $request,
        TourCategory $category
    ): RedirectResponse {
        $data = $request->validate([
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $this->categoryService->changeStatus(
            $category,
            (bool) $data['status']
        );

        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category status updated successfully.'
            );
    }


    /**
     * Delete category.
     */
    public function destroy(
        TourCategory $category
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Packages Check
        |--------------------------------------------------------------------------
        */

        if ($category->packages()->exists()) {
            return redirect()
                ->route('admin.tour-categories.index')
                ->with(
                    'error',
                    'This category is assigned to tour packages. Deactivate it instead of deleting it.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Children Check
        |--------------------------------------------------------------------------
        */

        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.tour-categories.index')
                ->with(
                    'error',
                    'This category contains sub-categories. Remove or move them before deleting the category.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        $category->delete();

        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category deleted successfully.'
            );
    }
}