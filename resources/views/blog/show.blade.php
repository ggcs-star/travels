@extends('layouts.app')

@section('title', $blog->meta_title ?: $blog->title)

@section('content')

<div class="user-blog-show">

    {{-- HERO --}}
    <header class="user-blog-show__header">

        <div class="user-blog-container">

            <div class="user-blog-breadcrumb">
                <a href="{{ route('blog.index') }}">Blog</a>

                <span>/</span>

                @if($blog->category)
                    <span>{{ $blog->category->name }}</span>
                @endif
            </div>


            @if($blog->category)
                <span class="user-blog-show__category">
                    {{ $blog->category->name }}
                </span>
            @endif


            <h1>
                {{ $blog->title }}
            </h1>


            @if($blog->excerpt)
                <p class="user-blog-show__excerpt">
                    {{ $blog->excerpt }}
                </p>
            @endif


            <div class="user-blog-show__meta">

                @if($blog->author)
                    <span>
                        By {{ $blog->author->username }}
                    </span>
                @endif

                @if($blog->published_at)
                    <span>
                        {{ $blog->published_at->format('d M Y') }}
                    </span>
                @endif

                @if($blog->reading_time)
                    <span>
                        {{ $blog->reading_time }} min read
                    </span>
                @endif

                <span>
                    {{ number_format($blog->views ?? 0) }} views
                </span>

            </div>

        </div>

    </header>


    {{-- FEATURED IMAGE --}}
    @if($blog->featured_image)

        <div class="user-blog-show__featured">

            <div class="user-blog-container">

                <img
                    src="{{ $blog->featured_image_url ?? \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                    alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                >

            </div>

        </div>

    @endif


    {{-- ARTICLE --}}
    <main class="user-blog-show__main">

        <div class="user-blog-container">

            <div class="user-blog-show__layout">

                {{-- CONTENT --}}
                <article class="user-blog-article">

                    {!! nl2br(e($blog->content)) !!}

                </article>


                {{-- SIDEBAR --}}
                <aside class="user-blog-show__sidebar">

                    {{-- SHARE --}}
                    <div class="user-blog-side-card">

                        <h3>
                            Share this article
                        </h3>

                        <div class="user-blog-share">

                            <button
                                type="button"
                                data-share="whatsapp"
                                data-url="{{ url()->current() }}"
                            >
                                WhatsApp
                            </button>

                            <button
                                type="button"
                                data-share="facebook"
                                data-url="{{ url()->current() }}"
                            >
                                Facebook
                            </button>

                            <button
                                type="button"
                                data-share="copy"
                                data-url="{{ url()->current() }}"
                            >
                                Copy link
                            </button>

                        </div>

                    </div>


                    {{-- TRAVEL INFO --}}
                    @if(
                        $blog->destination ||
                        $blog->travel_type ||
                        $blog->best_time ||
                        $blog->duration ||
                        $blog->budget
                    )

                        <div class="user-blog-side-card">

                            <h3>
                                Travel information
                            </h3>


                            @if($blog->destination)
                                <div class="user-blog-info-row">
                                    <span>Destination</span>
                                    <strong>{{ $blog->destination }}</strong>
                                </div>
                            @endif


                            @if($blog->travel_type)
                                <div class="user-blog-info-row">
                                    <span>Travel type</span>
                                    <strong>{{ $blog->travel_type }}</strong>
                                </div>
                            @endif


                            @if($blog->best_time)
                                <div class="user-blog-info-row">
                                    <span>Best time</span>
                                    <strong>{{ $blog->best_time }}</strong>
                                </div>
                            @endif


                            @if($blog->duration)
                                <div class="user-blog-info-row">
                                    <span>Duration</span>
                                    <strong>{{ $blog->duration }}</strong>
                                </div>
                            @endif


                            @if($blog->budget)
                                <div class="user-blog-info-row">
                                    <span>Budget</span>
                                    <strong>{{ $blog->budget }}</strong>
                                </div>
                            @endif

                        </div>

                    @endif


                    {{-- TAGS --}}
                    @if($blog->tags && $blog->tags->isNotEmpty())

                        <div class="user-blog-side-card">

                            <h3>
                                Topics
                            </h3>

                            <div class="user-blog-tags">

                                @foreach($blog->tags as $tag)

                                    <span>
                                        #{{ $tag->name }}
                                    </span>

                                @endforeach

                            </div>

                        </div>

                    @endif

                </aside>

            </div>


            {{-- GALLERY --}}
            @if($blog->images && $blog->images->isNotEmpty())

                <section class="user-blog-content-section">

                    <div class="user-blog-section-heading">

                        <span>
                            PHOTOS
                        </span>

                        <h2>
                            Explore the destination
                        </h2>

                    </div>


                    <div class="user-blog-gallery">

                        @foreach($blog->images as $image)

                            <figure>

                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}"
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


            {{-- VIDEOS --}}
            @if($blog->videos && $blog->videos->isNotEmpty())

                <section class="user-blog-content-section">

                    <div class="user-blog-section-heading">

                        <span>
                            WATCH
                        </span>

                        <h2>
                            Travel videos
                        </h2>

                    </div>


                    <div class="user-blog-videos">

                        @foreach($blog->videos as $video)

                            @php
                                $url = $video->url ?? $video->video_url ?? null;

                                $youtubeId = null;

                                if ($url) {
                                    preg_match(
                                        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?\/]+)/',
                                        $url,
                                        $matches
                                    );

                                    $youtubeId = $matches[1] ?? null;
                                }
                            @endphp


                            @if($youtubeId)

                                <div class="user-blog-video">

                                    <iframe
                                        src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                        title="{{ $video->title ?? 'Travel video' }}"
                                        loading="lazy"
                                        allowfullscreen
                                    ></iframe>

                                </div>

                            @endif

                        @endforeach

                    </div>

                </section>

            @endif


            {{-- FAQ --}}
            @if($blog->faqs && $blog->faqs->isNotEmpty())

                <section class="user-blog-content-section">

                    <div class="user-blog-section-heading">

                        <span>
                            FAQ
                        </span>

                        <h2>
                            Frequently asked questions
                        </h2>

                    </div>


                    <div class="user-blog-faq">

                        @foreach($blog->faqs as $faq)

                            <details>

                                <summary>
                                    {{ $faq->question }}

                                    <span>+</span>
                                </summary>

                                <div>
                                    {{ $faq->answer }}
                                </div>

                            </details>

                        @endforeach

                    </div>

                </section>

            @endif


       {{-- =========================================================
     RELATED TOUR PACKAGES
========================================================= --}}

