@extends('admin.layouts.app')

@section('title', 'Edit Blog Category')

@section('content')

<div class="admin-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                BLOG / CATEGORIES / EDIT
            </span>

            <h1 class="admin-page__title">
                Edit Blog Category
            </h1>

            <p class="admin-page__description">
                Update the category information, visibility and display order.
            </p>

        </div>


        <a
            href="{{ route('admin.blog-categories.index') }}"
            class="admin-button"
        >
            ← Back to Categories
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


    {{-- =====================================================
         UPDATE FORM
    ====================================================== --}}

    <form
        method="POST"
        action="{{ route('admin.blog-categories.update', $category) }}"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


        {{-- =================================================
             BASIC INFORMATION
        ================================================== --}}

        <div class="admin-card blog-category-form-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        STEP 01
                    </span>

                    <h2 class="admin-card__title">
                        Basic Information
                    </h2>

                    <p class="admin-card__description">
                        Update the category name, URL and description.
                    </p>

                </div>

            </div>


            <div class="admin-form-grid">


                {{-- NAME --}}

                <div class="admin-form-group">

                    <label for="name">
                        Category Name <span>*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $category->name) }}"
                        placeholder="e.g. Travel Guides"
                        maxlength="120"
                        required
                        autofocus
                    >

                    @error('name')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- SLUG --}}

                <div class="admin-form-group">

                    <label for="slug">
                        URL Slug
                    </label>

                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        value="{{ old('slug', $category->slug) }}"
                        placeholder="e.g. travel-guides"
                        maxlength="160"
                    >

                    <small class="admin-form-help">
                        This slug is used in the category URL.
                    </small>

                    @error('slug')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- DESCRIPTION --}}

                <div class="admin-form-group admin-form-group--full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        maxlength="2000"
                        placeholder="Briefly describe this category..."
                    >{{ old('description', $category->description) }}</textarea>

                    <small class="admin-form-help">
                        {{ strlen((string) $category->description) }}/2000 characters
                    </small>

                    @error('description')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

            </div>

        </div>


        {{-- =====================================================
             IMAGE
        ====================================================== --}}

        <div class="admin-card blog-category-form-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        STEP 02
                    </span>

                    <h2 class="admin-card__title">
                        Category Image
                    </h2>

                    <p class="admin-card__description">
                        Replace the current category image if required.
                    </p>

                </div>

            </div>


            <div class="blog-category-image-editor">


                @if($category->image)

                    <div class="blog-category-current-image">

                        <img
                            src="{{ $category->image_url }}"
                            alt="{{ $category->name }}"
                        >

                        <div>

                            <strong>
                                Current Image
                            </strong>

                            <small>
                                Upload a new image below to replace it.
                            </small>

                        </div>

                    </div>

                @else

                    <div class="blog-category-no-image">

                        <span>
                            {{ strtoupper(substr($category->name, 0, 1)) }}
                        </span>

                        <div>

                            <strong>
                                No category image
                            </strong>

                            <small>
                                You can add one below.
                            </small>

                        </div>

                    </div>

                @endif


                <div class="admin-form-group">

                    <label for="image">
                        New Category Image
                    </label>

                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                    >

                    <small class="admin-form-help">
                        JPG, PNG, WebP or AVIF. Maximum 5 MB.
                    </small>

                    @error('image')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

            </div>

        </div>


        {{-- =====================================================
             DISPLAY SETTINGS
        ====================================================== --}}

        <div class="admin-card blog-category-form-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        STEP 03
                    </span>

                    <h2 class="admin-card__title">
                        Display Settings
                    </h2>

                    <p class="admin-card__description">
                        Control category ordering and visibility.
                    </p>

                </div>

            </div>


            <div class="admin-form-grid">


                {{-- SORT ORDER --}}

                <div class="admin-form-group">

                    <label for="sort_order">
                        Sort Order
                    </label>

                    <input
                        id="sort_order"
                        type="number"
                        name="sort_order"
                        value="{{ old('sort_order', $category->sort_order) }}"
                        min="0"
                        max="9999"
                    >

                    <small class="admin-form-help">
                        Lower numbers appear first.
                    </small>

                    @error('sort_order')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- STATUS --}}

                <div class="admin-form-group">

                    <label for="is_active">
                        Status
                    </label>

                    <select
                        id="is_active"
                        name="is_active"
                    >

                        <option
                            value="1"
                            @selected(old('is_active', $category->is_active ? '1' : '0') == '1')
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active', $category->is_active ? '1' : '0') == '0')
                        >
                            Inactive
                        </option>

                    </select>

                    @error('is_active')

                        <small class="admin-form-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

            </div>

        </div>


        {{-- =====================================================
             CATEGORY INFORMATION
        ====================================================== --}}

        <div class="admin-card blog-category-meta-card">

            <div class="blog-category-meta-grid">

                <div>

                    <span>
                        Category ID
                    </span>

                    <strong>
                        #{{ $category->id }}
                    </strong>

                </div>


                <div>

                    <span>
                        Created
                    </span>

                    <strong>
                        {{ $category->created_at?->format('d M Y, h:i A') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Last Updated
                    </span>

                    <strong>
                        {{ $category->updated_at?->format('d M Y, h:i A') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Blogs Using Category
                    </span>

                    <strong>
                        {{ $category->blogs()->count() }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}

        <div class="admin-form-actions">

            <a
                href="{{ route('admin.blog-categories.index') }}"
                class="admin-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="admin-button admin-button--primary"
            >
                Save Changes
            </button>

        </div>

    </form>


    {{-- =====================================================
         DELETE
    ====================================================== --}}

    <div class="admin-card blog-category-danger-card">

        <div>

            <span class="admin-eyebrow">
                DANGER ZONE
            </span>

            <h2 class="admin-card__title">
                Delete Category
            </h2>

            <p class="admin-card__description">
                A category cannot be deleted while blogs are assigned to it.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('admin.blog-categories.destroy', $category) }}"
            onsubmit="return confirm('Are you sure you want to permanently delete this category?');"
        >

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="admin-button admin-button--danger"
                @disabled($category->blogs()->exists())
            >
                Delete Category
            </button>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const nameInput =
        document.getElementById('name');

    const slugInput =
        document.getElementById('slug');


    if (!nameInput || !slugInput) {
        return;
    }


    let slugManuallyEdited = false;


    slugInput.addEventListener('input', function () {
        slugManuallyEdited = true;
    });


    nameInput.addEventListener('input', function () {

        if (slugManuallyEdited) {
            return;
        }


        const slug =
            nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');


        slugInput.value = slug;

    });

});

</script>

@endpush