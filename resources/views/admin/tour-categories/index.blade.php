@extends('admin.layouts.app')

@section('title', 'Tour Categories')

@section('content')

<div class="admin-page">

    {{-- Page Header --}}
    <div class="admin-page__header">

        <div>
            <span class="admin-eyebrow">
                CATEGORY MANAGEMENT
            </span>

            <h1 class="admin-page__title">
                Tour Categories
            </h1>

            <p class="admin-page__description">
                Manage tour categories, sub-categories, visibility,
                featured status and package assignments.
            </p>
        </div>

        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tour-categories.create') }}"
                class="admin-button admin-button--dark"
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
                                            View
                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.tour-categories.edit', $category) }}"
                                            class="admin-icon-button"
                                            title="Edit"
                                        >
                                            Edit
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
                                                Copy
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
                                                {{ $category->status ? 'Off' : 'On' }}
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
                                                    Delete
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
            @if($categories->hasPages())

                <div class="admin-pagination">

                    {{ $categories->links() }}

                </div>

            @endif

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
                    class="admin-button admin-button--dark"
                >
                    + Create Category
                </a>

            </div>

        @endif

    </div>

</div>

@endsection