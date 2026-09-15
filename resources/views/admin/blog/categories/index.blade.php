@extends('admin.layouts.app')

@section('title', 'Blog Categories')

@section('content')

<div class="admin-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                BLOG / CATEGORIES
            </span>

            <h1 class="admin-page__title">
                Blog Categories
            </h1>

            <p class="admin-page__description">
                Create and manage categories used to organize your travel blogs.
            </p>

        </div>

        <a
            href="{{ route('admin.blog-categories.create') }}"
            class="admin-button admin-button--primary"
        >
            + Add Category
        </a>

    </div>


    {{-- =====================================================
         ALERTS
    ====================================================== --}}

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


    {{-- =====================================================
         FILTER CARD
    ====================================================== --}}

    <div class="admin-card blog-category-filter-card">

        <form
            method="GET"
            action="{{ route('admin.blog-categories.index') }}"
            class="admin-filter-form"
        >

            <div class="admin-filter-field">

                <label for="category-search">
                    Search
                </label>

                <input
                    id="category-search"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search category name or slug..."
                >

            </div>


            <div class="admin-filter-field">

                <label for="category-status">
                    Status
                </label>

                <select
                    id="category-status"
                    name="status"
                >

                    <option value="">
                        All statuses
                    </option>

                    <option
                        value="active"
                        @selected(request('status') === 'active')
                    >
                        Active
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <div class="admin-filter-actions">

                <button
                    type="submit"
                    class="admin-button admin-button--primary"
                >
                    Filter
                </button>

                <a
                    href="{{ route('admin.blog-categories.index') }}"
                    class="admin-button"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- =====================================================
         CATEGORY TABLE
    ====================================================== --}}

    <div class="admin-card blog-category-table-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    CATEGORY LIBRARY
                </span>

                <h2 class="admin-card__title">
                    All Categories
                </h2>

            </div>

            <span class="blog-category-count">
                {{ $categories->total() }}
                {{ Str::plural('category', $categories->total()) }}
            </span>

        </div>


        @if($categories->isNotEmpty())

            <div class="admin-table-wrapper">

                <table class="admin-table blog-category-table">

                    <thead>

                        <tr>

                            <th>
                                Category
                            </th>

                            <th>
                                Slug
                            </th>

                            <th>
                                Blogs
                            </th>

                            <th>
                                Order
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Updated
                            </th>

                            <th class="admin-table__actions">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($categories as $category)

                            <tr>

                                {{-- CATEGORY --}}

                                <td>

                                    <div class="blog-category-name-cell">

                                        <div class="blog-category-thumb">

                                            @if($category->image)

                                                <img
                                                    src="{{ $category->image_url }}"
                                                    alt="{{ $category->name }}"
                                                    loading="lazy"
                                                >

                                            @else

                                                <span>
                                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                                </span>

                                            @endif

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $category->name }}
                                            </strong>

                                            @if($category->description)

                                                <small>
                                                    {{ Str::limit($category->description, 70) }}
                                                </small>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- SLUG --}}

                                <td>

                                    <code class="blog-category-slug">
                                        {{ $category->slug }}
                                    </code>

                                </td>


                                {{-- BLOG COUNT --}}

                                <td>

                                    <span class="blog-category-blog-count">
                                        {{ $category->blogs_count }}
                                    </span>

                                </td>


                                {{-- ORDER --}}

                                <td>

                                    <span class="blog-category-order">
                                        {{ $category->sort_order }}
                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td>

                                    @if($category->is_active)

                                        <span class="admin-status admin-status--success">
                                            Active
                                        </span>

                                    @else

                                        <span class="admin-status admin-status--muted">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- UPDATED --}}

                                <td>

                                    <span class="blog-category-date">
                                        {{ $category->updated_at?->format('d M Y') }}
                                    </span>

                                </td>


                                {{-- ACTIONS --}}

                                <td>

                                    <div class="blog-category-actions">

                                        <a
                                            href="{{ route('admin.blog-categories.edit', $category) }}"
                                            class="admin-table-action"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route('admin.blog-categories.status', $category) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="is_active"
                                                value="{{ $category->is_active ? 0 : 1 }}"
                                            >

                                            <button
                                                type="submit"
                                                class="admin-table-action admin-table-action--status"
                                            >
                                                {{ $category->is_active ? 'Disable' : 'Enable' }}
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route('admin.blog-categories.destroy', $category) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="admin-table-action admin-table-action--danger"
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


            {{-- PAGINATION --}}

            @if($categories->hasPages())

                <div class="admin-pagination">

                    {{ $categories->links() }}

                </div>

            @endif

        @else

            {{-- EMPTY STATE --}}

            <div class="blog-category-empty">

                <div class="blog-category-empty__icon">
                    ▤
                </div>

                <h3>
                    No blog categories found
                </h3>

                <p>
                    Create your first blog category to start organizing travel articles.
                </p>

                <a
                    href="{{ route('admin.blog-categories.create') }}"
                    class="admin-button admin-button--primary"
                >
                    + Create First Category
                </a>

            </div>

        @endif

    </div>

</div>

@endsection