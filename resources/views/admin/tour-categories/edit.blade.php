@extends('admin.layouts.app')

@section('title', 'Edit Tour Category')

@section('content')

<div class="admin-page">

    {{-- PAGE HEADER --}}
    <div class="admin-page__header">

        <div>
            <span class="admin-eyebrow">
                CATEGORY MANAGEMENT
            </span>

            <h1 class="admin-page__title">
                Edit Tour Category
            </h1>

            <p class="admin-page__description">
                Update category details, hierarchy, media, visibility and SEO settings.
            </p>
        </div>

        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tour-categories.index') }}"
                class="admin-button"
            >
                ← Back to Categories
            </a>

            <a
                href="{{ route('admin.tour-categories.show', $tourCategory) }}"
                class="admin-button admin-button--dark"
            >
                View Category
            </a>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if(session('error'))

        <div class="admin-alert admin-alert--danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="admin-alert admin-alert--danger">

            <strong>
                Please correct the following errors:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.tour-categories.update', $tourCategory) }}"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


        {{-- ==========================================================
             BASIC INFORMATION
        =========================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        BASIC INFORMATION
                    </span>

                    <h2>
                        Category Details
                    </h2>

                </div>

            </div>


            <div class="admin-form-grid">

                {{-- NAME --}}
                <div class="admin-form-group">

                    <label for="name">
                        Category Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $tourCategory->name) }}"
                        maxlength="100"
                        required
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
                        Slug
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $tourCategory->slug) }}"
                        maxlength="120"
                    >

                    <small>
                        Use lowercase letters, numbers and hyphens.
                    </small>

                    @error('slug')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- PARENT --}}
                <div class="admin-form-group">

                    <label for="parent_id">
                        Parent Category
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                    >

                        <option value="">
                            Category
                        </option>

                        @foreach($parentCategories as $parent)

                            <option
                                value="{{ $parent->id }}"
                                @selected(
                                    old(
                                        'parent_id',
                                        $tourCategory->parent_id
                                    ) == $parent->id
                                )
                            >
                                {{ $parent->name }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Keep "Category" if this category has no parent.
                    </small>

                    @error('parent_id')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- ICON --}}
                <div class="admin-form-group">

                    <label for="icon">
                        Icon
                    </label>

                    <input
                        type="text"
                        id="icon"
                        name="icon"
                        value="{{ old('icon', $tourCategory->icon) }}"
                        maxlength="100"
                        placeholder="e.g. fa-solid fa-umbrella-beach"
                    >

                    @error('icon')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- SHORT DESCRIPTION --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="short_description">
                        Short Description
                    </label>

                    <textarea
                        id="short_description"
                        name="short_description"
                        maxlength="500"
                        placeholder="Short category description..."
                    >{{ old('short_description', $tourCategory->short_description) }}</textarea>

                    @error('short_description')
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
                        placeholder="Complete category description..."
                    >{{ old('description', $tourCategory->description) }}</textarea>

                    @error('description')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

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
                <div class="admin-form-group">

                    <label for="image">
                        Category Image
                    </label>


                    @if($tourCategory->image)

                        <div
                            class="admin-image-preview"
                            style="margin-bottom:12px;"
                        >

                            <img
                                src="{{ asset('storage/' . $tourCategory->image) }}"
                                alt="{{ $tourCategory->name }}"
                            >

                        </div>

                    @endif


                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                    >


                    @if($tourCategory->image)

                        <label class="admin-checkbox">

                            <input
                                type="checkbox"
                                name="remove_image"
                                value="1"
                            >

                            <span>
                                Remove current image
                            </span>

                        </label>

                    @endif


                    <small>
                        Upload a new image to replace the current one.
                        Maximum 5MB.
                    </small>


                    @error('image')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror


                    <div
                        id="imagePreview"
                        class="admin-image-preview"
                        hidden
                    >

                        <img
                            id="imagePreviewImage"
                            src=""
                            alt="New category image preview"
                        >

                    </div>

                </div>


                {{-- OG IMAGE --}}
                <div class="admin-form-group">

                    <label for="og_image">
                        Open Graph Image
                    </label>


                    @if($tourCategory->og_image)

                        <div
                            class="admin-image-preview"
                            style="margin-bottom:12px;"
                        >

                            <img
                                src="{{ asset('storage/' . $tourCategory->og_image) }}"
                                alt="{{ $tourCategory->name }} social image"
                            >

                        </div>

                    @endif


                    <input
                        type="file"
                        id="og_image"
                        name="og_image"
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                    >


                    @if($tourCategory->og_image)

                        <label class="admin-checkbox">

                            <input
                                type="checkbox"
                                name="remove_og_image"
                                value="1"
                            >

                            <span>
                                Remove current OG image
                            </span>

                        </label>

                    @endif


                    <small>
                        Upload a new image to replace the current OG image.
                    </small>


                    @error('og_image')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror


                    <div
                        id="ogImagePreview"
                        class="admin-image-preview"
                        hidden
                    >

                        <img
                            id="ogImagePreviewImage"
                            src=""
                            alt="New OG image preview"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================================
             DISPLAY SETTINGS
        =========================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        DISPLAY SETTINGS
                    </span>

                    <h2>
                        Visibility & Ordering
                    </h2>

                </div>

            </div>


            <div class="admin-form-grid">


                {{-- SORT ORDER --}}
                <div class="admin-form-group">

                    <label for="sort_order">
                        Sort Order
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', $tourCategory->sort_order ?? 0) }}"
                        min="0"
                        max="999999"
                    >

                    <small>
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

                    <label for="status">
                        Status
                        <span>*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="1"
                            @selected(
                                old(
                                    'status',
                                    $tourCategory->status ? '1' : '0'
                                ) == '1'
                            )
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(
                                old(
                                    'status',
                                    $tourCategory->status ? '1' : '0'
                                ) == '0'
                            )
                        >
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- FEATURED --}}
                <div class="admin-form-group">

                    <label>
                        Featured
                    </label>

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="featured"
                            value="1"
                            @checked(
                                old(
                                    'featured',
                                    $tourCategory->featured
                                )
                            )
                        >

                        <span>
                            Mark this category as featured
                        </span>

                    </label>

                    @error('featured')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </div>


        {{-- ==========================================================
             SEO SETTINGS
        =========================================================== --}}

        <div class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        SEARCH ENGINE OPTIMIZATION
                    </span>

                    <h2>
                        SEO Settings
                    </h2>

                </div>

            </div>


            <div class="admin-form-grid">


                {{-- META TITLE --}}
                <div class="admin-form-group">

                    <label for="meta_title">
                        Meta Title
                    </label>

                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $tourCategory->meta_title) }}"
                        maxlength="255"
                        placeholder="Category SEO title"
                    >

                    @error('meta_title')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- ROBOTS --}}
                <div class="admin-form-group">

                    <label for="robots">
                        Robots
                    </label>

                    <select
                        id="robots"
                        name="robots"
                    >

                        @php
                            $robots = old(
                                'robots',
                                $tourCategory->robots ?: 'index,follow'
                            );
                        @endphp

                        <option
                            value="index,follow"
                            @selected($robots === 'index,follow')
                        >
                            index,follow
                        </option>

                        <option
                            value="index,nofollow"
                            @selected($robots === 'index,nofollow')
                        >
                            index,nofollow
                        </option>

                        <option
                            value="noindex,follow"
                            @selected($robots === 'noindex,follow')
                        >
                            noindex,follow
                        </option>

                        <option
                            value="noindex,nofollow"
                            @selected($robots === 'noindex,nofollow')
                        >
                            noindex,nofollow
                        </option>

                    </select>

                    @error('robots')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- META DESCRIPTION --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="meta_description">
                        Meta Description
                    </label>

                    <textarea
                        id="meta_description"
                        name="meta_description"
                        maxlength="1000"
                        placeholder="SEO description for this category..."
                    >{{ old('meta_description', $tourCategory->meta_description) }}</textarea>

                    @error('meta_description')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- META KEYWORDS --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="meta_keywords">
                        Meta Keywords
                    </label>

                    <textarea
                        id="meta_keywords"
                        name="meta_keywords"
                        maxlength="1000"
                        placeholder="travel, holidays, tours..."
                    >{{ old('meta_keywords', $tourCategory->meta_keywords) }}</textarea>

                    <small>
                        Optional. Separate keywords with commas.
                    </small>

                    @error('meta_keywords')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- CANONICAL --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="canonical_url">
                        Canonical URL
                    </label>

                    <input
                        type="url"
                        id="canonical_url"
                        name="canonical_url"
                        value="{{ old('canonical_url', $tourCategory->canonical_url) }}"
                        maxlength="500"
                        placeholder="https://example.com/tour-categories/holidays"
                    >

                    <small>
                        Leave blank to use the system-generated canonical URL.
                    </small>

                    @error('canonical_url')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </div>


        {{-- ==========================================================
             SAVE
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
                        Save your changes
                    </strong>

                    <small
                        style="
                            display:block;
                            margin-top:4px;
                            color:var(--admin-text-light);
                            font-size:9px;
                        "
                    >
                        Existing images remain unchanged unless replaced or removed.
                    </small>

                </div>


                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                    "
                >

                    <a
                        href="{{ route('admin.tour-categories.index') }}"
                        class="admin-button"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="admin-button admin-button--dark"
                    >
                        Save Changes
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- ================================================================
     IMAGE PREVIEW
================================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    function setupPreview(inputId, wrapperId, imageId) {

        const input = document.getElementById(inputId);
        const wrapper = document.getElementById(wrapperId);
        const image = document.getElementById(imageId);

        if (!input || !wrapper || !image) {
            return;
        }

        input.addEventListener('change', function () {

            const file = this.files?.[0];

            if (!file) {
                wrapper.hidden = true;
                image.removeAttribute('src');
                return;
            }

            if (!file.type.startsWith('image/')) {
                wrapper.hidden = true;
                image.removeAttribute('src');
                return;
            }

            const objectUrl = URL.createObjectURL(file);

            image.src = objectUrl;
            wrapper.hidden = false;

            image.onload = function () {
                URL.revokeObjectURL(objectUrl);
            };

        });

    }


    setupPreview(
        'image',
        'imagePreview',
        'imagePreviewImage'
    );


    setupPreview(
        'og_image',
        'ogImagePreview',
        'ogImagePreviewImage'
    );

});
</script>

@endsection