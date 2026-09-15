@extends('layouts.app')

@section(
    'title',
    $page->seo?->meta_title
        ?: $page->title
)

@push('head')

    @if($page->seo?->meta_description)

        <meta
            name="description"
            content="{{ $page->seo->meta_description }}"
        >

    @endif


    @if($page->seo?->keywords)

        <meta
            name="keywords"
            content="{{ $page->seo->keywords }}"
        >

    @endif


    @if($page->seo?->robots)

        <meta
            name="robots"
            content="{{ $page->seo->robots }}"
        >

    @endif


    @if($page->seo?->canonical_url)

        <link
            rel="canonical"
            href="{{ $page->seo->canonical_url }}"
        >

    @endif


    @if($page->seo?->og_title)

        <meta
            property="og:title"
            content="{{ $page->seo->og_title }}"
        >

    @endif


    @if($page->seo?->og_description)

        <meta
            property="og:description"
            content="{{ $page->seo->og_description }}"
        >

    @endif


    @if($page->seo?->og_image)

        <meta
            property="og:image"
            content="{{ asset('storage/' . $page->seo->og_image) }}"
        >

    @endif


    @if($page->seo?->twitter_title)

        <meta
            name="twitter:title"
            content="{{ $page->seo->twitter_title }}"
        >

    @endif


    @if($page->seo?->twitter_description)

        <meta
            name="twitter:description"
            content="{{ $page->seo->twitter_description }}"
        >

    @endif


    @if($page->seo?->twitter_image)

        <meta
            name="twitter:image"
            content="{{ asset('storage/' . $page->seo->twitter_image) }}"
        >

    @endif

@endpush


@section('content')

<style>

.public-page {
    max-width: 1150px;
    margin: 0 auto;
    padding: 50px 25px 80px;
}

.public-page-header {
    margin-bottom: 35px;
}

.public-page-title {
    margin: 0;
    color: #0f172a;
    font-size: clamp(32px, 5vw, 52px);
    line-height: 1.15;
}

.public-page-excerpt {
    margin-top: 15px;
    max-width: 850px;
    color: #64748b;
    font-size: 18px;
    line-height: 1.7;
}

.public-page-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 35px;
}

.public-page-content {
    color: #334155;
    font-size: 16px;
    line-height: 1.85;
}

.public-page-content h1,
.public-page-content h2,
.public-page-content h3,
.public-page-content h4 {
    color: #0f172a;
    line-height: 1.3;
    margin-top: 35px;
}

.public-page-content a {
    color: #2563eb;
}

.public-page-content img {
    max-width: 100%;
    height: auto;
}

.public-page-content table {
    width: 100%;
    border-collapse: collapse;
}

.public-page-content th,
.public-page-content td {
    border: 1px solid #e2e8f0;
    padding: 10px;
}

</style>


<article class="public-page">

    <header class="public-page-header">

        <h1 class="public-page-title">
            {{ $page->title }}
        </h1>


        @if($page->excerpt)

            <div class="public-page-excerpt">
                {{ $page->excerpt }}
            </div>

        @endif

    </header>


    @if($page->featured_image)

        <img
            src="{{ asset('storage/' . $page->featured_image) }}"
            alt="{{ $page->image_alt ?: $page->title }}"
            class="public-page-image"
        >

    @endif


    @if($page->content)

        <div class="public-page-content">

            {!! $page->content !!}

        </div>

    @endif

</article>

@endsection