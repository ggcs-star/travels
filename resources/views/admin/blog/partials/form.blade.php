@php

    $isEdit = isset($blog) && $blog;

    /*
    |--------------------------------------------------------------------------
    | Existing Values
    |--------------------------------------------------------------------------
    */

    $selectedTags = old(
        'tags',
        $isEdit
            ? $blog->tags->pluck('name')->toArray()
            : []
    );

    $selectedRelatedTours = old(
        'related_tours',
        $isEdit
            ? $blog->relatedTours->pluck('id')->toArray()
            : []
    );

    $faqs = old(
        'faqs',
        $isEdit
            ? $blog->faqs
                ->map(fn ($faq) => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'is_active' => $faq->is_active,
                ])
                ->toArray()
            : []
    );

    $videos = old(
        'videos',
        $isEdit
            ? $blog->videos
                ->map(fn ($video) => [
                    'url' => $video->url,
                    'title' => $video->title,
                    'thumbnail' => $video->thumbnail,
                    'is_active' => $video->is_active,
                ])
                ->toArray()
            : []
    );

    if (empty($faqs)) {
        $faqs = [];
    }

    if (empty($videos)) {
        $videos = [];
    }

@endphp


<div class="blog-editor-layout">


    {{-- ================================================================
         MAIN EDITOR
    ================================================================= --}}

    <div class="blog-editor-main">


        {{-- ============================================================
             HERO / INTRO
        ============================================================= --}}

        <div class="blog-editor-intro">

            <div class="blog-editor-intro__icon">
                ✦
            </div>

            <div>

                <span>
                    {{ $isEdit ? 'EDIT TRAVEL CONTENT' : 'NEW TRAVEL CONTENT' }}
                </span>

                <h2>
                    {{ $isEdit ? 'Update your travel story' : 'Create your travel story' }}
                </h2>

                <p>
                    Add useful travel information, stories and guides for your travellers.
                </p>

            </div>

        </div>


        {{-- ============================================================
             STEP 01 — BASIC INFORMATION
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card"
            id="blogBasicSection"
        >

            <div class="blog-editor-card__header">

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        01
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            START HERE
                        </span>

                        <h2>
                            Basic Information
                        </h2>

                        <p>
                            Give your article a clear title and choose how it should be organized.
                        </p>

                    </div>

                </div>

            </div>


            <div class="blog-editor-card__body">

                {{-- TITLE --}}

                <div class="blog-field blog-field--full">

                    <div class="blog-field__label-row">

                        <label for="title">
                            Blog Title
                            <span>*</span>
                        </label>

                        <span
                            class="blog-character-count"
                            data-count-for="title"
                            data-max="200"
                        >
                            0 / 200
                        </span>

                    </div>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $blog->title ?? '') }}"
                        maxlength="200"
                        required
                        class="blog-input blog-input--title"
                        placeholder="Example: 10 Best Places to Visit in Rajasthan"
                    >

                    @error('title')
                        <small class="blog-field-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- CATEGORY --}}

                <div class="blog-field-grid blog-field-grid--two">

                    <div class="blog-field">

                        <div class="blog-field__label-row">
                            <label for="category_id">
                                Category
                            </label>

                            <button
                                type="button"
                                class="blog-inline-action"
                                id="openBlogCategoryModal"
                            >
                                + Create category
                            </button>
                        </div>

                        <div class="blog-select-with-action">

                            <select
                                id="category_id"
                                name="category_id"
                                class="blog-input"
                            >

                                <option value="">
                                    Select category
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            (string) old(
                                                'category_id',
                                                $blog->category_id ?? ''
                                            ) === (string) $category->id
                                        )
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <small class="blog-field-help">
                            Select an existing category or create one without leaving this page.
                        </small>

                        @error('category_id')
                            <small class="blog-field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- READING TIME --}}

                    <div class="blog-field">

                        <label for="reading_time">
                            Reading Time
                        </label>

                        <div class="blog-input-suffix">

                            <input
                                type="number"
                                id="reading_time"
                                name="reading_time"
                                value="{{ old('reading_time', $blog->reading_time ?? '') }}"
                                min="1"
                                max="999"
                                class="blog-input"
                                placeholder="Auto"
                            >

                            <span>
                                min
                            </span>

                        </div>

                        <small class="blog-field-help">
                            Leave empty and it can be calculated automatically.
                        </small>

                    </div>

                </div>


                {{-- SLUG --}}

                <div class="blog-field blog-field--full">

                    <label for="slug">
                        URL Slug
                    </label>

                    <div class="blog-slug-control">

                        <span>
                            /blog/
                        </span>

                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="{{ old('slug', $blog->slug ?? '') }}"
                            maxlength="255"
                            class="blog-input"
                            placeholder="best-places-to-visit-in-rajasthan"
                        >

                    </div>

                    <small class="blog-field-help">
                        Leave empty to generate it from the title.
                    </small>

                    @error('slug')
                        <small class="blog-field-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- EXCERPT --}}

                <div class="blog-field blog-field--full">

                    <div class="blog-field__label-row">

                        <label for="excerpt">
                            Short Description
                        </label>

                        <span
                            class="blog-character-count"
                            data-count-for="excerpt"
                            data-max="1000"
                        >
                            0 / 1000
                        </span>

                    </div>

                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="4"
                        maxlength="1000"
                        class="blog-input blog-textarea"
                        placeholder="Write a short introduction that tells travellers what they will learn..."
                    >{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>

                    <small class="blog-field-help">
                        This can appear on blog cards, search results and previews.
                    </small>

                    @error('excerpt')
                        <small class="blog-field-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </section>


        {{-- ============================================================
             STEP 02 — ARTICLE
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-editor-card--article"
            id="blogArticleSection"
        >

            <div class="blog-editor-card__header">

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        02
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            YOUR STORY
                        </span>

                        <h2>
                            Article Content
                        </h2>

                        <p>
                            Write the complete travel guide, destination story or travel article.
                        </p>

                    </div>

                </div>


                <div class="blog-editor-card__badge">
                    Required
                </div>

            </div>


            <div class="blog-editor-card__body blog-editor-card__body--article">

                <div class="blog-writing-toolbar">

                    <span>
                        ARTICLE
                    </span>

                    <small>
                        Keep paragraphs short and easy to read.
                    </small>

                </div>


                <div class="blog-field blog-field--full">

                    <textarea
                        id="content"
                        name="content"
                        rows="22"
                        required
                        class="blog-input blog-article-editor"
                        placeholder="Start writing your travel article here...

Example:

Introduction

Rajasthan is one of India's most colourful destinations...

Top places to visit

1. Jaipur
2. Udaipur
3. Jaisalmer

Travel tips

..."
                    >{{ old('content', $blog->content ?? '') }}</textarea>

                    <div class="blog-editor-bottom">

                        <small>
                            You can connect your preferred rich text editor to this field later.
                        </small>

                        <span
                            class="blog-character-count"
                            data-count-for="content"
                        >
                            0 characters
                        </span>

                    </div>

                    @error('content')
                        <small class="blog-field-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

        </section>


        {{-- ============================================================
             STEP 03 — TRAVEL INFORMATION
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card"
            id="blogTravelSection"
        >

            <div class="blog-editor-card__header">

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        03
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            TRAVELLER INFORMATION
                        </span>

                        <h2>
                            Travel Details
                        </h2>

                        <p>
                            Add practical information that helps travellers plan their trip.
                        </p>

                    </div>

                </div>

            </div>


            <div class="blog-editor-card__body">

                <div class="blog-field-grid blog-field-grid--two">

                    {{-- DESTINATION --}}

                    <div class="blog-field">

                        <label for="destination">
                            Destination
                        </label>

                        <input
                            type="text"
                            id="destination"
                            name="destination"
                            value="{{ old('destination', $blog->destination ?? '') }}"
                            maxlength="180"
                            class="blog-input"
                            placeholder="Rajasthan, India"
                        >

                    </div>


                    {{-- TRAVEL TYPE --}}

                    <div class="blog-field">

                        <label for="travel_type">
                            Travel Type
                        </label>

                        <select
                            id="travel_type"
                            name="travel_type"
                            class="blog-input"
                        >

                            <option value="">
                                Select travel type
                            </option>

                            @foreach([
                                'Adventure',
                                'Beach',
                                'City Break',
                                'Family',
                                'Honeymoon',
                                'Luxury',
                                'Nature',
                                'Pilgrimage',
                                'Road Trip',
                                'Solo Travel',
                                'Wildlife',
                                'Other'
                            ] as $travelType)

                                <option
                                    value="{{ $travelType }}"
                                    @selected(
                                        old(
                                            'travel_type',
                                            $blog->travel_type ?? ''
                                        ) === $travelType
                                    )
                                >
                                    {{ $travelType }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BEST TIME --}}

                    <div class="blog-field">

                        <label for="best_time_to_visit">
                            Best Time to Visit
                        </label>

                        <input
                            type="text"
                            id="best_time_to_visit"
                            name="best_time_to_visit"
                            value="{{ old('best_time_to_visit', $blog->best_time_to_visit ?? '') }}"
                            maxlength="180"
                            class="blog-input"
                            placeholder="October to March"
                        >

                    </div>


                    {{-- DURATION --}}

                    <div class="blog-field">

                        <label for="duration_days">
                            Recommended Duration
                        </label>

                        <div class="blog-input-suffix">

                            <input
                                type="number"
                                id="duration_days"
                                name="duration_days"
                                value="{{ old('duration_days', $blog->duration_days ?? '') }}"
                                min="1"
                                max="365"
                                class="blog-input"
                                placeholder="5"
                            >

                            <span>
                                days
                            </span>

                        </div>

                    </div>


                    {{-- BUDGET MIN --}}

                    <div class="blog-field">

                        <label for="budget_min">
                            Budget From
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="budget_min"
                            name="budget_min"
                            value="{{ old('budget_min', $blog->budget_min ?? '') }}"
                            class="blog-input"
                            placeholder="25000"
                        >

                    </div>


                    {{-- BUDGET MAX --}}

                    <div class="blog-field">

                        <label for="budget_max">
                            Budget Up To
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="budget_max"
                            name="budget_max"
                            value="{{ old('budget_max', $blog->budget_max ?? '') }}"
                            class="blog-input"
                            placeholder="75000"
                        >

                    </div>


                    {{-- CURRENCY --}}

                    <div class="blog-field">

                        <label for="currency">
                            Currency
                        </label>

                        <select
                            id="currency"
                            name="currency"
                            class="blog-input"
                        >

                            @foreach([
                                'INR' => 'Indian Rupee (INR)',
                                'USD' => 'US Dollar (USD)',
                                'EUR' => 'Euro (EUR)',
                                'GBP' => 'British Pound (GBP)'
                            ] as $currencyCode => $currencyName)

                                <option
                                    value="{{ $currencyCode }}"
                                    @selected(
                                        old(
                                            'currency',
                                            $blog->currency ?? 'INR'
                                        ) === $currencyCode
                                    )
                                >
                                    {{ $currencyName }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </section>


        {{-- ============================================================
             STEP 04 — FEATURED IMAGE
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card"
            id="blogImageSection"
        >

            <div class="blog-editor-card__header">

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        04
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            VISUAL
                        </span>

                        <h2>
                            Featured Image
                        </h2>

                        <p>
                            Choose the main image travellers will see first.
                        </p>

                    </div>

                </div>

            </div>


            <div class="blog-editor-card__body">

                @if($isEdit && $blog->featured_image)

                    <div class="blog-existing-image">

                        <div class="blog-existing-image__preview">

                            <img
                                src="{{ $blog->featured_image_url ?? \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                                alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                            >

                        </div>


                        <div class="blog-existing-image__content">

                            <span class="blog-image-label">
                                CURRENT IMAGE
                            </span>

                            <strong>
                                Featured image is already uploaded
                            </strong>

                            <small>
                                Upload a new image below if you want to replace it.
                            </small>


                            <label class="blog-check-row">

                                <input
                                    type="checkbox"
                                    name="remove_featured_image"
                                    value="1"
                                >

                                <span>
                                    Remove current image
                                </span>

                            </label>

                        </div>

                    </div>

                @endif


                <div class="blog-upload-zone">

                    <input
                        type="file"
                        id="featured_image"
                        name="featured_image"
                        accept=".jpg,.jpeg,.png,.webp,.avif"
                    >

                    <label for="featured_image">

                        <span class="blog-upload-zone__icon">
                            ↑
                        </span>

                        <strong>
                            Upload featured image
                        </strong>

                        <span>
                            Drag and drop or click to browse
                        </span>

                        <small>
                            JPG, PNG, WebP or AVIF · Maximum 5 MB
                        </small>

                    </label>

                </div>


                <div
                    class="blog-file-preview"
                    id="featuredImagePreview"
                ></div>


                <div class="blog-field-grid blog-field-grid--two blog-image-meta-fields">

                    <div class="blog-field">

                        <label for="featured_image_alt">
                            Image Alt Text
                        </label>

                        <input
                            type="text"
                            id="featured_image_alt"
                            name="featured_image_alt"
                            value="{{ old('featured_image_alt', $blog->featured_image_alt ?? '') }}"
                            maxlength="255"
                            class="blog-input"
                            placeholder="Rajasthan travel destination"
                        >

                        <small class="blog-field-help">
                            Describe what is shown in the image.
                        </small>

                    </div>


                    <div class="blog-field">

                        <label for="featured_image_caption">
                            Image Caption
                        </label>

                        <input
                            type="text"
                            id="featured_image_caption"
                            name="featured_image_caption"
                            value="{{ old('featured_image_caption', $blog->featured_image_caption ?? '') }}"
                            maxlength="500"
                            class="blog-input"
                            placeholder="Beautiful Rajasthan landscape"
                        >

                    </div>

                </div>


                @error('featured_image')
                    <small class="blog-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

        </section>


        {{-- ============================================================
             STEP 05 — TAGS
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogTagsSection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogTagsContent"
                aria-expanded="true"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        05
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            ORGANIZATION
                        </span>

                        <h2>
                            Tags
                        </h2>

                        <p>
                            Add searchable topics related to this article.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogTagsContent"
            >

                <div
                    class="blog-tags-manager"
                    id="blogTagsManager"
                >

                    @forelse($selectedTags as $tag)

                        <div class="blog-tag-row">

                            <span class="blog-tag-row__icon">
                                #
                            </span>

                            <input
                                type="text"
                                name="tags[]"
                                value="{{ $tag }}"
                                maxlength="100"
                                class="blog-input"
                                placeholder="Example: Rajasthan"
                            >

                            <button
                                type="button"
                                class="blog-remove-row"
                                onclick="removeBlogRow(this)"
                                aria-label="Remove tag"
                            >
                                ×
                            </button>

                        </div>

                    @empty

                        <div class="blog-tag-row">

                            <span class="blog-tag-row__icon">
                                #
                            </span>

                            <input
                                type="text"
                                name="tags[]"
                                value=""
                                maxlength="100"
                                class="blog-input"
                                placeholder="Example: Rajasthan"
                            >

                            <button
                                type="button"
                                class="blog-remove-row"
                                onclick="removeBlogRow(this)"
                                aria-label="Remove tag"
                            >
                                ×
                            </button>

                        </div>

                    @endforelse

                </div>


                <button
                    type="button"
                    class="blog-add-row"
                    onclick="addBlogTag()"
                >
                    <span>+</span>
                    Add another tag
                </button>

            </div>

        </section>


        {{-- ============================================================
             STEP 06 — GALLERY
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogGallerySection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogGalleryContent"
                aria-expanded="false"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        06
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            MEDIA
                        </span>

                        <h2>
                            Photo Gallery
                        </h2>

                        <p>
                            Add additional images that support your article.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogGalleryContent"
                hidden
            >

                @if($isEdit && $blog->images->isNotEmpty())

                    <div class="blog-existing-gallery">

                        <div class="blog-existing-gallery__heading">

                            <strong>
                                Existing Gallery
                            </strong>

                            <span>
                                {{ $blog->images->count() }}
                                {{ Str::plural('image', $blog->images->count()) }}
                            </span>

                        </div>


                        <div class="blog-gallery-grid">

                            @foreach($blog->images as $image)

                                <div class="blog-gallery-item">

                                    <img
                                        src="{{ $image->url ?? \Illuminate\Support\Facades\Storage::url($image->path) }}"
                                        alt="{{ $image->alt_text ?: $blog->title }}"
                                        loading="lazy"
                                    >

                                    <label class="blog-gallery-remove">

                                        <input
                                            type="checkbox"
                                            name="remove_images[]"
                                            value="{{ $image->id }}"
                                        >

                                        <span>
                                            Remove
                                        </span>

                                    </label>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif


                <div class="blog-upload-zone blog-upload-zone--compact">

                    <input
                        type="file"
                        id="gallery"
                        name="gallery[]"
                        accept=".jpg,.jpeg,.png,.webp,.avif"
                        multiple
                    >

                    <label for="gallery">

                        <span class="blog-upload-zone__icon">
                            ↑
                        </span>

                        <strong>
                            Add gallery images
                        </strong>

                        <span>
                            Select multiple images at once
                        </span>

                        <small>
                            Up to 20 images · Maximum 5 MB each
                        </small>

                    </label>

                </div>


                <div
                    class="blog-file-preview blog-file-preview--gallery"
                    id="galleryPreview"
                ></div>

            </div>

        </section>


        {{-- ============================================================
             STEP 07 — VIDEOS
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogVideoSection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogVideoContent"
                aria-expanded="false"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        07
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            MEDIA
                        </span>

                        <h2>
                            Travel Videos
                        </h2>

                        <p>
                            Add YouTube or other travel videos related to this article.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogVideoContent"
                hidden
            >

                <div id="blogVideosManager">

                    @foreach($videos as $index => $video)

                        <div class="blog-repeater-item blog-video-item">

                            <div class="blog-repeater-item__header">

                                <div>

                                    <span>
                                        VIDEO {{ $index + 1 }}
                                    </span>

                                    <strong>
                                        Travel Video
                                    </strong>

                                </div>

                                <button
                                    type="button"
                                    class="blog-remove-row blog-remove-row--text"
                                    onclick="removeBlogRow(this)"
                                >
                                    Remove
                                </button>

                            </div>


                            <div class="blog-field-grid blog-field-grid--two">

                                <div class="blog-field blog-field--full">

                                    <label>
                                        Video URL
                                    </label>

                                    <input
                                        type="url"
                                        name="videos[{{ $index }}][url]"
                                        value="{{ $video['url'] ?? '' }}"
                                        class="blog-input"
                                        placeholder="https://www.youtube.com/watch?v=..."
                                    >

                                </div>


                                <div class="blog-field">

                                    <label>
                                        Video Title
                                    </label>

                                    <input
                                        type="text"
                                        name="videos[{{ $index }}][title]"
                                        value="{{ $video['title'] ?? '' }}"
                                        maxlength="255"
                                        class="blog-input"
                                        placeholder="Rajasthan Travel Guide"
                                    >

                                </div>


                                <div class="blog-field">

                                    <label>
                                        Thumbnail URL
                                    </label>

                                    <input
                                        type="url"
                                        name="videos[{{ $index }}][thumbnail]"
                                        value="{{ $video['thumbnail'] ?? '' }}"
                                        maxlength="500"
                                        class="blog-input"
                                        placeholder="Optional thumbnail URL"
                                    >

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                <button
                    type="button"
                    class="blog-add-row"
                    onclick="addBlogVideo()"
                >
                    <span>+</span>
                    Add another video
                </button>

            </div>

        </section>


        {{-- ============================================================
             STEP 08 — FAQ
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogFaqSection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogFaqContent"
                aria-expanded="false"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        08
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            HELPFUL INFORMATION
                        </span>

                        <h2>
                            Frequently Asked Questions
                        </h2>

                        <p>
                            Answer common questions travellers may have about this topic.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogFaqContent"
                hidden
            >

                <div id="blogFaqManager">

                    @foreach($faqs as $index => $faq)

                        <div class="blog-repeater-item blog-faq-item">

                            <div class="blog-repeater-item__header">

                                <div>

                                    <span>
                                        FAQ {{ $index + 1 }}
                                    </span>

                                    <strong>
                                        Traveller Question
                                    </strong>

                                </div>


                                <button
                                    type="button"
                                    class="blog-remove-row blog-remove-row--text"
                                    onclick="removeBlogRow(this)"
                                >
                                    Remove
                                </button>

                            </div>


                            <div class="blog-field">

                                <label>
                                    Question
                                </label>

                                <input
                                    type="text"
                                    name="faqs[{{ $index }}][question]"
                                    value="{{ $faq['question'] ?? '' }}"
                                    maxlength="500"
                                    class="blog-input"
                                    placeholder="What is the best time to visit Rajasthan?"
                                >

                            </div>


                            <div class="blog-field">

                                <label>
                                    Answer
                                </label>

                                <textarea
                                    name="faqs[{{ $index }}][answer]"
                                    rows="5"
                                    maxlength="5000"
                                    class="blog-input blog-textarea"
                                    placeholder="Write a helpful answer for travellers..."
                                >{{ $faq['answer'] ?? '' }}</textarea>

                            </div>


                            <label class="blog-check-row">

                                <input
                                    type="checkbox"
                                    name="faqs[{{ $index }}][is_active]"
                                    value="1"
                                    @checked($faq['is_active'] ?? true)
                                >

                                <span>
                                    Show this FAQ on the website
                                </span>

                            </label>

                        </div>

                    @endforeach

                </div>


                <button
                    type="button"
                    class="blog-add-row"
                    onclick="addBlogFaq()"
                >
                    <span>+</span>
                    Add FAQ
                </button>

            </div>

        </section>


        {{-- ============================================================
             STEP 09 — RELATED TOURS
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogToursSection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogToursContent"
                aria-expanded="false"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        09
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            CROSS SELL
                        </span>

                        <h2>
                            Related Tour Packages
                        </h2>

                        <p>
                            Connect this article with relevant tour packages.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogToursContent"
                hidden
            >

                @if($tours->isNotEmpty())

                    <div class="blog-tour-selection">

                        @foreach($tours as $tour)

                            <label class="blog-tour-option">

                                <input
                                    type="checkbox"
                                    name="related_tours[]"
                                    value="{{ $tour->id }}"
                                    @checked(
                                        in_array(
                                            $tour->id,
                                            $selectedRelatedTours
                                        )
                                    )
                                >

                                <span class="blog-tour-option__check">
                                    ✓
                                </span>


                                <span class="blog-tour-option__content">

                                    <strong>
                                        {{ $tour->name }}
                                    </strong>

                                    @if($tour->destination)

                                        <small>
                                            {{ $tour->destination }}
                                        </small>

                                    @endif

                                </span>

                            </label>

                        @endforeach

                    </div>

                @else

                    <div class="blog-empty-inline">

                        <div>
                            ✦
                        </div>

                        <strong>
                            No published tour packages available
                        </strong>

                        <small>
                            Publish a tour package first to connect it with this article.
                        </small>

                    </div>

                @endif

            </div>

        </section>


        {{-- ============================================================
             STEP 10 — SEO
        ============================================================= --}}

        <section
            class="admin-card blog-editor-card blog-collapsible-card"
            id="blogSeoSection"
        >

            <button
                type="button"
                class="blog-collapsible-trigger"
                data-collapse-target="blogSeoContent"
                aria-expanded="false"
            >

                <div class="blog-editor-card__heading">

                    <div class="blog-editor-step">
                        10
                    </div>

                    <div>

                        <span class="blog-editor-kicker">
                            SEARCH & SOCIAL
                        </span>

                        <h2>
                            SEO & Social Sharing
                        </h2>

                        <p>
                            Optional advanced settings for search engines and social media.
                        </p>

                    </div>

                </div>


                <span class="blog-collapse-icon">
                   ⌄
                </span>

            </button>


            <div
                class="blog-collapsible-content"
                id="blogSeoContent"
                hidden
            >

                <div class="blog-seo-notice">

                    <span>
                        ✦
                    </span>

                    <div>

                        <strong>
                            Recommended
                        </strong>

                        <p>
                            If you are not familiar with SEO, you can leave most of these fields empty.
                            The system can use your article information automatically.
                        </p>

                    </div>

                </div>


                <div class="blog-field-grid blog-field-grid--two">


                    {{-- SEO KEY --}}

                    <div class="blog-field blog-field--full">

                        <label for="seo_key">
                            SEO Key
                        </label>

                        <input
                            type="text"
                            id="seo_key"
                            name="seo_key"
                            value="{{ old('seo_key', $blog->seo_key ?? '') }}"
                            maxlength="160"
                            class="blog-input"
                            placeholder="rajasthan-travel-guide"
                        >

                        <small class="blog-field-help">
                            Leave empty if you want automatic generation.
                        </small>

                        @error('seo_key')
                            <small class="blog-field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- META TITLE --}}

                    <div class="blog-field blog-field--full">

                        <div class="blog-field__label-row">

                            <label for="meta_title">
                                Meta Title
                            </label>

                            <span
                                class="blog-character-count"
                                data-count-for="meta_title"
                                data-max="255"
                            >
                                0 / 255
                            </span>

                        </div>

                        <input
                            type="text"
                            id="meta_title"
                            name="meta_title"
                            value="{{ old('meta_title', $blog->meta_title ?? '') }}"
                            maxlength="255"
                            class="blog-input"
                            placeholder="Best Places to Visit in Rajasthan | {{ config('travels.brand.name', 'Travels') }}"
                        >

                        <small class="blog-field-help">
                            Aim for a concise search result title.
                        </small>

                    </div>


                    {{-- META DESCRIPTION --}}

                    <div class="blog-field blog-field--full">

                        <div class="blog-field__label-row">

                            <label for="meta_description">
                                Meta Description
                            </label>

                            <span
                                class="blog-character-count"
                                data-count-for="meta_description"
                                data-max="1000"
                            >
                                0 / 1000
                            </span>

                        </div>

                        <textarea
                            id="meta_description"
                            name="meta_description"
                            rows="4"
                            maxlength="1000"
                            class="blog-input blog-textarea"
                            placeholder="Discover the best places to visit in Rajasthan..."
                        >{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>

                    </div>


                    {{-- META KEYWORDS --}}

                    <div class="blog-field blog-field--full">

                        <label for="meta_keywords">
                            Meta Keywords
                        </label>

                        <input
                            type="text"
                            id="meta_keywords"
                            name="meta_keywords"
                            value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}"
                            maxlength="2000"
                            class="blog-input"
                            placeholder="rajasthan travel, india travel, travel guide"
                        >

                    </div>


                    {{-- ROBOTS --}}

                    <div class="blog-field">

                        <label for="robots">
                            Search Robots
                        </label>

                        <select
                            id="robots"
                            name="robots"
                            class="blog-input"
                        >

                            @foreach([
                                'index,follow' => 'Index, Follow',
                                'index,nofollow' => 'Index, Nofollow',
                                'noindex,follow' => 'Noindex, Follow',
                                'noindex,nofollow' => 'Noindex, Nofollow',
                            ] as $robotValue => $robotLabel)

                                <option
                                    value="{{ $robotValue }}"
                                    @selected(
                                        old(
                                            'robots',
                                            $blog->robots ?? 'index,follow'
                                        ) === $robotValue
                                    )
                                >
                                    {{ $robotLabel }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- CANONICAL --}}

                    <div class="blog-field">

                        <label for="canonical_url">
                            Canonical URL
                        </label>

                        <input
                            type="url"
                            id="canonical_url"
                            name="canonical_url"
                            value="{{ old('canonical_url', $blog->canonical_url ?? '') }}"
                            maxlength="500"
                            class="blog-input"
                            placeholder="https://example.com/blog/..."
                        >

                    </div>


                    {{-- OG TITLE --}}

                    <div class="blog-field blog-field--full">

                        <label for="og_title">
                            Social Share Title
                        </label>

                        <input
                            type="text"
                            id="og_title"
                            name="og_title"
                            value="{{ old('og_title', $blog->og_title ?? '') }}"
                            maxlength="255"
                            class="blog-input"
                            placeholder="Title shown when shared on social media"
                        >

                    </div>


                    {{-- OG DESCRIPTION --}}

                    <div class="blog-field blog-field--full">

                        <label for="og_description">
                            Social Share Description
                        </label>

                        <textarea
                            id="og_description"
                            name="og_description"
                            rows="4"
                            maxlength="1000"
                            class="blog-input blog-textarea"
                            placeholder="Description shown when this article is shared..."
                        >{{ old('og_description', $blog->og_description ?? '') }}</textarea>

                    </div>


                    {{-- OG IMAGE --}}

                    <div class="blog-field blog-field--full">

                        <label for="og_image">
                            Social Share Image
                        </label>


                        @if($isEdit && $blog->og_image)

                            <div class="blog-existing-image blog-existing-image--small">

                                <div class="blog-existing-image__preview">

                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::url($blog->og_image) }}"
                                        alt="Social share image"
                                    >

                                </div>


                                <div class="blog-existing-image__content">

                                    <strong>
                                        Current social image
                                    </strong>

                                    <small>
                                        Upload a new image below to replace it.
                                    </small>


                                    <label class="blog-check-row">

                                        <input
                                            type="checkbox"
                                            name="remove_og_image"
                                            value="1"
                                        >

                                        <span>
                                            Remove current image
                                        </span>

                                    </label>

                                </div>

                            </div>

                        @endif


                        <div class="blog-upload-zone blog-upload-zone--compact">

                            <input
                                type="file"
                                id="og_image"
                                name="og_image"
                                accept=".jpg,.jpeg,.png,.webp,.avif"
                            >

                            <label for="og_image">

                                <span class="blog-upload-zone__icon">
                                    ↑
                                </span>

                                <strong>
                                    Upload social share image
                                </strong>

                                <span>
                                    Recommended for Facebook, WhatsApp and other social previews
                                </span>

                                <small>
                                    JPG, PNG, WebP or AVIF · Maximum 5 MB
                                </small>

                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- ============================================================
             MOBILE ACTIONS
        ============================================================= --}}

        <div class="blog-mobile-publish-actions">

            <button
                type="submit"
                name="status"
                value="draft"
                class="blog-publish-button blog-publish-button--secondary"
            >
                Save Draft
            </button>


            <button
                type="submit"
                name="status"
                value="published"
                class="blog-publish-button blog-publish-button--primary"
            >
                {{ $isEdit ? 'Update & Publish' : 'Publish Blog' }}
            </button>

        </div>

    </div>


    {{-- ================================================================
         SIDEBAR
    ================================================================= --}}

    <aside class="blog-editor-sidebar">


        {{-- ============================================================
             PUBLISH CARD
        ============================================================= --}}

        <section class="admin-card blog-publish-card">

            <div class="blog-publish-card__top">

                <div>

                    <span>
                        PUBLISH
                    </span>

                    <h2>
                        Publishing
                    </h2>

                </div>

                <div class="blog-publish-indicator">
                    ●
                </div>

            </div>


            <div class="blog-publish-card__body">

                {{-- STATUS --}}

                <div class="blog-field">

                    <label for="status">
                        Status
                        <span>*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="blog-input blog-status-select"
                    >

                        <option
                            value="draft"
                            @selected(
                                old(
                                    'status',
                                    $blog->status ?? 'draft'
                                ) === 'draft'
                            )
                        >
                            Draft
                        </option>

                        <option
                            value="published"
                            @selected(
                                old(
                                    'status',
                                    $blog->status ?? ''
                                ) === 'published'
                            )
                        >
                            Published
                        </option>

                        <option
                            value="scheduled"
                            @selected(
                                old(
                                    'status',
                                    $blog->status ?? ''
                                ) === 'scheduled'
                            )
                        >
                            Scheduled
                        </option>

                        <option
                            value="inactive"
                            @selected(
                                old(
                                    'status',
                                    $blog->status ?? ''
                                ) === 'inactive'
                            )
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- PUBLISHED DATE --}}

                <div
                    class="blog-field blog-publishing-date-field"
                    id="publishedAtField"
                >

                    <label for="published_at">
                        Publish Date
                    </label>

                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        value="{{ old(
                            'published_at',
                            $blog?->published_at
                                ? $blog->published_at->format('Y-m-d\TH:i')
                                : ''
                        ) }}"
                        class="blog-input"
                    >

                </div>


                {{-- SCHEDULE DATE --}}

                <div
                    class="blog-field blog-publishing-date-field"
                    id="scheduledAtField"
                >

                    <label for="scheduled_at">
                        Schedule Date & Time
                    </label>

                    <input
                        type="datetime-local"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old(
                            'scheduled_at',
                            $blog?->scheduled_at
                                ? $blog->scheduled_at->format('Y-m-d\TH:i')
                                : ''
                        ) }}"
                        class="blog-input"
                    >

                    <small class="blog-field-help">
                        The article will be published according to this date and time.
                    </small>

                    @error('scheduled_at')
                        <small class="blog-field-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- FEATURED --}}

                <label class="blog-feature-toggle">

                    <input
                        type="hidden"
                        name="featured"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="featured"
                        value="1"
                        @checked(
                            old(
                                'featured',
                                $blog->featured ?? false
                            )
                        )
                    >

                    <span class="blog-feature-toggle__switch">
                    </span>

                    <span class="blog-feature-toggle__content">

                        <strong>
                            Featured Post
                        </strong>

                        <small>
                            Highlight this article on the website.
                        </small>

                    </span>

                </label>


                {{-- PRIMARY ACTION --}}

                <button
                    type="submit"
                    class="blog-primary-submit"
                >

                    <span>
                        {{ $isEdit ? 'Update Blog Post' : 'Create Blog Post' }}
                    </span>

                    <span>
                        →
                    </span>

                </button>


                @if($isEdit)

                    <a
                        href="{{ route('admin.blog.show', $blog) }}"
                        class="blog-secondary-submit"
                    >
                        View Blog Post
                    </a>

                @endif

            </div>

        </section>


        {{-- ============================================================
             URL PREVIEW
        ============================================================= --}}

        <section class="admin-card blog-side-card">

            <div class="blog-side-card__header">

                <span>
                    PREVIEW
                </span>

                <strong>
                    Public URL
                </strong>

            </div>


            <div class="blog-url-preview">

                <span>
                    {{ url('/blog') }}/
                </span>

                <strong id="blog-slug-preview">
                    {{ old('slug', $blog->slug ?? 'your-blog-slug') }}
                </strong>

            </div>

        </section>


        {{-- ============================================================
             QUICK CHECKLIST
        ============================================================= --}}

        <section class="admin-card blog-side-card">

            <div class="blog-side-card__header">

                <span>
                    QUICK CHECK
                </span>

                <strong>
                    Before Publishing
                </strong>

            </div>


            <div class="blog-checklist">

                <div class="blog-checklist-item">

                    <span>
                        01
                    </span>

                    <p>
                        Clear and useful title
                    </p>

                </div>


                <div class="blog-checklist-item">

                    <span>
                        02
                    </span>

                    <p>
                        Featured image added
                    </p>

                </div>


                <div class="blog-checklist-item">

                    <span>
                        03
                    </span>

                    <p>
                        Travel details completed
                    </p>

                </div>


                <div class="blog-checklist-item">

                    <span>
                        04
                    </span>

                    <p>
                        Article content written
                    </p>

                </div>


                <div class="blog-checklist-item">

                    <span>
                        05
                    </span>

                    <p>
                        SEO reviewed
                    </p>

                </div>

            </div>

        </section>


        {{-- ============================================================
             SIMPLE TIP
        ============================================================= --}}

        <section class="blog-editor-tip">

            <div class="blog-editor-tip__icon">
                ✦
            </div>

            <div>

                <strong>
                    Tip for better travel content
                </strong>

                <p>
                    Give travellers practical information they can actually use:
                    timing, budget, duration, transport and useful tips.
                </p>

            </div>

        </section>

    </aside>

</div>




<style>
/* ================================================================
   QUICK CATEGORY CREATOR — FINAL
   ================================================================ */
.blog-field__label-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:8px;
}

.blog-field__label-row label{ margin:0; }

.blog-inline-action{
    appearance:none;
    border:0;
    background:transparent;
    color:#0f766e;
    font:inherit;
    font-size:12px;
    font-weight:700;
    line-height:1.2;
    padding:4px 0;
    cursor:pointer;
    white-space:nowrap;
}

.blog-inline-action:hover{ color:#115e59; text-decoration:underline; }

.blog-category-quick-modal{
    position:fixed !important;
    top:0 !important;
    right:0 !important;
    bottom:0 !important;
    left:0 !important;
    width:100vw !important;
    height:100vh !important;
    margin:0 !important;
    padding:20px !important;
    box-sizing:border-box !important;
    z-index:2147483000 !important;
    display:none !important;
    align-items:center !important;
    justify-content:center !important;
}

.blog-category-quick-modal.is-open{
    display:flex !important;
}

.blog-category-quick-modal__overlay{
    position:absolute !important;
    inset:0 !important;
    width:100% !important;
    height:100% !important;
    background:rgba(7,18,20,.62) !important;
    backdrop-filter:blur(4px);
}

.blog-category-quick-modal__dialog{
    position:relative !important;
    z-index:2 !important;
    width:min(580px,100%) !important;
    max-height:calc(100vh - 40px) !important;
    margin:0 !important;
    overflow:auto !important;
    box-sizing:border-box !important;
    background:#fff !important;
    border:1px solid rgba(15,23,42,.08) !important;
    border-radius:18px !important;
    box-shadow:0 30px 90px rgba(0,0,0,.28) !important;
}

.blog-category-quick-modal__header{
    display:flex !important;
    align-items:flex-start !important;
    justify-content:space-between !important;
    gap:18px !important;
    padding:22px 24px 18px !important;
    border-bottom:1px solid #edf0f2 !important;
}

.blog-category-quick-modal__eyebrow{
    display:block !important;
    margin-bottom:5px !important;
    color:#0f766e !important;
    font-size:9px !important;
    font-weight:800 !important;
    letter-spacing:.14em !important;
}

.blog-category-quick-modal__header h2{
    margin:0 !important;
    color:#142c2a !important;
    font-size:20px !important;
    line-height:1.25 !important;
    font-weight:800 !important;
}

.blog-category-quick-modal__header p{
    margin:6px 0 0 !important;
    color:#77817f !important;
    font-size:11px !important;
    line-height:1.5 !important;
}

.blog-category-quick-modal__close{
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    flex:0 0 34px !important;
    width:34px !important;
    height:34px !important;
    padding:0 !important;
    border:1px solid #e4e9e8 !important;
    border-radius:9px !important;
    background:#fff !important;
    color:#60706d !important;
    font-size:20px !important;
    line-height:1 !important;
    cursor:pointer !important;
}

.blog-category-quick-modal__close:hover{
    background:#f5f8f7 !important;
    color:#17302d !important;
}

.blog-category-quick-modal__body{
    padding:22px 24px !important;
}

.blog-category-quick-modal__grid{
    display:grid !important;
    grid-template-columns:1fr 1fr !important;
    gap:15px !important;
}

.blog-category-quick-field{ min-width:0 !important; }

.blog-category-quick-field--full{ grid-column:1 / -1 !important; }

.blog-category-quick-field label{
    display:block !important;
    margin:0 0 6px !important;
    color:#34423f !important;
    font-size:10px !important;
    font-weight:750 !important;
}

.blog-category-quick-field label span{ color:#c0392b !important; }

.blog-category-quick-field input,
.blog-category-quick-field textarea{
    display:block !important;
    width:100% !important;
    box-sizing:border-box !important;
    padding:10px 11px !important;
    border:1px solid #dfe6e4 !important;
    border-radius:9px !important;
    outline:none !important;
    background:#fff !important;
    color:#243532 !important;
    font-family:inherit !important;
    font-size:11px !important;
}

.blog-category-quick-field input{ height:39px !important; }

.blog-category-quick-field textarea{
    min-height:92px !important;
    resize:vertical !important;
}

.blog-category-quick-field input:focus,
.blog-category-quick-field textarea:focus{
    border-color:#0f766e !important;
    box-shadow:0 0 0 3px rgba(15,118,110,.09) !important;
}

.blog-category-quick-field small{
    display:block !important;
    margin-top:5px !important;
    color:#929c99 !important;
    font-size:9px !important;
    line-height:1.4 !important;
}

.blog-category-quick-modal__error{
    margin:0 0 15px !important;
    padding:9px 11px !important;
    border:1px solid #f2c7c3 !important;
    border-radius:8px !important;
    background:#fff5f4 !important;
    color:#a63d35 !important;
    font-size:10px !important;
    line-height:1.45 !important;
}

.blog-category-quick-modal__footer{
    display:flex !important;
    align-items:center !important;
    justify-content:flex-end !important;
    gap:8px !important;
    padding:14px 24px 20px !important;
    border-top:1px solid #edf0f2 !important;
}

.blog-category-quick-modal__footer .admin-button{
    min-width:105px !important;
}

body.blog-category-modal-open{ overflow:hidden !important; }

@media(max-width:640px){
    .blog-category-quick-modal{ padding:10px !important; }
    .blog-category-quick-modal__dialog{ max-height:calc(100vh - 20px) !important; border-radius:14px !important; }
    .blog-category-quick-modal__header,
    .blog-category-quick-modal__body,
    .blog-category-quick-modal__footer{ padding-left:16px !important; padding-right:16px !important; }
    .blog-category-quick-modal__grid{ grid-template-columns:1fr !important; }
    .blog-category-quick-field--full{ grid-column:auto !important; }
    .blog-category-quick-modal__footer{ flex-direction:column-reverse !important; align-items:stretch !important; }
    .blog-category-quick-modal__footer .admin-button{ width:100% !important; }
}
</style>

{{-- ====================================================================
     QUICK CREATE CATEGORY MODAL
===================================================================== --}}

<div
    class="blog-category-quick-modal"
    id="blogCategoryQuickModal"
    aria-hidden="true"
    style="display:none;"
>
    <div
        class="blog-category-quick-modal__overlay"
        data-close-blog-category-modal
    ></div>

    <div
        class="blog-category-quick-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="blogCategoryQuickModalTitle"
    >
        <div class="blog-category-quick-modal__header">

            <div>
                <span class="blog-category-quick-modal__eyebrow">
                    BLOG CATEGORY
                </span>

                <h2 id="blogCategoryQuickModalTitle">
                    Create Category
                </h2>

                <p>
                    Add a category without leaving the blog editor.
                </p>
            </div>

            <button
                type="button"
                class="blog-category-quick-modal__close"
                id="closeBlogCategoryModal"
                aria-label="Close"
            >
                ×
            </button>

        </div>
<div
    id="blogQuickCategoryForm"
    data-action="{{ route('admin.blog-categories.store') }}"
>
            @csrf

            <div class="blog-category-quick-modal__body">

                <div
                    class="blog-category-quick-modal__error"
                    id="blogCategoryQuickError"
                    hidden
                ></div>

                <div class="blog-category-quick-modal__grid">

                    <div class="blog-category-quick-field blog-category-quick-field--full">
                        <label for="quick_category_name">
                            Category Name
                            <span>*</span>
                        </label>
<input
    type="text"
    id="quick_category_name"
    name="name"
    maxlength="120"
    autocomplete="off"
    placeholder="Example: Destination Guides"
>
                    </div>

                    <div class="blog-category-quick-field">
                        <label for="quick_category_slug">
                            Slug
                        </label>

                        <input
                            type="text"
                            id="quick_category_slug"
                            name="slug"
                            maxlength="160"
                            autocomplete="off"
                            placeholder="destination-guides"
                        >

                        <small>
                            Leave empty to generate automatically.
                        </small>
                    </div>

                    <div class="blog-category-quick-field">
                        <label for="quick_category_sort_order">
                            Sort Order
                        </label>

                        <input
                            type="number"
                            id="quick_category_sort_order"
                            name="sort_order"
                            min="0"
                            value="0"
                        >
                    </div>

                    <div class="blog-category-quick-field blog-category-quick-field--full">
                        <label for="quick_category_description">
                            Description
                        </label>

                        <textarea
                            id="quick_category_description"
                            name="description"
                            rows="4"
                            placeholder="Short description for this category..."
                        ></textarea>
                    </div>

                    <input
                        type="hidden"
                        name="is_active"
                        value="1"
                    >

                </div>
            </div>

            <div class="blog-category-quick-modal__footer">

                <button
                    type="button"
                    class="admin-button"
                    id="cancelBlogCategoryModal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="admin-button admin-button--primary"
                    id="submitBlogCategory"
                >
                    <span class="blog-category-submit-text">
                        Create Category
                    </span>

                    <span
                        class="blog-category-submit-loading"
                        hidden
                    >
                        Creating...
                    </span>
                </button>

            </div>
</div>    </div>
</div>

{{-- ====================================================================
     JAVASCRIPT
===================================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const titleInput =
        document.getElementById('title');

    const slugInput =
        document.getElementById('slug');

    const slugPreview =
        document.getElementById('blog-slug-preview');

    const statusInput =
        document.getElementById('status');

    const publishedAtField =
        document.getElementById('publishedAtField');

    const scheduledAtField =
        document.getElementById('scheduledAtField');


    /*
    |--------------------------------------------------------------------------
    | SLUG
    |--------------------------------------------------------------------------
    */

    function slugify(value) {

        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');

    }


    function updateSlugPreview() {

        if (!slugPreview || !slugInput) {
            return;
        }

        slugPreview.textContent =
            slugInput.value ||
            'your-blog-slug';

    }


    if (titleInput && slugInput) {

        if (slugInput.value.trim() !== '') {

            slugInput.dataset.manual =
                'true';

        }


        titleInput.addEventListener(
            'input',
            function () {

                if (!slugInput.dataset.manual) {

                    slugInput.value =
                        slugify(
                            titleInput.value
                        );

                    updateSlugPreview();

                }

            }
        );


        slugInput.addEventListener(
            'input',
            function () {

                slugInput.dataset.manual =
                    'true';

                slugInput.value =
                    slugify(
                        slugInput.value
                    );

                updateSlugPreview();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | PUBLISHING FIELDS
    |--------------------------------------------------------------------------
    */

    function updatePublishingFields() {

        if (!statusInput) {
            return;
        }

        const status =
            statusInput.value;


        if (publishedAtField) {

            publishedAtField.style.display =
                status === 'published'
                    ? ''
                    : 'none';

        }


        if (scheduledAtField) {

            scheduledAtField.style.display =
                status === 'scheduled'
                    ? ''
                    : 'none';

        }

    }


    if (statusInput) {

        statusInput.addEventListener(
            'change',
            updatePublishingFields
        );

        updatePublishingFields();

    }


    /*
    |--------------------------------------------------------------------------
    | CHARACTER COUNTERS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-count-for]')
        .forEach(function (counter) {

            const targetId =
                counter.dataset.countFor;

            const target =
                document.getElementById(targetId);

            if (!target) {
                return;
            }


            const max =
                counter.dataset.max;


            function updateCounter() {

                const length =
                    target.value
                        ? target.value.length
                        : 0;


                counter.textContent =
                    max
                        ? `${length} / ${max}`
                        : `${length} characters`;

            }


            target.addEventListener(
                'input',
                updateCounter
            );

            updateCounter();

        });


    /*
    |--------------------------------------------------------------------------
    | COLLAPSIBLE SECTIONS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-collapse-target]')
        .forEach(function (trigger) {

            trigger.addEventListener(
                'click',
                function () {

                    const targetId =
                        this.dataset.collapseTarget;

                    const content =
                        document.getElementById(
                            targetId
                        );

                    if (!content) {
                        return;
                    }


                    const isOpen =
                        this.getAttribute(
                            'aria-expanded'
                        ) === 'true';


                    this.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );


                    content.hidden =
                        isOpen;


                    const card =
                        this.closest(
                            '.blog-collapsible-card'
                        );

                    if (card) {

                        card.classList.toggle(
                            'is-expanded',
                            !isOpen
                        );

                    }

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | FEATURED IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    const featuredImageInput =
        document.getElementById(
            'featured_image'
        );

    const featuredImagePreview =
        document.getElementById(
            'featuredImagePreview'
        );


    if (
        featuredImageInput
        &&
        featuredImagePreview
    ) {

        featuredImageInput.addEventListener(
            'change',
            function () {

                featuredImagePreview.innerHTML = '';

                const file =
                    this.files?.[0];

                if (!file) {
                    return;
                }


                if (
                    !file.type.startsWith(
                        'image/'
                    )
                ) {
                    return;
                }


                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        const wrapper =
                            document.createElement(
                                'div'
                            );

                        wrapper.className =
                            'blog-preview-image';


                        wrapper.innerHTML = `

                            <img
                                src="${event.target.result}"
                                alt="Selected featured image"
                            >

                            <div>

                                <strong>
                                    New image selected
                                </strong>

                                <small>
                                    ${file.name}
                                </small>

                            </div>

                        `;


                        featuredImagePreview
                            .appendChild(
                                wrapper
                            );

                    };


                reader.readAsDataURL(
                    file
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GALLERY PREVIEW
    |--------------------------------------------------------------------------
    */

    const galleryInput =
        document.getElementById(
            'gallery'
        );

    const galleryPreview =
        document.getElementById(
            'galleryPreview'
        );


    if (
        galleryInput
        &&
        galleryPreview
    ) {

        galleryInput.addEventListener(
            'change',
            function () {

                galleryPreview.innerHTML = '';

                const files =
                    Array.from(
                        this.files || []
                    );


                files.forEach(
                    function (file) {

                        if (
                            !file.type.startsWith(
                                'image/'
                            )
                        ) {
                            return;
                        }


                        const reader =
                            new FileReader();


                        reader.onload =
                            function (event) {

                                const item =
                                    document.createElement(
                                        'div'
                                    );

                                item.className =
                                    'blog-preview-gallery-item';


                                item.innerHTML = `

                                    <img
                                        src="${event.target.result}"
                                        alt=""
                                    >

                                `;


                                galleryPreview
                                    .appendChild(
                                        item
                                    );

                            };


                        reader.readAsDataURL(
                            file
                        );

                    }
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL SLUG
    |--------------------------------------------------------------------------
    */

    updateSlugPreview();

});


/*
|--------------------------------------------------------------------------
| TAGS
|--------------------------------------------------------------------------
*/

let blogTagIndex =
    {{ count($selectedTags) }};


function addBlogTag() {

    const manager =
        document.getElementById(
            'blogTagsManager'
        );


    if (!manager) {
        return;
    }


    const row =
        document.createElement(
            'div'
        );


    row.className =
        'blog-tag-row';


    row.innerHTML = `

        <span class="blog-tag-row__icon">
            #
        </span>

        <input
            type="text"
            name="tags[]"
            maxlength="100"
            class="blog-input"
            placeholder="Example: Rajasthan"
        >

        <button
            type="button"
            class="blog-remove-row"
            onclick="removeBlogRow(this)"
            aria-label="Remove tag"
        >
            ×
        </button>

    `;


    manager.appendChild(
        row
    );


    const input =
        row.querySelector(
            'input'
        );


    input?.focus();


    blogTagIndex++;

}


/*
|--------------------------------------------------------------------------
| FAQ
|--------------------------------------------------------------------------
*/

let blogFaqIndex =
    {{ count($faqs) }};


function addBlogFaq() {

    const manager =
        document.getElementById(
            'blogFaqManager'
        );


    if (!manager) {
        return;
    }


    const index =
        blogFaqIndex++;


    const item =
        document.createElement(
            'div'
        );


    item.className =
        'blog-repeater-item blog-faq-item';


    item.innerHTML = `

        <div class="blog-repeater-item__header">

            <div>

                <span>
                    NEW FAQ
                </span>

                <strong>
                    Traveller Question
                </strong>

            </div>

            <button
                type="button"
                class="blog-remove-row blog-remove-row--text"
                onclick="removeBlogRow(this)"
            >
                Remove
            </button>

        </div>


        <div class="blog-field">

            <label>
                Question
            </label>

            <input
                type="text"
                name="faqs[${index}][question]"
                maxlength="500"
                class="blog-input"
                placeholder="What is the best time to visit?"
            >

        </div>


        <div class="blog-field">

            <label>
                Answer
            </label>

            <textarea
                name="faqs[${index}][answer]"
                rows="5"
                maxlength="5000"
                class="blog-input blog-textarea"
                placeholder="Write a helpful answer for travellers..."
            ></textarea>

        </div>


        <label class="blog-check-row">

            <input
                type="checkbox"
                name="faqs[${index}][is_active]"
                value="1"
                checked
            >

            <span>
                Show this FAQ on the website
            </span>

        </label>

    `;


    manager.appendChild(
        item
    );

}


/*
|--------------------------------------------------------------------------
| VIDEOS
|--------------------------------------------------------------------------
*/

let blogVideoIndex =
    {{ count($videos) }};


function addBlogVideo() {

    const manager =
        document.getElementById(
            'blogVideosManager'
        );


    if (!manager) {
        return;
    }


    const index =
        blogVideoIndex++;


    const item =
        document.createElement(
            'div'
        );


    item.className =
        'blog-repeater-item blog-video-item';


    item.innerHTML = `

        <div class="blog-repeater-item__header">

            <div>

                <span>
                    NEW VIDEO
                </span>

                <strong>
                    Travel Video
                </strong>

            </div>

            <button
                type="button"
                class="blog-remove-row blog-remove-row--text"
                onclick="removeBlogRow(this)"
            >
                Remove
            </button>

        </div>


        <div class="blog-field-grid blog-field-grid--two">

            <div class="blog-field blog-field--full">

                <label>
                    Video URL
                </label>

                <input
                    type="url"
                    name="videos[${index}][url]"
                    class="blog-input"
                    placeholder="https://www.youtube.com/watch?v=..."
                >

            </div>


            <div class="blog-field">

                <label>
                    Video Title
                </label>

                <input
                    type="text"
                    name="videos[${index}][title]"
                    maxlength="255"
                    class="blog-input"
                    placeholder="Travel Guide Video"
                >

            </div>


            <div class="blog-field">

                <label>
                    Thumbnail URL
                </label>

                <input
                    type="url"
                    name="videos[${index}][thumbnail]"
                    maxlength="500"
                    class="blog-input"
                    placeholder="Optional thumbnail URL"
                >

            </div>

        </div>

    `;


    manager.appendChild(
        item
    );

}


/*
|--------------------------------------------------------------------------
| REMOVE REPEATER
|--------------------------------------------------------------------------
*/

function removeBlogRow(button) {

    const row =
        button.closest(
            '.blog-tag-row, .blog-repeater-item'
        );


    if (!row) {
        return;
    }


    const parent =
        row.parentElement;


    /*
    | Keep one empty tag row available.
    */

    if (
        row.classList.contains(
            'blog-tag-row'
        )
        &&
        parent
        &&
        parent.querySelectorAll(
            '.blog-tag-row'
        ).length <= 1
    ) {

        const input =
            row.querySelector(
                'input'
            );

        if (input) {
            input.value = '';
        }

        return;

    }


    row.remove();

}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const categoryModal =
        document.getElementById('blogCategoryQuickModal');

    const openCategoryButton =
        document.getElementById('openBlogCategoryModal');

    const closeCategoryButton =
        document.getElementById('closeBlogCategoryModal');

    const cancelCategoryButton =
        document.getElementById('cancelBlogCategoryModal');

    const categoryForm =
        document.getElementById('blogQuickCategoryForm');

    const categoryNameInput =
        document.getElementById('quick_category_name');

    const categorySlugInput =
        document.getElementById('quick_category_slug');

    const categorySortInput =
        document.getElementById('quick_category_sort_order');

    const categoryDescriptionInput =
        document.getElementById('quick_category_description');

    const categorySelect =
        document.getElementById('category_id');

    const categoryError =
        document.getElementById('blogCategoryQuickError');

    const submitCategoryButton =
        document.getElementById('submitBlogCategory');

    const submitCategoryText =
        submitCategoryButton?.querySelector(
            '.blog-category-submit-text'
        );

    const submitCategoryLoading =
        submitCategoryButton?.querySelector(
            '.blog-category-submit-loading'
        );


    /* =========================================================
       SLUG
    ========================================================= */

    function categorySlugify(value) {

        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');

    }


    /* =========================================================
       OPEN MODAL
    ========================================================= */

    function openCategoryModal() {

        if (!categoryModal) {
            return;
        }

        categoryModal.setAttribute(
            'aria-hidden',
            'false'
        );

        categoryModal.classList.add(
            'is-open'
        );

        categoryModal.style.display = 'flex';

        document.body.classList.add(
            'blog-category-modal-open'
        );

        clearCategoryError();

        setTimeout(function () {

            categoryNameInput?.focus();

        }, 80);
    }


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeCategoryModal() {

        if (!categoryModal) {
            return;
        }

        categoryModal.setAttribute(
            'aria-hidden',
            'true'
        );

        categoryModal.classList.remove(
            'is-open'
        );

        categoryModal.style.display = 'none';

        document.body.classList.remove(
            'blog-category-modal-open'
        );

        clearCategoryError();

    }


    /* =========================================================
       ERROR
    ========================================================= */

    function clearCategoryError() {

        if (!categoryError) {
            return;
        }

        categoryError.textContent = '';

        categoryError.hidden = true;

    }


    function showCategoryError(message) {

        if (!categoryError) {
            return;
        }

        categoryError.textContent =
            message ||
            'Unable to create category.';

        categoryError.hidden = false;

    }


    /* =========================================================
       LOADING
    ========================================================= */

    function setCategoryLoading(loading) {

        if (!submitCategoryButton) {
            return;
        }

        submitCategoryButton.disabled =
            loading;

        if (submitCategoryText) {

            submitCategoryText.hidden =
                loading;

        }

        if (submitCategoryLoading) {

            submitCategoryLoading.hidden =
                !loading;

        }

    }


    /* =========================================================
       RESET
    ========================================================= */

    function resetCategoryFields() {

        if (categoryNameInput) {
            categoryNameInput.value = '';
        }

        if (categorySlugInput) {

            categorySlugInput.value = '';

            categorySlugInput.dataset.manual =
                '';

        }

        if (categorySortInput) {

            categorySortInput.value =
                '0';

        }

        if (categoryDescriptionInput) {

            categoryDescriptionInput.value =
                '';

        }

        clearCategoryError();

    }


    /* =========================================================
       OPEN
    ========================================================= */

    if (openCategoryButton) {

        openCategoryButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                openCategoryModal();

            }
        );

    }


    /* =========================================================
       CLOSE BUTTON
    ========================================================= */

    if (closeCategoryButton) {

        closeCategoryButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                closeCategoryModal();

            }
        );

    }


    /* =========================================================
       CANCEL
    ========================================================= */

    if (cancelCategoryButton) {

        cancelCategoryButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                closeCategoryModal();

            }
        );

    }


    /* =========================================================
       BACKDROP
    ========================================================= */

    if (categoryModal) {

        const backdrop =
            categoryModal.querySelector(
                '[data-close-blog-category-modal]'
            );

        if (backdrop) {

            backdrop.addEventListener(
                'click',
                function () {

                    closeCategoryModal();

                }
            );

        }

    }


    /* =========================================================
       ESCAPE
    ========================================================= */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                categoryModal &&
                categoryModal.classList.contains(
                    'is-open'
                )
            ) {

                closeCategoryModal();

            }

        }
    );


    /* =========================================================
       AUTO SLUG
    ========================================================= */

    if (categoryNameInput) {

        categoryNameInput.addEventListener(
            'input',
            function () {

                if (
                    categorySlugInput &&
                    !categorySlugInput.dataset.manual
                ) {

                    categorySlugInput.value =
                        categorySlugify(
                            this.value
                        );

                }

            }
        );

    }


    /* =========================================================
       MANUAL SLUG
    ========================================================= */

    if (categorySlugInput) {

        categorySlugInput.addEventListener(
            'input',
            function () {

                this.value =
                    categorySlugify(
                        this.value
                    );

                this.dataset.manual =
                    this.value !== ''
                        ? 'true'
                        : '';

            }
        );

    }


    /* =========================================================
       CREATE CATEGORY
       IMPORTANT:
       BUTTON CLICK, NOT FORM SUBMIT
    ========================================================= */

    if (submitCategoryButton) {

        submitCategoryButton.addEventListener(
            'click',
            async function (event) {

                /*
                |--------------------------------------------------------------------------
                | VERY IMPORTANT
                |--------------------------------------------------------------------------
                | Prevent the main Blog form from submitting.
                */

                event.preventDefault();

                event.stopPropagation();

                clearCategoryError();


                /* -------------------------------------------------------------
                   NAME
                ------------------------------------------------------------- */

                const name =
                    categoryNameInput?.value
                        .trim() || '';


                if (!name) {

                    showCategoryError(
                        'Please enter a category name.'
                    );

                    categoryNameInput?.focus();

                    return;

                }


                /* -------------------------------------------------------------
                   SLUG
                ------------------------------------------------------------- */

                let slug =
                    categorySlugInput?.value
                        .trim() || '';

                if (!slug) {

                    slug =
                        categorySlugify(
                            name
                        );

                }


                /* -------------------------------------------------------------
                   SORT ORDER
                ------------------------------------------------------------- */

                const sortOrder =
                    categorySortInput?.value ||
                    '0';


                /* -------------------------------------------------------------
                   DESCRIPTION
                ------------------------------------------------------------- */

                const description =
                    categoryDescriptionInput?.value
                        .trim() || '';


                /* -------------------------------------------------------------
                   ACTION URL
                ------------------------------------------------------------- */

                const action =
                    categoryForm?.dataset.action;


                if (!action) {

                    showCategoryError(
                        'Category creation URL is missing.'
                    );

                    console.error(
                        'Missing data-action on #blogQuickCategoryForm'
                    );

                    return;

                }


                /* -------------------------------------------------------------
                   FORM DATA
                ------------------------------------------------------------- */

                const formData =
                    new FormData();


                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrfToken =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.getAttribute(
                        'content'
                    );


                if (!csrfToken) {

                    showCategoryError(
                        'Security token is missing. Please refresh the page.'
                    );

                    return;

                }


                formData.append(
                    '_token',
                    csrfToken
                );

                formData.append(
                    'name',
                    name
                );

                formData.append(
                    'slug',
                    slug
                );

                formData.append(
                    'sort_order',
                    sortOrder
                );

                formData.append(
                    'description',
                    description
                );

                formData.append(
                    'is_active',
                    '1'
                );


                /* -------------------------------------------------------------
                   LOADING
                ------------------------------------------------------------- */

                setCategoryLoading(true);


                try {

                    const response =
                        await fetch(
                            action,
                            {
                                method: 'POST',

                                body: formData,

                                credentials:
                                    'same-origin',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                }
                            }
                        );


                    const data =
                        await response
                            .json()
                            .catch(
                                function () {
                                    return {};
                                }
                            );


                    /* ---------------------------------------------------------
                       VALIDATION / SERVER ERROR
                    --------------------------------------------------------- */

                    if (!response.ok) {

                        if (
                            data.errors &&
                            typeof data.errors ===
                                'object'
                        ) {

                            const messages =
                                Object.values(
                                    data.errors
                                )
                                .flat()
                                .filter(Boolean);

                            showCategoryError(
                                messages.join(' ')
                            );

                        } else {

                            showCategoryError(
                                data.message ||
                                'Unable to create category.'
                            );

                        }

                        return;

                    }


                    /* ---------------------------------------------------------
                       CATEGORY RESPONSE
                    --------------------------------------------------------- */

                    const category =
                        data.category;


                    if (
                        !category ||
                        !category.id ||
                        !category.name
                    ) {

                        showCategoryError(
                            'Category was created, but the server response was invalid.'
                        );

                        console.error(
                            'Invalid category response:',
                            data
                        );

                        return;

                    }


                    /* ---------------------------------------------------------
                       ADD TO BLOG CATEGORY DROPDOWN
                    --------------------------------------------------------- */

                    if (categorySelect) {

                        /*
                        |--------------------------------------------------------------------------
                        | Avoid duplicate option
                        |--------------------------------------------------------------------------
                        */

                        const existingOption =
                            categorySelect.querySelector(
                                `option[value="${CSS.escape(String(category.id))}"]`
                            );


                        if (existingOption) {

                            existingOption.selected =
                                true;

                        } else {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                String(
                                    category.id
                                );

                            option.textContent =
                                category.name;

                            option.selected =
                                true;

                            categorySelect.appendChild(
                                option
                            );

                        }


                        categorySelect.value =
                            String(
                                category.id
                            );


                        categorySelect.dispatchEvent(
                            new Event(
                                'change',
                                {
                                    bubbles: true
                                }
                            )
                        );

                    }


                    /* ---------------------------------------------------------
                       CLOSE + RESET
                    --------------------------------------------------------- */

                    closeCategoryModal();

                    resetCategoryFields();


                } catch (error) {

                    console.error(
                        'Blog category creation failed:',
                        error
                    );

                    showCategoryError(
                        'Something went wrong while creating the category. Please try again.'
                    );


                } finally {

                    setCategoryLoading(
                        false
                    );

                }

            }
        );

    }

});
</script>
