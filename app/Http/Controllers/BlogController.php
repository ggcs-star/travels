<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $blogs = Blog::query()
            ->with([
                'category:id,name,slug',
                'author:id,username',
            ])
            ->where('status', Blog::STATUS_PUBLISHED)
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', compact('blogs'));
    }

    public function show(string $slug): View
    {
        $blog = Blog::query()
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
            ->where('status', Blog::STATUS_PUBLISHED)
            ->firstOrFail();

        return view('blog.show', compact('blog'));
    }
}