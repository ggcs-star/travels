@extends('admin.layouts.app')

@section('title', $blog->title)

@section('content')

@php
    $statusLabel = match ($blog->status) {
        \App\Models\Blog::STATUS_PUBLISHED => 'Published',
        \App\Models\Blog::STATUS_SCHEDULED => 'Scheduled',
        \App\Models\Blog::STATUS_INACTIVE => 'Inactive',
        default => 'Draft',
    };

    $statusClass = match ($blog->status) {
        \App\Models\Blog::STATUS_PUBLISHED => 'published',
        \App\Models\Blog::STATUS_SCHEDULED => 'scheduled',
        \App\Models\Blog::STATUS_INACTIVE => 'inactive',
        default => 'draft',
    };
@endphp

<div class="admin-page blog-show-page">

    {{-- ================================================================
         HEADER
    ================================================================= --}}

    <div class="admin-page__header blog-show-header">

        <div class="blog-show-header__content">

            <span class="admin-eyebrow">
                BLOG / VIEW
            </span>

            <h1 class="admin-page__title">
                {{ $blog->title }}
            </h1>

            <p class="admin-page__description">
                Review your travel article, media, SEO and publishing details.
            </p>

        </div>


        <div class="admin-page__header-actions">

            <a
                href="{{ route('admin.blog.edit', $blog) }}"
                class="admin-button admin-button--primary"
            >
                Edit Post
            </a>

            <a
                href="{{ route('admin.blog.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

        </div>

    </div>


    {{-- ================================================================
         STATUS STRIP
    ================================================================= --}}

    <div class="blog-show-status-strip">

        <div class="blog-show-status-strip__left">

            <span class="blog-show-status blog-show-status--{{ $statusClass }}">
                <span class="blog-show-status__dot"></span>
                {{ $statusLabel }}
            </span>

            @if($blog->featured)

                <span class="blog-show-featured">
                    ★ Featured
                </span>

            @endif

            @if($blog->category)

                <span class="blog-show-category">
                    {{ $blog->category->name }}
                </span>

            @endif

        </div>


        <div class="blog-show-status-strip__right">

            <span>
                {{ $blog->views ?? 0 }} views
            </span>

            @if($blog->reading_time)

                <span>
                    {{ $blog->reading_time }} min read
                </span>

            @endif

        </div>

    </div>


    {{-- ================================================================
         MAIN LAYOUT
    ================================================================= --}}

    <div class="blog-show-grid">


        {{-- ============================================================
             MAIN CONTENT
        ============================================================= --}}

        <main class="blog-show-main">


            {{-- ========================================================
                 HERO
            ========================================================= --}}

            <article class="admin-card blog-show-card blog-show-article">

                @if($blog->featured_image)

                    <div class="blog-show-cover">

                        <img
                            src="{{ $blog->featured_image_url ?? \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                            alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                        >

                    </div>

                @endif


                <div class="blog-show-article__body">

                    <div class="blog-show-meta">

                        @if($blog->category)

                            <span>
                                {{ $blog->category->name }}
                            </span>

                        @endif

                        @if($blog->destination)

                            <span>
                                {{ $blog->destination }}
                            </span>

                        @endif

                        @if($blog->published_at)

                            <span>
                                {{ $blog->published_at->format('d M Y') }}
                            </span>

                        @endif

                    </div>


                    <h2 class="blog-show-article__title">
                        {{ $blog->title }}
                    </h2>


                    @if($blog->excerpt)

                        <p class="blog-show-excerpt">
                            {{ $blog->excerpt }}
                        </p>

                    @endif


                    <div class="blog-show-body">

                        {!! $blog->content !!}

                    </div>

                </div>

            </article>


            {{-- ========================================================
                 TRAVEL INFORMATION
            ========================================================= --}}

            @if(
                $blog->destination ||
                $blog->travel_type ||
                $blog->best_time_to_visit ||
                $blog->duration_days ||
                $blog->budget_min !== null ||
                $blog->budget_max !== null
            )

                <section class="admin-card blog-show-card">

                    <div class="blog-show-section-header">

                        <div>

                            <span class="blog-show-section-kicker">
                                TRIP DETAILS
                            </span>

                            <h2>
                                Travel Information
                            </h2>

                            <p>
                                Key travel information associated with this article.
                            </p>

                        </div>

                    </div>


                    <div class="blog-travel-grid">

                        @if($blog->destination)

                            <div class="blog-travel-item">

                                <span>
                                    Destination
                                </span>

                                <strong>
                                    {{ $blog->destination }}
                                </strong>

                            </div>

                        @endif


                        @if($blog->travel_type)

                            <div class="blog-travel-item">

                                <span>
                                    Travel Type
                                </span>

                                <strong>
                                    {{ $blog->travel_type }}
                                </strong>

                            </div>

                        @endif


                        @if($blog->best_time_to_visit)

                            <div class="blog-travel-item">

                                <span>
                                    Best Time
                                </span>

                                <strong>
                                    {{ $blog->best_time_to_visit }}
                                </strong>

                            </div>

                        @endif


                        @if($blog->duration_days)

                            <div class="blog-travel-item">

                                <span>
                                    Duration
                                </span>

                                <strong>
                                    {{ $blog->duration_days }} days
                                </strong>

                            </div>

                        @endif


                        @if(
                            $blog->budget_min !== null ||
                            $blog->budget_max !== null
                        )

                            <div class="blog-travel-item">

                                <span>
                                    Estimated Budget
                                </span>

                                <strong>

                                    @if($blog->currency)
                                        {{ $blog->currency }}
                                    @endif

                                    @if($blog->budget_min !== null)

                                        {{ number_format($blog->budget_min, 0) }}

                                    @endif

                                    @if(
                                        $blog->budget_min !== null &&
                                        $blog->budget_max !== null
                                    )

                                        –

                                    @endif

                                    @if($blog->budget_max !== null)

                                        {{ number_format($blog->budget_max, 0) }}

                                    @endif

                                </strong>

                            </div>

                        @endif

                    </div>

                </section>

            @endif


            {{-- ========================================================
                 GALLERY
            ========================================================= --}}

            @if($blog->images->isNotEmpty())

                <section class="admin-card blog-show-card">

                    <div class="blog-show-section-header">

                        <div>

                            <span class="blog-show-section-kicker">
                                VISUALS
                            </span>

                            <h2>
                                Photo Gallery
                            </h2>

                            <p>
                                {{ $blog->images->count() }}
                                {{ $blog->images->count() === 1 ? 'image' : 'images' }}
                                attached to this article.
                            </p>

                        </div>

                    </div>


                    <div class="blog-show-gallery">

                        @foreach($blog->images as $image)

                            <figure class="blog-show-gallery__item">

                                <img
                                    src="{{ $image->url ?? \Illuminate\Support\Facades\Storage::url($image->path) }}"
                                    alt="{{ $image->alt_text ?: $blog->title }}"
                                    loading="lazy"
                                >

                                @if($image->caption)

                                    <figcaption>
                                        {{ $image->caption }}
                                    </figcaption>

                                @endif

                            </figure>

                        @endforeach

                    </div>

                </section>

            @endif


            {{-- ========================================================
                 VIDEOS
            ========================================================= --}}

            @if($blog->videos->isNotEmpty())

                <section class="admin-card blog-show-card">

                    <div class="blog-show-section-header">

                        <div>

                            <span class="blog-show-section-kicker">
                                VIDEO
                            </span>

                            <h2>
                                Travel Videos
                            </h2>

                            <p>
                                Videos connected with this travel article.
                            </p>

                        </div>

                    </div>


                    <div class="blog-show-video-list">

                        @foreach($blog->videos as $video)

                            @if($video->is_active)

                                <article class="blog-show-video">

                                    <div class="blog-show-video__frame">

                                        @php
                                            $videoUrl = $video->url;
                                            $youtubeId = null;

                                            if (
                                                preg_match(
                                                    '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?\/]+)/',
                                                    $videoUrl,
                                                    $matches
                                                )
                                            ) {
                                                $youtubeId = $matches[1];
                                            }
                                        @endphp


                                        @if($youtubeId)

                                            <iframe
                                                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                                title="{{ $video->title ?: 'Travel video' }}"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen
                                            ></iframe>

                                        @else

                                            <a
                                                href="{{ $videoUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="blog-show-video__external"
                                            >
                                                Open Video
                                            </a>

                                        @endif

                                    </div>


                                    @if($video->title)

                                        <div class="blog-show-video__content">

                                            <h3>
                                                {{ $video->title }}
                                            </h3>

                                        </div>

                                    @endif

                                </article>

                            @endif

                        @endforeach

                    </div>

                </section>

            @endif


            {{-- ========================================================
                 FAQ
            ========================================================= --}}

            @if($blog->faqs->isNotEmpty())

                <section class="admin-card blog-show-card">

                    <div class="blog-show-section-header">

                        <div>

                            <span class="blog-show-section-kicker">
                                FAQ
                            </span>

                            <h2>
                                Frequently Asked Questions
                            </h2>

                            <p>
                                Questions and answers included with this article.
                            </p>

                        </div>

                    </div>


                    <div class="blog-show-faq-list">

                        @foreach($blog->faqs as $faq)

                            @if($faq->is_active)

                                <details class="blog-show-faq">

                                    <summary>

                                        <span>
                                            {{ $faq->question }}
                                        </span>

                                        <b>
                                            +
                                        </b>

                                    </summary>

                                    <div class="blog-show-faq__answer">

                                        {!! nl2br(e($faq->answer)) !!}

                                    </div>

                                </details>

                            @endif

                        @endforeach

                    </div>

                </section>

            @endif


            {{-- ========================================================
                 RELATED TOURS
            ========================================================= --}}

            @if($blog->relatedTours->isNotEmpty())

                <section class="admin-card blog-show-card">

                    <div class="blog-show-section-header">

                        <div>

                            <span class="blog-show-section-kicker">
                                TRAVEL PRODUCTS
                            </span>

                            <h2>
                                Related Tour Packages
                            </h2>

                            <p>
                                Tour packages connected with this article.
                            </p>

                        </div>

                    </div>


                    <div class="blog-show-tours">

                        @foreach($blog->relatedTours as $tour)

                            <div class="blog-show-tour">

                                <div class="blog-show-tour__icon">
                                    ✦
                                </div>

                                <div class="blog-show-tour__content">

                                    <strong>
                                        {{ $tour->name }}
                                    </strong>

                                    @if($tour->destination)

                                        <span>
                                            {{ $tour->destination }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

            @endif

        </main>


        {{-- ============================================================
             SIDEBAR
        ============================================================= --}}

        <aside class="blog-show-sidebar">


            {{-- ========================================================
                 PUBLISHING
            ========================================================= --}}

            <section class="admin-card blog-info-card">

                <div class="blog-info-card__header">

                    <h3>
                        Publishing
                    </h3>

                    <span class="blog-info-card__icon">
                        ✓
                    </span>

                </div>


                <div class="blog-info-row">

                    <span>
                        Status
                    </span>

                    <strong>
                        {{ $statusLabel }}
                    </strong>

                </div>


                <div class="blog-info-row">

                    <span>
                        Featured
                    </span>

                    <strong>
                        {{ $blog->featured ? 'Yes' : 'No' }}
                    </strong>

                </div>


                <div class="blog-info-row">

                    <span>
                        Author
                    </span>

                    <strong>
                        {{ $blog->author?->username ?? $blog->author?->name ?? '—' }}
                    </strong>

                </div>


                <div class="blog-info-row">

                    <span>
                        Created
                    </span>

                    <strong>
                        {{ $blog->created_at?->format('d M Y') ?? '—' }}
                    </strong>

                </div>


                <div class="blog-info-row">

                    <span>
                        Published
                    </span>

                    <strong>
                        {{ $blog->published_at
                            ? $blog->published_at->format('d M Y H:i')
                            : 'Not published'
                        }}
                    </strong>

                </div>

            </section>


            {{-- ========================================================
                 TRAVEL SUMMARY
            ========================================================= --}}

            @if(
                $blog->destination ||
                $blog->travel_type ||
                $blog->duration_days
            )

                <section class="admin-card blog-info-card">

                    <div class="blog-info-card__header">

                        <h3>
                            Travel Summary
                        </h3>

                        <span class="blog-info-card__icon">
                            ✈
                        </span>

                    </div>


                    @if($blog->destination)

                        <div class="blog-info-block">

                            <span>
                                Destination
                            </span>

                            <p>
                                {{ $blog->destination }}
                            </p>

                        </div>

                    @endif


                    @if($blog->travel_type)

                        <div class="blog-info-block">

                            <span>
                                Travel Type
                            </span>

                            <p>
                                {{ $blog->travel_type }}
                            </p>

                        </div>

                    @endif


                    @if($blog->duration_days)

                        <div class="blog-info-block">

                            <span>
                                Duration
                            </span>

                            <p>
                                {{ $blog->duration_days }} days
                            </p>

                        </div>

                    @endif

                </section>

            @endif


            {{-- ========================================================
                 SEO
            ========================================================= --}}

            <section class="admin-card blog-info-card">

                <div class="blog-info-card__header">

                    <h3>
                        SEO
                    </h3>

                    <span class="blog-info-card__icon">
                        SEO
                    </span>

                </div>


                <div class="blog-info-row">

                    <span>
                        SEO Key
                    </span>

                    <strong class="blog-info-value--wrap">
                        {{ $blog->seo_key ?: '—' }}
                    </strong>

                </div>


                <div class="blog-info-row">

                    <span>
                        Robots
                    </span>

                    <strong>
                        {{ $blog->robots ?: '—' }}
                    </strong>

                </div>


                @if($blog->meta_title)

                    <div class="blog-info-block">

                        <span>
                            Meta Title
                        </span>

                        <p>
                            {{ $blog->meta_title }}
                        </p>

                    </div>

                @endif


                @if($blog->meta_description)

                    <div class="blog-info-block">

                        <span>
                            Meta Description
                        </span>

                        <p>
                            {{ $blog->meta_description }}
                        </p>

                    </div>

                @endif


                @if($blog->canonical_url)

                    <div class="blog-info-block">

                        <span>
                            Canonical URL
                        </span>

                        <p class="blog-info-url">
                            {{ $blog->canonical_url }}
                        </p>

                    </div>

                @endif

            </section>


            {{-- ========================================================
                 SOCIAL
            ========================================================= --}}

            @if(
                $blog->og_title ||
                $blog->og_description ||
                $blog->og_image
            )

                <section class="admin-card blog-info-card">

                    <div class="blog-info-card__header">

                        <h3>
                            Social Sharing
                        </h3>

                        <span class="blog-info-card__icon">
                            ↗
                        </span>

                    </div>


                    @if($blog->og_image)

                        <div class="blog-social-image">

                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($blog->og_image) }}"
                                alt="Social share image"
                            >

                        </div>

                    @endif


                    @if($blog->og_title)

                        <div class="blog-info-block">

                            <span>
                                Share Title
                            </span>

                            <p>
                                {{ $blog->og_title }}
                            </p>

                        </div>

                    @endif


                    @if($blog->og_description)

                        <div class="blog-info-block">

                            <span>
                                Share Description
                            </span>

                            <p>
                                {{ $blog->og_description }}
                            </p>

                        </div>

                    @endif

                </section>

            @endif


            {{-- ========================================================
                 TAGS
            ========================================================= --}}

            @if($blog->tags->isNotEmpty())

                <section class="admin-card blog-info-card">

                    <div class="blog-info-card__header">

                        <h3>
                            Tags
                        </h3>

                    </div>


                    <div class="blog-tag-list">

                        @foreach($blog->tags as $tag)

                            <span>
                                #{{ $tag->name }}
                            </span>

                        @endforeach

                    </div>

                </section>

            @endif


            {{-- ========================================================
                 CONTENT STATS
            ========================================================= --}}

            <section class="admin-card blog-info-card">

                <div class="blog-info-card__header">

                    <h3>
                        Content
                    </h3>

                </div>


                <div class="blog-stats-grid">

                    <div>

                        <strong>
                            {{ $blog->images->count() }}
                        </strong>

                        <span>
                            Images
                        </span>

                    </div>


                    <div>

                        <strong>
                            {{ $blog->videos->count() }}
                        </strong>

                        <span>
                            Videos
                        </span>

                    </div>


                    <div>

                        <strong>
                            {{ $blog->faqs->count() }}
                        </strong>

                        <span>
                            FAQs
                        </span>

                    </div>


                    <div>

                        <strong>
                            {{ $blog->relatedTours->count() }}
                        </strong>

                        <span>
                            Tours
                        </span>

                    </div>

                </div>

            </section>


            {{-- ========================================================
                 ACTIONS
            ========================================================= --}}

            <section class="admin-card blog-info-card">

                <div class="blog-info-card__header">

                    <h3>
                        Actions
                    </h3>

                </div>


                <div class="blog-action-stack">


                    {{-- EDIT --}}

                    <a
                        href="{{ route('admin.blog.edit', $blog) }}"
                        class="admin-button admin-button--primary admin-button--wide"
                    >
                        Edit Post
                    </a>


                    {{-- PUBLISH / DRAFT --}}

                    <form
                        method="POST"
                        action="{{ route('admin.blog.status', $blog) }}"
                    >

                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="{{ $blog->status === \App\Models\Blog::STATUS_PUBLISHED
                                ? \App\Models\Blog::STATUS_DRAFT
                                : \App\Models\Blog::STATUS_PUBLISHED
                            }}"
                        >

                        <button
                            type="submit"
                            class="admin-button admin-button--wide"
                        >
                            {{ $blog->status === \App\Models\Blog::STATUS_PUBLISHED
                                ? 'Move to Draft'
                                : 'Publish Post'
                            }}
                        </button>

                    </form>


                    {{-- DUPLICATE --}}

                    <form
                        method="POST"
                        action="{{ route('admin.blog.duplicate', $blog) }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="admin-button admin-button--wide"
                        >
                            Duplicate Post
                        </button>

                    </form>


                    {{-- DELETE --}}

                    <form
                        method="POST"
                        action="{{ route('admin.blog.destroy', $blog) }}"
                        onsubmit="return confirm('Delete this blog post permanently?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="admin-button admin-button--danger admin-button--wide"
                        >
                            Delete Post
                        </button>

                    </form>

                </div>

            </section>

        </aside>

    </div>

</div>

@endsection