@if($blog->relatedTours && $blog->relatedTours->isNotEmpty())

    <section class="user-blog-content-section user-blog-related-section">

        <div class="user-blog-section-heading">

            <span>
                TRAVEL WITH US
            </span>

            <h2>
                Related tour packages
            </h2>

            <p>
                Continue your journey with our carefully curated travel experiences.
            </p>

        </div>


        <div class="user-blog-related-tours">

            @foreach($blog->relatedTours as $tour)

                @php

                    /*
                     * ----------------------------------------------------
                     * TOUR IMAGE
                     * ----------------------------------------------------
                     */

                    $tourImage = $tour->cover_image_url
                        ?? (
                            $tour->cover_image
                                ? \Illuminate\Support\Facades\Storage::url(
                                    $tour->cover_image
                                )
                                : null
                        );


                    /*
                     * ----------------------------------------------------
                     * TOUR DURATION
                     * ----------------------------------------------------
                     */

                    $durationText = null;

                    if (
                        $tour->duration_days
                        &&
                        $tour->duration_nights !== null
                    ) {

                        $durationText =
                            $tour->duration_days
                            . ' Days / '
                            . $tour->duration_nights
                            . ' Nights';

                    } elseif ($tour->duration_days) {

                        $durationText =
                            $tour->duration_days . ' Days';

                    }


                    /*
                     * ----------------------------------------------------
                     * FIRST AVAILABLE DEPARTURE
                     * ----------------------------------------------------
                     */

                    $departure = $tour->departures
                        ->filter(function ($item) {

                            return
                                $item->departure_date
                                &&
                                $item->status === 'open';

                        })
                        ->sortBy('departure_date')
                        ->first();


                    /*
                     * ----------------------------------------------------
                     * PRICE
                     *
                     * Sale price gets priority.
                     * Otherwise regular price.
                     * ----------------------------------------------------
                     */

                    $tourPrice = null;

                    $tourCurrency = 'INR';

                    if ($departure) {

                        $tourPrice =
                            $departure->sale_price
                            ?? $departure->price;

                        $tourCurrency =
                            $departure->currency
                            ?: 'INR';

                    }

                @endphp


                <article class="user-blog-tour-card">


                    {{-- IMAGE --}}

                    <a
                        href="{{ route('tours.show', $tour) }}"
                        class="user-blog-tour-card__image"
                    >

                        @if($tourImage)

                            <img
                                src="{{ $tourImage }}"
                                alt="{{ $tour->name }}"
                                loading="lazy"
                            >

                        @else

                            <div class="user-blog-tour-card__placeholder">

                                <span>
                                    TRAVEL
                                </span>

                            </div>

                        @endif

                    </a>


                    {{-- CONTENT --}}

                    <div class="user-blog-tour-card__content">


                        @if($tour->destination)

                            <span class="user-blog-tour-card__destination">
                                {{ $tour->destination }}
                            </span>

                        @endif


                        <h3>

                            <a
                                href="{{ route('tours.show', $tour) }}"
                            >
                                {{ $tour->name }}
                            </a>

                        </h3>


                        @if($tour->short_description)

                            <p>
                                {{ \Illuminate\Support\Str::limit(
                                    strip_tags($tour->short_description),
                                    105
                                ) }}
                            </p>

                        @elseif($tour->description)

                            <p>
                                {{ \Illuminate\Support\Str::limit(
                                    strip_tags($tour->description),
                                    105
                                ) }}
                            </p>

                        @endif


                        <div class="user-blog-tour-card__footer">


                            <div class="user-blog-tour-card__details">


                                @if($durationText)

                                    <span>
                                        <b>Duration</b>
                                        {{ $durationText }}
                                    </span>

                                @endif


                                @if($tourPrice !== null)

                                    <span>

                                        <b>
                                            From
                                        </b>

                                        {{ $tourCurrency === 'INR' ? '₹' : $tourCurrency . ' ' }}

                                        {{ number_format(
                                            (float) $tourPrice,
                                            0
                                        ) }}

                                    </span>

                                @endif


                            </div>


                            <a
                                href="{{ route('tours.show', $tour) }}"
                                class="user-blog-tour-card__link"
                            >

                                View tour

                                <span>
                                    →
                                </span>

                            </a>

                        </div>


                    </div>

                </article>

            @endforeach

        </div>

    </section>

@endif

        </div>

    </main>

</div>


<script>
document.addEventListener('click', function (event) {

    const button = event.target.closest('[data-share]');

    if (!button) {
        return;
    }

    const type = button.dataset.share;
    const url = button.dataset.url;

    if (type === 'copy') {

        navigator.clipboard.writeText(url).then(function () {

            const original = button.textContent;

            button.textContent = 'Copied';

            setTimeout(function () {
                button.textContent = original;
            }, 1500);

        });

        return;
    }


    if (type === 'whatsapp') {

        window.open(
            'https://wa.me/?text=' +
            encodeURIComponent(url),
            '_blank',
            'noopener'
        );

        return;
    }


    if (type === 'facebook') {

        window.open(
            'https://www.facebook.com/sharer/sharer.php?u=' +
            encodeURIComponent(url),
            '_blank',
            'noopener'
        );

    }

});
</script>

@endsection