@extends('admin.layouts.app')

@section('title', 'Tour Categories')

@section('description', 'Manage tour categories, sub-categories, visibility, featured status and package assignments.')

@section('content')

<div class="admin-page">

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

                <div class="admin-stat__icon admin-stat__icon--green">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="2" fill="currentColor"/><rect x="13" y="3" width="8" height="8" rx="2" fill="currentColor"/><rect x="3" y="13" width="8" height="8" rx="2" fill="currentColor"/><rect x="13" y="13" width="8" height="8" rx="2" fill="currentColor"/></svg>
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

                <div class="admin-stat__icon admin-stat__icon--teal">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2.5 1.5V22l4-1 4 1v-1.5L13 19v-5.5l8 2.5Z" fill="currentColor"/></svg>
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

            <a
                href="{{ route('admin.tour-categories.create') }}"
                class="admin-button admin-button--primary"
            >
                <span>+</span>
                Create Category
            </a>

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
                                            Category
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

                                    <form
                                        method="POST"
                                        action="{{ route('admin.tour-categories.status', $category) }}"
                                        class="admin-inline-status-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            title="Change status"
                                            data-status-select
                                            class="admin-status-select admin-status-select--{{ $category->status ? 'active' : 'inactive' }}"
                                            onchange="this.form.submit()"
                                        >
                                            <option value="1" @selected($category->status)>Active</option>
                                            <option value="0" @selected(!$category->status)>Inactive</option>
                                        </select>
                                    </form>

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

.admin-status-select--active {
    background: var(--admin-success-bg, #ecfdf3);
    border-color: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
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

            select.className = 'admin-status-select admin-status-select--'
                + (select.value === '1' ? 'active' : 'inactive');

        });

    });

});
</script>

@endsection