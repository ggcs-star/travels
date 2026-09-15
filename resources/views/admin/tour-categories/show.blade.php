@extends('admin.layouts.app')

@section('title', $tourCategory->name)

@section('content')

<div class="admin-page">

    {{-- ==========================================================
         HEADER
    =========================================================== --}}

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                CATEGORY MANAGEMENT
            </span>

            <h1 class="admin-page__title">
                {{ $tourCategory->name }}
            </h1>

            <p class="admin-page__description">
                Category details, hierarchy, media, visibility and SEO information.
            </p>

        </div>


        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tour-categories.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

            <a
                href="{{ route('admin.tour-categories.edit', $tourCategory) }}"
                class="admin-button admin-button--dark"
            >
                Edit Category
            </a>

        </div>

    </div>


    {{-- ==========================================================
         ALERTS
    =========================================================== --}}

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


    {{-- ==========================================================
         OVERVIEW STATS
    =========================================================== --}}

    <div class="admin-stats">

        <div class="admin-stat">

            <span class="admin-stat__label">
                Status
            </span>

            <strong>

                @if($tourCategory->status)
                    Active
                @else
                    Inactive
                @endif

            </strong>

        </div>


        <div class="admin-stat">

            <span class="admin-stat__label">
                Tour Packages
            </span>

            <strong>
                {{ number_format($tourCategory->packages_count ?? 0) }}
            </strong>

        </div>


        <div class="admin-stat">

            <span class="admin-stat__label">
                Sub Categories
            </span>

            <strong>
                {{ number_format($tourCategory->children->count()) }}
            </strong>

        </div>


        <div class="admin-stat">

            <span class="admin-stat__label">
                Sort Order
            </span>

            <strong>
                {{ $tourCategory->sort_order }}
            </strong>

        </div>

    </div>


    {{-- ==========================================================
         CATEGORY INFORMATION
    =========================================================== --}}

    <div class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    CATEGORY INFORMATION
                </span>

                <h2>
                    General Details
                </h2>

            </div>


            <span class="admin-status {{ $tourCategory->status ? 'admin-status--success' : 'admin-status--danger' }}">

                <span></span>

                {{ $tourCategory->status ? 'Active' : 'Inactive' }}

            </span>

        </div>


        <div class="admin-detail-list">

            <div>

                <span>
                    Name
                </span>

                <strong>
                    {{ $tourCategory->name }}
                </strong>

            </div>


            <div>

                <span>
                    Slug
                </span>

                <strong>
                    {{ $tourCategory->slug }}
                </strong>

            </div>


            <div>

                <span>
                    Parent Category
                </span>

                <strong>

                    @if($tourCategory->parent)
                        {{ $tourCategory->parent->name }}
                    @else
                        Root Category
                    @endif

                </strong>

            </div>


            <div>

                <span>
                    Icon
                </span>

                <strong>
                    {{ $tourCategory->icon ?: '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Sort Order
                </span>

                <strong>
                    {{ $tourCategory->sort_order }}
                </strong>

            </div>


            <div>

                <span>
                    Featured
                </span>

                <strong>

                    @if($tourCategory->featured)
                        Yes
                    @else
                        No
                    @endif

                </strong>

            </div>


            <div>

                <span>
                    Created
                </span>

                <strong>
                    {{ $tourCategory->created_at?->format('d M Y, h:i A') ?? '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Last Updated
                </span>

                <strong>
                    {{ $tourCategory->updated_at?->format('d M Y, h:i A') ?? '—' }}
                </strong>

            </div>

        </div>

    </div>


    {{-- ==========================================================
         DESCRIPTION
    =========================================================== --}}

    <div class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    CONTENT
                </span>

                <h2>
                    Description
                </h2>

            </div>

        </div>


        @if($tourCategory->short_description)

            <div style="margin-bottom:20px;">

                <strong
                    style="
                        display:block;
                        margin-bottom:8px;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Short Description
                </strong>

                <p
                    style="
                        margin:0;
                        color:var(--admin-text-light);
                        line-height:1.7;
                    "
                >
                    {{ $tourCategory->short_description }}
                </p>

            </div>

        @endif


        @if($tourCategory->description)

            <div>

                <strong
                    style="
                        display:block;
                        margin-bottom:8px;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Full Description
                </strong>

                <div
                    style="
                        color:var(--admin-text-light);
                        line-height:1.8;
                        white-space:pre-line;
                    "
                >
                    {{ $tourCategory->description }}
                </div>

            </div>

        @else

            <div class="admin-empty">

                <strong>
                    No description added
                </strong>

                <p>
                    Add a description from the edit page.
                </p>

            </div>

        @endif

    </div>


    {{-- ==========================================================
         MEDIA
    =========================================================== --}}

    <div class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    MEDIA
                </span>

                <h2>
                    Category Images
                </h2>

            </div>

        </div>


        <div class="admin-form-grid">


            {{-- CATEGORY IMAGE --}}
            <div>

                <strong
                    style="
                        display:block;
                        margin-bottom:10px;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Category Image
                </strong>


                @if($tourCategory->image)

                    <div class="admin-image-preview">

                        <img
                            src="{{ asset('storage/' . $tourCategory->image) }}"
                            alt="{{ $tourCategory->name }}"
                            loading="lazy"
                        >

                    </div>

                @else

                    <div class="admin-empty">

                        <strong>
                            No category image
                        </strong>

                    </div>

                @endif

            </div>


            {{-- OG IMAGE --}}
            <div>

                <strong
                    style="
                        display:block;
                        margin-bottom:10px;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Open Graph Image
                </strong>


                @if($tourCategory->og_image)

                    <div class="admin-image-preview">

                        <img
                            src="{{ asset('storage/' . $tourCategory->og_image) }}"
                            alt="{{ $tourCategory->name }} OG image"
                            loading="lazy"
                        >

                    </div>

                @else

                    <div class="admin-empty">

                        <strong>
                            No OG image
                        </strong>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- ==========================================================
         CHILD CATEGORIES
    =========================================================== --}}

    @if($tourCategory->children->isNotEmpty())

        <div class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        CATEGORY HIERARCHY
                    </span>

                    <h2>
                        Sub Categories
                    </h2>

                </div>

            </div>


            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Category
                            </th>

                            <th>
                                Packages
                            </th>

                            <th>
                                Sort
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($tourCategory->children as $child)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $child->name }}
                                    </strong>

                                    <small
                                        style="
                                            display:block;
                                            margin-top:3px;
                                            color:var(--admin-text-light);
                                        "
                                    >
                                        /{{ $child->slug }}
                                    </small>

                                </td>


                                <td>
                                    {{ $child->packages_count ?? 0 }}
                                </td>


                                <td>
                                    {{ $child->sort_order }}
                                </td>


                                <td>

                                    @if($child->status)

                                        <span class="admin-badge admin-badge--success">
                                            Active
                                        </span>

                                    @else

                                        <span class="admin-badge admin-badge--danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="admin-actions">

                                        <a
                                            href="{{ route('admin.tour-categories.show', $child) }}"
                                            class="admin-icon-button"
                                            title="View"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route('admin.tour-categories.edit', $child) }}"
                                            class="admin-icon-button"
                                            title="Edit"
                                        >
                                            Edit
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @endif


    {{-- ==========================================================
         SEO
    =========================================================== --}}

    <div class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    SEO
                </span>

                <h2>
                    Search Engine Settings
                </h2>

            </div>

        </div>


        <div class="admin-detail-list">

            <div>

                <span>
                    Meta Title
                </span>

                <strong>
                    {{ $tourCategory->meta_title ?: '—' }}
                </strong>

            </div>


            <div>

                <span>
                    Robots
                </span>

                <strong>
                    {{ $tourCategory->robots ?: 'index,follow' }}
                </strong>

            </div>


            <div>

                <span>
                    Canonical URL
                </span>

                <strong style="word-break:break-word;">
                    {{ $tourCategory->canonical_url ?: 'Automatic' }}
                </strong>

            </div>


            <div>

                <span>
                    Meta Keywords
                </span>

                <strong>
                    {{ $tourCategory->meta_keywords ?: '—' }}
                </strong>

            </div>

        </div>


        @if($tourCategory->meta_description)

            <div style="margin-top:24px;">

                <strong
                    style="
                        display:block;
                        margin-bottom:8px;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Meta Description
                </strong>

                <p
                    style="
                        margin:0;
                        color:var(--admin-text-light);
                        line-height:1.7;
                    "
                >
                    {{ $tourCategory->meta_description }}
                </p>

            </div>

        @endif

    </div>


    {{-- ==========================================================
         ACTIONS
    =========================================================== --}}

    <div class="admin-card">

        <div
            style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:16px;
                flex-wrap:wrap;
            "
        >

            <div>

                <strong
                    style="
                        display:block;
                        color:var(--admin-text-secondary);
                        font-size:11px;
                    "
                >
                    Category Actions
                </strong>

                <small
                    style="
                        display:block;
                        margin-top:5px;
                        color:var(--admin-text-light);
                    "
                >
                    Manage this category from the available actions.
                </small>

            </div>


            <div class="admin-actions">

                <a
                    href="{{ route('admin.tour-categories.edit', $tourCategory) }}"
                    class="admin-button admin-button--dark"
                >
                    Edit Category
                </a>


                @if(!$tourCategory->packages()->exists() && !$tourCategory->children()->exists())

                    <form
                        method="POST"
                        action="{{ route('admin.tour-categories.destroy', $tourCategory) }}"
                        onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="admin-button"
                            style="
                                border-color:var(--admin-danger);
                                color:var(--admin-danger);
                                background:#fff;
                            "
                        >
                            Delete
                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection