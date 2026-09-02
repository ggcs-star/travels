<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourCategoryController extends Controller
{
    /**
     * Display tour categories.
     */
    public function index(): View
    {
        $categories = TourCategory::query()
            ->withCount('packages')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view(
            'admin.tour-categories.index',
            compact('categories')
        );
    }


    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.tour-categories.create');
    }


    /**
     * Store category.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                'unique:tour_categories,slug',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);


        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $data['status'] = (bool) $data['status'];


        TourCategory::create($data);


        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(TourCategory $tourCategory): View
    {
        return view(
            'admin.tour-categories.edit',
            compact('tourCategory')
        );
    }


    /**
     * Update category.
     */
    public function update(
        Request $request,
        TourCategory $tourCategory
    ): RedirectResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                'unique:tour_categories,slug,' . $tourCategory->id,
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);


        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $data['status'] = (bool) $data['status'];


        $tourCategory->update($data);


        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category updated successfully.'
            );
    }


    /**
     * Delete category.
     */
    public function destroy(
        TourCategory $tourCategory
    ): RedirectResponse {
        /*
         * Prevent deleting a category that is already
         * assigned to tour packages.
         */
        if ($tourCategory->packages()->exists()) {
            return redirect()
                ->route('admin.tour-categories.index')
                ->with(
                    'error',
                    'This category is assigned to tour packages. Deactivate it instead of deleting it.'
                );
        }


        $tourCategory->delete();


        return redirect()
            ->route('admin.tour-categories.index')
            ->with(
                'success',
                'Tour category deleted successfully.'
            );
    }
}