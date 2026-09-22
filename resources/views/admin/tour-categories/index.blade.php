@extends('admin.layouts.app')

@section('title', 'Tour Categories')

@section('description', 'Manage tour categories, sub-categories, visibility, featured status and package assignments.')

@section('content')

<div class="admin-page">

    {{-- Page Header --}}
    <div class="admin-page__header">

        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tour-categories.create') }}"
                class="admin-button admin-button--primary"
            >
                <span>+</span>
                Create Category
            </a>

        </div>

    </div>


    {{-- Flash Messages --}}
    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="admin-alert admin-alert--danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- Statistics --}}
    @php
        $totalCategories = $categories->total();

        $activeCategories = $categories->getCollection()
            ->where('status', true)
            ->count();

        $featuredCategories = $categories->getCollection()
            ->where('featured', true)
            ->count();

        $assignedPackages = $categories->getCollection()
            ->sum('packages_count');
    @endphp


    <div class="admin-stats">

        {{-- Total --}}
        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    #
                </div>

                <span class="admin-badge">
                    TOTAL
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($totalCategories) }}
            </strong>

            <span class="admin-stat__label">
                Total Categories
            </span>

            <small class="admin-stat__caption">
                All available categories
            </small>

        </div>


        {{-- Active --}}
        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    ✓
                </div>

                <span class="admin-badge admin-badge--success">
                    ACTIVE
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($activeCategories) }}
            </strong>

            <span class="admin-stat__label">
                Active Categories
            </span>

            <small class="admin-stat__caption">
                Currently visible
            </small>

        </div>


        {{-- Featured --}}
        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    ★
                </div>

                <span class="admin-badge admin-badge--warning">
                    FEATURED
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($featuredCategories) }}
            </strong>

            <span class="admin-stat__label">
                Featured Categories
            </span>

            <small class="admin-stat__caption">
                Highlighted categories
            </small>

        </div>


        {{-- Packages --}}
        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    ◈
                </div>

                <span class="admin-badge">
                    MAPPED
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($assignedPackages) }}
            </strong>

            <span class="admin-stat__label">
                Assigned Packages
            </span>

            <small class="admin-stat__caption">
                Packages linked to these categories
            </small>

        </div>

    </div>


    {{-- Category Table --}}
    <div class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    CATEGORY LIST
                </span>

                <h2>
                    All Tour Categories
                </h2>

            </div>

        </div>


        @if($categories->count())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Category
                            </th>

                            <th>
                                Parent
                            </th>

                            <th>
                                Packages
                            </th>

                            <th>
                                Children
                            </th>

                            <th>
                                Order
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Featured
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($categories as $category)

                            <tr>

                                {{-- Category --}}
                                <td>

                                    <div
                                        style="
                                            display:flex;
                                            align-items:center;
                                            gap:10px;
                                        "
                                    >

                                        @if($category->image)

                                            <img
                                                src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}"
                                                style="
                                                    width:42px;
                                                    height:42px;
                                                    border-radius:9px;
                                                    object-fit:cover;
                                                    border:1px solid var(--admin-border);
                                                    flex:0 0 auto;
                                                "
                                            >

                                        @else

                                            <div
                                                style="
                                                    width:42px;
                                                    height:42px;
                                                    display:grid;
                                                    place-items:center;
                                                    border-radius:9px;
                                                    background:var(--admin-primary-soft);
                                                    color:var(--admin-primary);
                                                    font-weight:800;
                                                    flex:0 0 auto;
                                                "
                                            >
                                                {{ strtoupper(substr($category->name, 0, 1)) }}
                                            </div>

                                        @endif


                                        <div class="admin-table__primary">

                                            <strong>
                                                {{ $category->name }}
                                            </strong>

                                            <small>
                                                /{{ $category->slug }}
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                {{-- Parent --}}
                                <td>

                                    @if($category->parent)

                                        <span>
                                            {{ $category->parent->name }}
                                        </span>

                                    @else

                                        <span style="color:var(--admin-text-light);">
                                            Root Category
                                        </span>

                                    @endif

                                </td>


                                {{-- Packages --}}
                                <td>

                                    <strong>
                                        {{ number_format($category->packages_count) }}
                                    </strong>

                                </td>


                                {{-- Children --}}
                                <td>

                                    <strong>
                                        {{ number_format($category->children_count) }}
                                    </strong>

                                </td>


                                {{-- Sort --}}
                                <td>

                                    <span>
                                        {{ $category->sort_order }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($category->status)

                                        <span class="admin-badge admin-badge--success">
                                            Active
                                        </span>

                                    @else

                                        <span class="admin-badge admin-badge--danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Featured --}}
                                <td>

                                    @if($category->featured)

                                        <span class="admin-badge admin-badge--warning">
                                            Featured
                                        </span>

                                    @else

                                        <span
                                            style="
                                                color:var(--admin-text-light);
                                                font-size:10px;
                                            "
                                        >
                                            No
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td>

                                    <div class="admin-table__actions">

                                        {{-- View --}}
                                        <a
                                            href="{{ route('admin.tour-categories.show', $category) }}"
                                            class="admin-icon-button"
                                            title="View"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <span class="admin-sr-only">View</span>
                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.tour-categories.edit', $category) }}"
                                            class="admin-icon-button"
                                            title="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            <span class="admin-sr-only">Edit</span>
                                        </a>


                                        {{-- Duplicate --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.tour-categories.duplicate', $category) }}"
                                            style="display:inline;"
                                            onsubmit="return confirm('Duplicate this category?')"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="admin-icon-button"
                                                title="Duplicate"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="admin-sr-only">Duplicate</span>
                                            </button>

                                        </form>


                                        {{-- Status --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.tour-categories.status', $category) }}"
                                            style="display:inline;"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="{{ $category->status ? 0 : 1 }}"
                                            >

                                            <button
                                                type="submit"
                                                class="admin-icon-button"
                                                title="{{ $category->status ? 'Deactivate' : 'Activate' }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.8 0"/></svg>
                                                <span class="admin-sr-only">{{ $category->status ? 'Deactivate' : 'Activate' }}</span>
                                            </button>

                                        </form>


                                        {{-- Delete --}}
                                        @if($category->packages_count === 0 && $category->children_count === 0)

                                            <form
                                                method="POST"
                                                action="{{ route('admin.tour-categories.destroy', $category) }}"
                                                style="display:inline;"
                                                onsubmit="return confirm('Delete this category permanently?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="admin-icon-button"
                                                    title="Delete"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                                    <span class="admin-sr-only">Delete</span>
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="admin-pagination">

                {{ $categories->links() }}

            </div>

        @else

            {{-- Empty State --}}
            <div class="admin-empty">

                <div class="admin-empty__icon">
                    #
                </div>

                <h3>
                    No tour categories yet
                </h3>

                <p>
                    Create your first category to start organizing tour packages.
                </p>

                <a
                    href="{{ route('admin.tour-categories.create') }}"
                    class="admin-button admin-button--primary"
                >
                    + Create Category
                </a>

            </div>

        @endif

    </div>

</div>

@endsection