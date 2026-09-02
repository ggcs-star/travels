@extends('admin.layouts.app')

@section('title', 'Blog Posts')

@section('content')

<div class="admin-page blog-admin-page">

    {{-- Header --}}
    <div class="admin-page__header blog-page-header">
        <div>
            <span class="admin-eyebrow">CONTENT / BLOG</span>

            <h1 class="admin-page__title">Blog Posts</h1>

            <p class="admin-page__description">
                Manage your travel articles, guides and stories.
            </p>
        </div>

        <a
            href="{{ route('admin.blog.create') }}"
            class="admin-button admin-button--primary"
        >
            + Create Blog Post
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert--error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="admin-card blog-filter-card">

        <div class="blog-filter-header">
            <div>
                <h2>Find blog posts</h2>
                <p>Search and filter your content.</p>
            </div>

            <span class="blog-total-count">
                {{ number_format($blogs->total()) }}
                {{ Str::plural('post', $blogs->total()) }}
            </span>
        </div>

        <form
            method="GET"
            action="{{ route('admin.blog.index') }}"
            class="blog-filter-form"
        >

            <div class="blog-filter-field blog-filter-field--search">
                <label for="search">Search</label>

                <div class="blog-search-input">
                    <span class="blog-search-icon">⌕</span>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search title or destination..."
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="blog-filter-field">
                <label for="category_id">Category</label>

                <select id="category_id" name="category_id">
                    <option value="">All categories</option>

                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected((string) request('category_id') === (string) $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="blog-filter-field">
                <label for="status">Status</label>

                <select id="status" name="status">
                    <option value="">All status</option>
                    <option value="draft" @selected(request('status') === 'draft')>
                        Draft
                    </option>
                    <option value="published" @selected(request('status') === 'published')>
                        Published
                    </option>
                    <option value="scheduled" @selected(request('status') === 'scheduled')>
                        Scheduled
                    </option>
                    <option value="inactive" @selected(request('status') === 'inactive')>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="blog-filter-field">
                <label for="featured">Featured</label>

                <select id="featured" name="featured">
                    <option value="">All</option>
                    <option value="1" @selected(request('featured') === '1')>
                        Featured
                    </option>
                    <option value="0" @selected(request('featured') === '0')>
                        Not Featured
                    </option>
                </select>
            </div>

            <div class="blog-filter-actions">
                <button
                    type="submit"
                    class="admin-button admin-button--primary"
                >
                    Filter
                </button>

                @if(
                    request()->filled('search')
                    || request()->filled('category_id')
                    || request()->filled('status')
                    || request()->filled('featured')
                )
                    <a
                        href="{{ route('admin.blog.index') }}"
                        class="admin-button"
                    >
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Blog list --}}
    <div class="admin-card blog-table-card">

        <div class="admin-card__header blog-list-header">
            <div>
                <h2>All Blog Posts</h2>
                <p>Manage published and unpublished travel content.</p>
            </div>

            @if($blogs->total() > 0)
                <div class="blog-results-info">
                    {{ $blogs->firstItem() }}–{{ $blogs->lastItem() }}
                    of {{ $blogs->total() }}
                </div>
            @endif
        </div>

        @if($blogs->isNotEmpty())

            {{-- Desktop --}}
            <div class="blog-table-wrapper">

                <table class="blog-table">

                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Stats</th>
                            <th>Published</th>
                            <th class="blog-table-actions-heading">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($blogs as $blog)

                        <tr>

                            {{-- Post --}}
                            <td>
                                <div class="blog-post-cell">

                                    <div class="blog-post-thumb">
                                        @if($blog->featured_image)
                                            <img
                                                src="{{ \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                                                alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <span class="blog-post-thumb-placeholder">✦</span>
                                        @endif
                                    </div>

                                    <div class="blog-post-info">

                                        <div class="blog-post-title-row">
                                            <strong>
                                                {{ Str::limit($blog->title, 65) }}
                                            </strong>

                                            @if($blog->featured)
                                                <span
                                                    class="blog-featured-mini"
                                                    title="Featured"
                                                >
                                                    ★
                                                </span>
                                            @endif
                                        </div>

                                        <small>/{{ $blog->slug }}</small>

                                        @if($blog->excerpt)
                                            <p>
                                                {{ Str::limit($blog->excerpt, 80) }}
                                            </p>
                                        @endif

                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="blog-category-name">
                                    {{ $blog->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($blog->status === \App\Models\Blog::STATUS_PUBLISHED)
                                    <span class="blog-status blog-status--published">
                                        <span></span> Published
                                    </span>
                                @elseif($blog->status === \App\Models\Blog::STATUS_SCHEDULED)
                                    <span class="blog-status blog-status--scheduled">
                                        <span></span> Scheduled
                                    </span>
                                @elseif($blog->status === \App\Models\Blog::STATUS_INACTIVE)
                                    <span class="blog-status blog-status--inactive">
                                        <span></span> Inactive
                                    </span>
                                @else
                                    <span class="blog-status blog-status--draft">
                                        <span></span> Draft
                                    </span>
                                @endif
                            </td>

                            {{-- Author --}}
                            <td>
                                <div class="blog-author-cell">
                                    <div class="blog-author-avatar">
                                        {{ strtoupper(substr($blog->author?->username ?? 'A', 0, 1)) }}
                                    </div>

                                    <span>
                                        {{ $blog->author?->username ?? 'Administrator' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Stats --}}
                            <td>
                                <div class="blog-content-stats">
                                    <span title="Views">
                                        <b>◉</b>
                                        {{ number_format($blog->views ?? 0) }}
                                    </span>

                                    @if($blog->reading_time)
                                        <span title="Reading time">
                                            <b>◷</b>
                                            {{ $blog->reading_time }} min
                                        </span>
                                    @endif

                                    @if(($blog->images_count ?? 0) > 0)
                                        <span title="Gallery images">
                                            <b>▧</b>
                                            {{ $blog->images_count }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Published --}}
                            <td>
                                @if(
                                    $blog->status === \App\Models\Blog::STATUS_SCHEDULED
                                    && $blog->scheduled_at
                                )
                                    <div class="blog-date-cell">
                                        <strong>{{ $blog->scheduled_at->format('d M Y') }}</strong>
                                        <small>{{ $blog->scheduled_at->format('h:i A') }}</small>
                                    </div>
                                @elseif($blog->published_at)
                                    <div class="blog-date-cell">
                                        <strong>{{ $blog->published_at->format('d M Y') }}</strong>
                                        <small>{{ $blog->published_at->format('h:i A') }}</small>
                                    </div>
                                @else
                                    <span class="blog-no-date">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="blog-actions">

                                    <a
                                        href="{{ route('admin.blog.show', $blog) }}"
                                        class="blog-action"
                                        title="View"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.blog.edit', $blog) }}"
                                        class="blog-action"
                                        title="Edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.blog.duplicate', $blog) }}"
                                        class="blog-inline-form"
                                        onsubmit="return confirm('Create a copy of this blog post?')"
                                    >
                                        @csrf

                                        <button type="submit" class="blog-action">
                                            Duplicate
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.blog.featured', $blog) }}"
                                        class="blog-inline-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="featured"
                                            value="{{ $blog->featured ? 0 : 1 }}"
                                        >

                                        <button
                                            type="submit"
                                            class="blog-action {{ $blog->featured ? 'blog-action--active' : '' }}"
                                        >
                                            {{ $blog->featured ? '★ Featured' : '☆ Feature' }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.blog.status', $blog) }}"
                                        class="blog-inline-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="{{ $blog->status === \App\Models\Blog::STATUS_PUBLISHED ? 'draft' : 'published' }}"
                                        >

                                        <button type="submit" class="blog-action">
                                            {{ $blog->status === \App\Models\Blog::STATUS_PUBLISHED ? 'Draft' : 'Publish' }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.blog.destroy', $blog) }}"
                                        class="blog-inline-form"
                                        onsubmit="return confirm('Delete this blog post permanently?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="blog-action blog-action--danger"
                                        >
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                    </tbody>
                </table>

            </div>

            {{-- Mobile --}}
            <div class="blog-mobile-list">

                @foreach($blogs as $blog)

                    <article class="blog-mobile-card">

                        <div class="blog-mobile-card__top">

                            <div class="blog-post-thumb">
                                @if($blog->featured_image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                                        alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                                        loading="lazy"
                                    >
                                @else
                                    <span class="blog-post-thumb-placeholder">✦</span>
                                @endif
                            </div>

                            <div class="blog-mobile-card__info">

                                <div class="blog-post-title-row">
                                    <strong>
                                        {{ Str::limit($blog->title, 60) }}
                                    </strong>

                                    @if($blog->featured)
                                        <span class="blog-featured-mini">★</span>
                                    @endif
                                </div>

                                <small>
                                    {{ $blog->category?->name ?? 'Uncategorized' }}
                                </small>

                            </div>

                        </div>

                        <div class="blog-mobile-card__meta">

                            @if($blog->status === \App\Models\Blog::STATUS_PUBLISHED)
                                <span class="blog-status blog-status--published">
                                    <span></span> Published
                                </span>
                            @elseif($blog->status === \App\Models\Blog::STATUS_SCHEDULED)
                                <span class="blog-status blog-status--scheduled">
                                    <span></span> Scheduled
                                </span>
                            @elseif($blog->status === \App\Models\Blog::STATUS_INACTIVE)
                                <span class="blog-status blog-status--inactive">
                                    <span></span> Inactive
                                </span>
                            @else
                                <span class="blog-status blog-status--draft">
                                    <span></span> Draft
                                </span>
                            @endif

                            <span>{{ number_format($blog->views ?? 0) }} views</span>

                            @if($blog->reading_time)
                                <span>{{ $blog->reading_time }} min read</span>
                            @endif

                        </div>

                        <div class="blog-mobile-card__actions">

                            <a
                                href="{{ route('admin.blog.show', $blog) }}"
                                class="blog-action"
                            >
                                View
                            </a>

                            <a
                                href="{{ route('admin.blog.edit', $blog) }}"
                                class="blog-action"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.blog.duplicate', $blog) }}"
                                class="blog-inline-form"
                            >
                                @csrf

                                <button type="submit" class="blog-action">
                                    Duplicate
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.blog.destroy', $blog) }}"
                                class="blog-inline-form"
                                onsubmit="return confirm('Delete this blog post permanently?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="blog-action blog-action--danger"
                                >
                                    Delete
                                </button>
                            </form>

                        </div>

                    </article>

                @endforeach

            </div>

            @if($blogs->hasPages())
                <div class="admin-pagination blog-pagination">
                    {{ $blogs->links() }}
                </div>
            @endif

        @else

            <div class="blog-empty">

                <div class="blog-empty__icon">✦</div>

                <h2>No blog posts found</h2>

                @if(
                    request()->filled('search')
                    || request()->filled('category_id')
                    || request()->filled('status')
                    || request()->filled('featured')
                )

                    <p>
                        No posts match your current filters.
                    </p>

                    <div class="blog-empty__actions">

                        <a
                            href="{{ route('admin.blog.index') }}"
                            class="admin-button"
                        >
                            Clear Filters
                        </a>

                        <a
                            href="{{ route('admin.blog.create') }}"
                            class="admin-button admin-button--primary"
                        >
                            + Create Blog Post
                        </a>

                    </div>

                @else

                    <p>
                        Start publishing useful travel guides and stories.
                    </p>

                    <a
                        href="{{ route('admin.blog.create') }}"
                        class="admin-button admin-button--primary"
                    >
                        Create First Post
                    </a>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection
