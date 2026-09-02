@extends('admin.layouts.app')

@section('title', 'Create Blog Category')

@section('content')

<div class="admin-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                BLOG / CATEGORIES / CREATE
            </span>

            <h1 class="admin-page__title">
                Create Blog Category
            </h1>

            <p class="admin-page__description">
                Create a category that can be assigned to travel blog posts.
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
         FORM
    ====================================================== --}}

    <form
        method="POST"
        action="{{ route('admin.blog-categories.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


        <div class="admin-card blog-category-form-card">

            {{-- =================================================
                 BASIC INFORMATION
            ================================================== --}}

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        STEP 01
                    </span>

                    <h2 class="admin-card__title">
                        Basic Information
                    </h2>

                    <p class="admin-card__description">
                        Define the category name, URL and description.
                    </p>

                </div>

            </div>


            <div class="admin-form-grid">


                {{-- CATEGORY NAME --}}

                <div class="admin-form-group">

                    <label for="name">
                        Category Name <span>*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
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
                        value="{{ old('slug') }}"
                        placeholder="e.g. travel-guides"
                        maxlength="160"
                    >

                    <small class="admin-form-help">
                        Leave empty to generate automatically from the category name.
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
                        placeholder="Briefly describe what type of blogs belong to this category..."
                    >{{ old('description') }}</textarea>

                    <small class="admin-form-help">
                        Keep the description clear and useful for administrators and visitors.
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
                        Add an optional image to visually represent this category.
                    </p>

                </div>

            </div>


            <div class="blog-category-upload">

                <div class="admin-form-group">

                    <label for="image">
                        Category Image
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
                        value="{{ old('sort_order', 0) }}"
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
                            @selected(old('is_active', '1') == '1')
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active') === '0')
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
                Create Category
            </button>

        </div>

    </form>

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