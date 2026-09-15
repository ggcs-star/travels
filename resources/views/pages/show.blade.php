@extends('layouts.app')

@section('title', $page->seo?->meta_title ?: $page->title)

@section('meta_description', $page->seo?->meta_description ?: $page->excerpt)

@push('head')
    @if($page->seo?->canonical_url)
        <link rel="canonical" href="{{ $page->seo->canonical_url }}">
    @endif

    @if($page->seo?->robots)
        <meta name="robots" content="{{ $page->seo->robots }}">
    @endif

    @if($page->seo?->meta_keywords)
        <meta name="keywords" content="{{ $page->seo->meta_keywords }}">
    @endif

    @if($page->seo?->og_title)
        <meta property="og:title" content="{{ $page->seo->og_title }}">
    @endif

    @if($page->seo?->og_description)
        <meta property="og:description" content="{{ $page->seo->og_description }}">
    @endif

    @if($page->seo?->og_image)
        <meta
            property="og:image"
            content="{{ filter_var($page->seo->og_image, FILTER_VALIDATE_URL) ? $page->seo->og_image : asset('storage/' . ltrim($page->seo->og_image, '/')) }}"
        >
    @endif

    @if($page->seo?->twitter_title)
        <meta name="twitter:title" content="{{ $page->seo->twitter_title }}">
    @endif

    @if($page->seo?->twitter_description)
        <meta name="twitter:description" content="{{ $page->seo->twitter_description }}">
    @endif

    @if($page->seo?->twitter_image)
        <meta
            name="twitter:image"
            content="{{ filter_var($page->seo->twitter_image, FILTER_VALIDATE_URL) ? $page->seo->twitter_image : asset('storage/' . ltrim($page->seo->twitter_image, '/')) }}"
        >
    @endif
@endpush

@php
    $pageBackground = (string) app(\App\Services\SettingsService::class)->get(
        'visual.background_color',
        '#f6f8f8'
    );

    $featuredImageUrl = null;

    if ($page->featured_image) {
        $featuredImageUrl = filter_var($page->featured_image, FILTER_VALIDATE_URL)
            ? $page->featured_image
            : asset('storage/' . ltrim($page->featured_image, '/'));
    }

    $readingMinutes = max(
        1,
        (int) ceil(
            str_word_count(strip_tags((string) $page->content)) / 200
        )
    );
@endphp

@section('content')

