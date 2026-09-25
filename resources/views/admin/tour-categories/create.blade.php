@extends('admin.layouts.app')

@section('title', 'Create Tour Category')

@section('content')

<div class="admin-page">

    {{-- Page Header --}}
    <div class="admin-page__header">

        <div>
            <span class="admin-eyebrow">
                CATEGORY MANAGEMENT
            </span>

            <h1 class="admin-page__title">
                Create Tour Category
            </h1>

            <p class="admin-page__description">
                Create a new tour category and configure its
                hierarchy, media, visibility and SEO settings.
            </p>
        </div>

        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tour-categories.index') }}"
                class="admin-button admin-button--dark"
            >
                ← Back to Categories
            </a>

        </div>

    </div>


    {{-- Validation Errors --}}
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
        action="{{ route('admin.tour-categories.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


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

                {{-- Name --}}
                <div class="admin-form-group">

                    <label for="name">
                        Category Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Holidays"
                        maxlength="100"
                        required
                    >

                    <small>
                        The main name displayed throughout the website.
                    </small>

                    @error('name')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Slug --}}
                <div class="admin-form-group">

                    <label for="slug">
                        Slug
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="e.g. holidays"
                        maxlength="120"
                    >

                    <small>
                        Leave blank to generate automatically from the category name.
                    </small>

                    @error('slug')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Parent --}}
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
                                @selected(old('parent_id') == $parent->id)
                            >
                                {{ $parent->name }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Select a parent only if this should be a sub-category.
                    </small>

                    @error('parent_id')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Icon --}}
                <div class="admin-form-group">

                    <label for="icon">
                        Icon
                    </label>

                    <input
                        type="text"
                        id="icon"
                        name="icon"
                        value="{{ old('icon') }}"
                        placeholder="e.g. fa-solid fa-umbrella-beach"
                        maxlength="100"
                    >

                    <small>
                        Enter your existing icon class/name if your frontend uses one.
                    </small>

                    @error('icon')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Short Description --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="short_description">
                        Short Description
                    </label>

                    <textarea
                        id="short_description"
                        name="short_description"
                        maxlength="500"
                        placeholder="Short description used for category cards and listings..."
                    >{{ old('short_description') }}</textarea>

                    <small>
                        Recommended for category cards, search results and listings.
                    </small>

                    @error('short_description')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Description --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Write the complete category description..."
                    >{{ old('description') }}</textarea>

                    <small>
                        Detailed category content displayed on the category page.
                    </small>

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

                {{-- Category Image --}}
                <div class="admin-form-group">

                    <label for="image">
                        Category Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                    >

                    <small>
                        JPG, JPEG, PNG, WEBP or AVIF. Maximum 5MB.
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
                            alt="Category image preview"
                        >

                    </div>

                </div>


                {{-- OG Image --}}
                <div class="admin-form-group">

                    <label for="og_image">
                        Open Graph Image
                    </label>

                    <input
                        type="file"
                        id="og_image"
                        name="og_image"
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                    >

                    <small>
                        Used when the category page is shared on social platforms.
                        Maximum 5MB.
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
                            alt="Open Graph image preview"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================================
             DISPLAY
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


            <div class="admin-form-grid--three admin-form-grid">

                {{-- Sort Order --}}
                <div class="admin-form-group">

                    <label for="sort_order">
                        Sort Order
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', 0) }}"
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


                {{-- Status --}}
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
                            @selected(old('status', '1') == '1')
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(old('status') === '0')
                        >
                            Inactive
                        </option>

                    </select>

                    <small>
                        Inactive categories will not be shown as active.
                    </small>

                    @error('status')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Featured --}}
                <div class="admin-form-group">

                    <label>
                        Featured
                    </label>

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="featured"
                            value="1"
                            @checked(old('featured'))
                        >

                        <span>
                            Mark this category as featured
                        </span>

                    </label>

                    <small>
                        Featured categories can be highlighted on the storefront.
                    </small>

                    @error('featured')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </div>


        {{-- ==========================================================
             SEO
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

                {{-- Meta Title --}}
                <div class="admin-form-group">

                    <label for="meta_title">
                        Meta Title
                    </label>

                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title') }}"
                        maxlength="255"
                        placeholder="Category SEO title"
                    >

                    <small>
                        Recommended title for search engines.
                    </small>

                    @error('meta_title')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Robots --}}
                <div class="admin-form-group">

                    <label for="robots">
                        Robots
                    </label>

                    <select
                        id="robots"
                        name="robots"
                    >

                        <option
                            value="index,follow"
                            @selected(old('robots', 'index,follow') === 'index,follow')
                        >
                            index,follow
                        </option>

                        <option
                            value="index,nofollow"
                            @selected(old('robots') === 'index,nofollow')
                        >
                            index,nofollow
                        </option>

                        <option
                            value="noindex,follow"
                            @selected(old('robots') === 'noindex,follow')
                        >
                            noindex,follow
                        </option>

                        <option
                            value="noindex,nofollow"
                            @selected(old('robots') === 'noindex,nofollow')
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


                {{-- Meta Description --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="meta_description">
                        Meta Description
                    </label>

                    <textarea
                        id="meta_description"
                        name="meta_description"
                        maxlength="1000"
                        placeholder="Write a useful search engine description..."
                    >{{ old('meta_description') }}</textarea>

                    @error('meta_description')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Meta Keywords --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="meta_keywords">
                        Meta Keywords
                    </label>

                    <textarea
                        id="meta_keywords"
                        name="meta_keywords"
                        maxlength="1000"
                        placeholder="travel, holidays, tours..."
                    >{{ old('meta_keywords') }}</textarea>

                    <small>
                        Optional. Separate keywords with commas.
                    </small>

                    @error('meta_keywords')
                        <small class="admin-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Canonical URL --}}
                <div class="admin-form-group admin-form-group--full">

                    <label for="canonical_url">
                        Canonical URL
                    </label>

                    <input
                        type="url"
                        id="canonical_url"
                        name="canonical_url"
                        value="{{ old('canonical_url') }}"
                        maxlength="500"
                        placeholder="https://example.com/tour-categories/holidays"
                    >

                    <small>
                        Leave blank if the system should use the default category URL.
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
             FORM ACTIONS
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
                        Ready to create?
                    </strong>

                    <small
                        style="
                            display:block;
                            margin-top:4px;
                            color:var(--admin-text-light);
                            font-size:9px;
                        "
                    >
                        Review the information before saving the category.
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
                        style="
                            border-color:var(--admin-border);
                            background:#fff;
                            color:var(--admin-text-secondary);
                        "
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="admin-button admin-button--dark"
                    >
                        Create Category
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