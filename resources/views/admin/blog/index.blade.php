@extends('admin.layouts.app')

@section('title', 'Blog Posts')

@section('description', 'Manage your travel articles, guides and stories.')

@section('content')

<div class="admin-page blog-admin-page">

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
                <span class="admin-eyebrow">CONTENT</span>
                <h2>All Blog Posts</h2>
            </div>

            <a
                href="{{ route('admin.blog.create') }}"
                class="admin-button admin-button--primary"
            >
                <span>+</span>
                Create Blog Post
            </a>
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
                                <form
                                    method="POST"
                                    action="{{ route('admin.blog.status', $blog) }}"
                                    class="admin-inline-status-form"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        title="Change status"
                                        data-status-select
                                        class="admin-status-select admin-status-select--{{ $blog->status }}"
                                        onchange="this.form.submit()"
                                    >
                                        <option value="draft" @selected($blog->status === \App\Models\Blog::STATUS_DRAFT)>Draft</option>
                                        <option value="published" @selected($blog->status === \App\Models\Blog::STATUS_PUBLISHED)>Published</option>
                                        <option value="scheduled" @selected($blog->status === \App\Models\Blog::STATUS_SCHEDULED)>Scheduled</option>
                                        <option value="inactive" @selected($blog->status === \App\Models\Blog::STATUS_INACTIVE)>Inactive</option>
                                    </select>
                                </form>
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
                                        class="admin-icon-button"
                                        title="View"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="admin-sr-only">View</span>
                                    </a>

                                    <a
                                        href="{{ route('admin.blog.edit', $blog) }}"
                                        class="admin-icon-button"
                                        title="Edit"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        <span class="admin-sr-only">Edit</span>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.blog.duplicate', $blog) }}"
                                        class="blog-inline-form"
                                        onsubmit="return confirm('Create a copy of this blog post?')"
                                    >
                                        @csrf

                                        <button type="submit" class="admin-icon-button" title="Duplicate">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                            <span class="admin-sr-only">Duplicate</span>
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
                                            class="admin-icon-button {{ $blog->featured ? 'admin-icon-button--active' : '' }}"
                                            title="{{ $blog->featured ? 'Featured' : 'Feature' }}"
                                        >
                                            <svg viewBox="0 0 24 24" fill="{{ $blog->featured ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2Z"/></svg>
                                            <span class="admin-sr-only">{{ $blog->featured ? 'Featured' : 'Feature' }}</span>
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
                                            class="admin-icon-button admin-icon-button--danger"
                                            title="Delete"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                            <span class="admin-sr-only">Delete</span>
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

            <div class="admin-pagination blog-pagination">
                {{ $blogs->links() }}
            </div>

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


<style>

.admin-inline-status-form {
    display: inline-block;
}

.admin-status-select {
    height: 30px;
    border: 1px solid #dfe3e9;
    border-radius: 6px;
    background: #fff;
    color: #4b5666;
    font-size: 11px;
    font-weight: 650;
    padding: 0 8px;
    cursor: pointer;
}

.admin-status-select:focus {
    outline: none;
    border-color: #8993a3;
}

.admin-status-select--published {
    background: var(--admin-success-bg, #ecfdf3);
    border-color: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.admin-status-select--draft {
    background: #fffbeb;
    border-color: #fffbeb;
    color: #b45309;
}

.admin-status-select--scheduled {
    background: #e8f1ff;
    border-color: #e8f1ff;
    color: #2563eb;
}

.admin-status-select--inactive {
    background: var(--admin-danger-bg, #fff1f2);
    border-color: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-status-select]').forEach(function (select) {

        select.addEventListener('change', function () {

            select.className = 'admin-status-select admin-status-select--' + select.value;

        });

    });

});
</script>

@endsection