{{-- ================================================================
     PAGE-SPECIFIC UI
     Kept inside the page so it reliably overrides the homepage
     transparent-header styling for every CMS page.
================================================================= --}}
<style>
    /* =============================================================
       CMS PAGE — FINAL PUBLIC UI
       Header follows the My Bookings visual language.
    ============================================================= */

    body:has(.cms-page) {
        background: #f6f4ee !important;
        overflow-x: hidden;
    }

    body:has(.cms-page) #main-content {
        background: #f6f4ee !important;
    }

    /* =============================================================
       HEADER — SAME STYLE AS MY BOOKINGS
    ============================================================= */

    body:has(.cms-page) .site-header.kanila-site-header {
        position: absolute !important;
        top: 48px !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;

        height: 94px !important;
        min-height: 94px !important;

        background: #0b302b !important;
        background-color: #0b302b !important;

        border-bottom: 1px solid rgba(255,255,255,.10) !important;
        box-shadow: 0 8px 28px rgba(0,0,0,.10) !important;

        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;

        z-index: 9999 !important;
    }

    body:has(.cms-page) .site-header.kanila-site-header .site-header-inner {
        height: 94px !important;
        min-height: 94px !important;

        background: transparent !important;
        background-color: transparent !important;

        border: 0 !important;
        box-shadow: none !important;
    }

    body:has(.cms-page) .kanila-site-header .nav-link,
    body:has(.cms-page) .kanila-site-header .nav-dropdown-trigger {
        color: rgba(255,255,255,.94) !important;
        background: transparent !important;
    }

    body:has(.cms-page) .kanila-site-header .nav-link:hover,
    body:has(.cms-page) .kanila-site-header .nav-dropdown-trigger:hover,
    body:has(.cms-page) .kanila-site-header .nav-link.active {
        color: #f28a24 !important;
    }

    body:has(.cms-page) .kanila-site-header .kanila-nav-arrow {
        color: rgba(255,255,255,.94) !important;
    }

    body:has(.cms-page) .kanila-site-header .kanila-nav-dropdown:hover .kanila-nav-arrow {
        color: #f28a24 !important;
    }

    body:has(.cms-page) .kanila-site-header .kanila-get-touch {
        background: #f28a24 !important;
        color: #ffffff !important;
    }

    /* =============================================================
       PAGE SHELL
    ============================================================= */

    .cms-page {
        width: 100%;
        min-height: 100vh;
        padding: 142px 0 0;
        background: #f6f4ee;
        color: #183a35;
    }

    .cms-page__container {
        width: min(1200px, calc(100% - 48px));
        margin: 0 auto;
    }

    /* =============================================================
       PAGE HERO
    ============================================================= */

    .cms-page__hero {
        padding: 48px 0 30px;
    }

    .cms-page__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 14px;

        color: #f28a24;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .cms-page__eyebrow::before {
        content: "";
        width: 28px;
        height: 2px;
        border-radius: 999px;
        background: currentColor;
    }

    .cms-page__title {
        max-width: 950px;
        margin: 0;

        color: #102d3e;
        font-size: clamp(42px, 5vw, 66px);
        line-height: 1.04;
        font-weight: 700;
        letter-spacing: -.04em;
    }

    .cms-page__excerpt {
        max-width: 860px;
        margin: 20px 0 0;

        color: #526577;
        font-size: clamp(17px, 2vw, 20px);
        line-height: 1.7;
    }

    .cms-page__meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;

        margin-top: 18px;

        color: #72818e;
        font-size: 13px;
        font-weight: 600;
    }

    .cms-page__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .cms-page__meta-item svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
    }

    .cms-page__meta-divider {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #b8c1c7;
    }

    /* =============================================================
       FEATURED IMAGE — COMPACT / PROFESSIONAL
    ============================================================= */

    .cms-page__featured {
        width: 100%;
        margin: 8px 0 48px;

        overflow: hidden;

        border: 0;
        border-radius: 22px;
        background: #e7eceb;

        box-shadow:
            0 18px 45px rgba(15, 52, 46, .10);
    }

    .cms-page__featured-media {
        width: 100%;
        height: 420px;
        max-height: 420px;
        overflow: hidden;
    }

    .cms-page__featured img {
        display: block;

        width: 100%;
        height: 100%;
        max-width: 100%;

        object-fit: cover;
        object-position: center;

        transition: transform .5s ease;
    }

    .cms-page__featured:hover img {
        transform: scale(1.015);
    }

    /* =============================================================
       PAGE CONTENT
    ============================================================= */

    .cms-page__content-wrap {
        padding: 0 0 84px;
    }

    .cms-page__content {
        max-width: 920px;
        margin: 0 auto;

        color: #344757;
        font-size: 17px;
        line-height: 1.85;

        overflow-wrap: anywhere;
    }

    .cms-page__content > *:first-child {
        margin-top: 0;
    }

    .cms-page__content h1,
    .cms-page__content h2,
    .cms-page__content h3,
    .cms-page__content h4,
    .cms-page__content h5,
    .cms-page__content h6 {
        margin: 1.7em 0 .65em;

        color: #102d3e;
        line-height: 1.25;
        font-weight: 700;
    }

    .cms-page__content h1 {
        font-size: 38px;
    }

    .cms-page__content h2 {
        font-size: 31px;
    }

    .cms-page__content h3 {
        font-size: 26px;
    }

    .cms-page__content h4 {
        font-size: 22px;
    }

    .cms-page__content p {
        margin: 0 0 1.15em;
    }

    .cms-page__content strong {
        color: #173b35;
    }

    .cms-page__content a {
        color: #e9651d;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .cms-page__content img {
        display: block;
        width: auto;
        max-width: 100%;
        height: auto;

        margin: 28px auto;
        border-radius: 14px;
    }

    .cms-page__content figure {
        margin: 30px 0;
    }

    .cms-page__content figcaption {
        margin-top: 9px;

        color: #788595;
        font-size: 14px;
        text-align: center;
    }

    .cms-page__content ul,
    .cms-page__content ol {
        margin: 0 0 1.3em;
        padding-left: 1.5em;
    }

    .cms-page__content li {
        margin: .4em 0;
    }

    .cms-page__content blockquote {
        margin: 28px 0;
        padding: 18px 22px;

        border-left: 4px solid #f28a24;
        border-radius: 0 12px 12px 0;

        background: rgba(255,255,255,.62);
        color: #4b5e70;
    }

    .cms-page__content table {
        width: 100%;
        margin: 28px 0;

        border-collapse: collapse;

        display: block;
        overflow-x: auto;
    }

    .cms-page__content th,
    .cms-page__content td {
        min-width: 130px;
        padding: 12px 14px;

        border: 1px solid #dce3e5;
        text-align: left;
    }

    .cms-page__content th {
        background: rgba(255,255,255,.72);
        color: #173b35;
        font-weight: 700;
    }

    /* =============================================================
       RESPONSIVE
    ============================================================= */

    @media (max-width: 1200px) {
        .cms-page__container {
            width: min(100% - 40px, 1100px);
        }

        .cms-page__featured-media {
            height: 380px;
            max-height: 380px;
        }

        body:has(.cms-page) .kanila-site-header .kanila-get-touch {
            width: 190px !important;
            font-size: 15px !important;
        }

        body:has(.cms-page) .kanila-site-header .nav-link,
        body:has(.cms-page) .kanila-site-header .nav-dropdown-trigger {
            padding: 0 9px !important;
            font-size: 14px !important;
        }
    }

    @media (max-width: 900px) {
        body:has(.cms-page) .site-header.kanila-site-header {
            top: 0 !important;
            height: 82px !important;
            min-height: 82px !important;
        }

        body:has(.cms-page) .site-header.kanila-site-header .site-header-inner {
            height: 82px !important;
            min-height: 82px !important;
        }

        .cms-page {
            padding-top: 82px;
        }

        .cms-page__hero {
            padding: 38px 0 26px;
        }

        .cms-page__featured-media {
            height: 340px;
            max-height: 340px;
        }
    }

    @media (max-width: 768px) {
        .cms-page__container {
            width: min(100% - 30px, 680px);
        }

        .cms-page__hero {
            padding: 32px 0 24px;
        }

        .cms-page__title {
            font-size: clamp(35px, 10vw, 50px);
        }

        .cms-page__excerpt {
            margin-top: 15px;
            font-size: 16px;
            line-height: 1.65;
        }

        .cms-page__featured {
            margin: 8px 0 34px;
            border-radius: 16px;
        }

        .cms-page__featured-media {
            height: 285px;
            max-height: 285px;
        }

        .cms-page__content {
            font-size: 16px;
            line-height: 1.75;
        }

        .cms-page__content h1 {
            font-size: 30px;
        }

        .cms-page__content h2 {
            font-size: 27px;
        }

        .cms-page__content h3 {
            font-size: 23px;
        }

        .cms-page__content h4 {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        body:has(.cms-page) .site-header.kanila-site-header {
            height: 76px !important;
            min-height: 76px !important;
        }

        body:has(.cms-page) .site-header.kanila-site-header .site-header-inner {
            height: 76px !important;
            min-height: 76px !important;
            padding: 0 14px !important;
        }

        .cms-page {
            padding-top: 76px;
        }

        .cms-page__container {
            width: calc(100% - 24px);
        }

        .cms-page__hero {
            padding: 28px 0 20px;
        }

        .cms-page__featured {
            border-radius: 13px;
            margin-bottom: 28px;
        }

        .cms-page__featured-media {
            height: 220px;
            max-height: 220px;
        }

        .cms-page__meta {
            font-size: 12px;
        }

        .cms-page__content {
            font-size: 15.5px;
        }
    }
</style>

<article class="cms-page">

    <div class="cms-page__container">

        <header class="cms-page__hero">

            <!-- <span class="cms-page__eyebrow">
                SSB Travelz
            </span> -->

            <h1 class="cms-page__title">
                {{ $page->title }}
            </h1>

            @if($page->excerpt)
                <p class="cms-page__excerpt">
                    {{ $page->excerpt }}
                </p>
            @endif

            <div class="cms-page__meta">

                <span class="cms-page__meta-item">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>

                    {{ $readingMinutes }} min read
                </span>

                @if($page->published_at)
                    <span class="cms-page__meta-divider"></span>

                    <span class="cms-page__meta-item">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4M8 3v4M3 10h18"></path>
                        </svg>

                        {{ $page->published_at->format('M d, Y') }}
                    </span>
                @endif

            </div>

        </header>

        @if($featuredImageUrl)
            <figure class="cms-page__featured">
                <div class="cms-page__featured-media">
                    <img
                        src="{{ $featuredImageUrl }}"
                        alt="{{ $page->featured_image_alt ?: $page->title }}"
                        loading="eager"
                        decoding="async"
                    >
                </div>
            </figure>
        @endif

        <div class="cms-page__content-wrap">
            <div class="cms-page__content">
                {!! $page->content !!}
            </div>
        </div>

    </div>

</article>

@endsection
