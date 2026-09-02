@extends('layouts.app')

@section('title', $tour->name . ' | ' . config('travels.brand.name'))

@section(
    'meta_description',
    $tour->short_description
        ?: Str::limit(strip_tags($tour->description), 155)
)

@section('content')

{{-- =========================================================
   TOUR DETAIL — KANILA REFERENCE LAYOUT
   ========================================================= --}}

<section class="tour-reference-page">

    {{-- BREADCRUMB STRIP --}}
    <div class="tour-reference-breadcrumb-strip">
        <div class="container tour-reference-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="tour-reference-breadcrumb__arrow">›</span>

            <a href="{{ route('tours.index') }}">Tours</a>

            @if($tour->category)
                <span class="tour-reference-breadcrumb__arrow">›</span>
                <span>{{ $tour->category->name }}</span>
            @endif

            <span class="tour-reference-breadcrumb__arrow">›</span>
            <strong>{{ $tour->name }}</strong>
        </div>
    </div>


    {{-- TOUR TITLE / META --}}
    <section class="tour-reference-heading">
        <div class="container">

            <div class="tour-reference-heading__top">

                <div class="tour-reference-location">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>
                        {{ $tour->destination ?: $tour->starting_city ?: 'India' }}
                    </span>
                </div>

                <div class="tour-reference-actions">
                    <button type="button" class="tour-reference-action">
                        <i class="fa-solid fa-share-nodes"></i>
                        Share
                    </button>

                    <button
                        type="button"
                        class="tour-reference-action"
                        aria-label="Add to wishlist"
                    >
                        Add to Wishlist
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

            </div>


            <h1 class="tour-reference-title">
                {{ $tour->name }}
            </h1>


            <div class="tour-reference-facts">

                <div class="tour-reference-fact">
                    <div class="tour-reference-fact__icon">
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <div>
                        <span>Duration</span>
                        <strong>
                            {{ $tour->duration_days }}
                            {{ Str::plural('Day', $tour->duration_days) }}
                            @if($tour->duration_nights)
                                /
                                {{ $tour->duration_nights }}
                                {{ Str::plural('Night', $tour->duration_nights) }}
                            @endif
                        </strong>
                    </div>
                </div>


                <div class="tour-reference-fact">
                    <div class="tour-reference-fact__icon">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <span>Age</span>
                        <strong>
                            @if($tour->age_min !== null && $tour->age_max !== null)
                                {{ $tour->age_min }}–{{ $tour->age_max }}
                            @elseif($tour->age_min !== null)
                                {{ $tour->age_min }}+
                            @elseif($tour->age_max !== null)
                                Up to {{ $tour->age_max }}
                            @else
                                All ages
                            @endif
                        </strong>
                    </div>
                </div>


                <div class="tour-reference-fact">
                    <div class="tour-reference-fact__icon">
                        <i class="fa-solid fa-compass"></i>
                    </div>

                    <div>
                        <span>Tour Type</span>
                        <strong>
                            {{ $tour->tour_type
                                ? ucfirst($tour->tour_type)
                                : 'Guided Tour' }}
                        </strong>
                    </div>
                </div>


                @if($tour->best_time)
                    <div class="tour-reference-fact">
                        <div class="tour-reference-fact__icon">
                            <i class="fa-regular fa-sun"></i>
                        </div>

                        <div>
                            <span>Best Time</span>
                            <strong>{{ $tour->best_time }}</strong>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </section>


    {{-- IMAGE GALLERY --}}
    <section class="tour-reference-gallery-section">
        <div class="container">

            <div class="tour-reference-gallery">

                <a
                    href="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                    class="tour-reference-gallery__main"
                    data-gallery-index="0"
                >
                    <img
                        src="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                        alt="{{ $tour->name }}"
                    >

                    <div class="tour-reference-gallery__main-overlay"></div>

                    <div class="tour-reference-gallery__buttons">
                        <span>
                            <i class="fa-regular fa-images"></i>
                            All Gallery
                        </span>

                        <span>
                            <i class="fa-solid fa-video"></i>
                            Play Video
                        </span>
                    </div>
                </a>


                @foreach($tour->images->take(4) as $image)

                    <a
                        href="{{ $image->image_url }}"
                        class="tour-reference-gallery__item"
                        data-gallery-index="{{ $loop->index + 1 }}"
                    >
                        <img
                            src="{{ $image->image_url }}"
                            alt="{{ $image->alt_text ?: $tour->name }}"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        >

                        @if(
                            $loop->last &&
                            $tour->images->count() > 4
                        )
                            <span class="tour-reference-gallery__more">
                                +{{ $tour->images->count() - 4 }} Photos
                            </span>
                        @endif
                    </a>

                @endforeach

            </div>

        </div>
    </section>


    {{-- MAIN DETAIL AREA --}}
    <section class="tour-reference-body">

        <div class="container tour-reference-layout">

            {{-- LEFT --}}
            <main class="tour-reference-content">

                {{-- OVERVIEW --}}
                <section
                    class="tour-reference-section"
                    id="overview"
                >
                    <h2>Overview</h2>

                    @if($tour->short_description)
                        <p class="tour-reference-lead">
                            {{ $tour->short_description }}
                        </p>
                    @endif

                    @if($tour->description)
                        <div class="tour-reference-copy">
                            {!! nl2br(e($tour->description)) !!}
                        </div>
                    @endif
                </section>


                {{-- TRIP HIGHLIGHTS --}}
                @if(!empty($tour->highlights))
                    <section
                        class="tour-reference-section"
                        id="highlights"
                    >
                        <h2>Trip Highlights</h2>

                        <ul class="tour-reference-check-list">

                            @foreach($tour->highlights as $highlight)
                                <li>
                                    <span>
                                        <i class="fa-solid fa-check"></i>
                                    </span>

                                    {{ $highlight }}
                                </li>
                            @endforeach

                        </ul>
                    </section>
                @endif


                {{-- INCLUDED / EXCLUDED --}}
                @if(
                    !empty($tour->included_items) ||
                    !empty($tour->excluded_items)
                )
                    <section
                        class="tour-reference-section"
                        id="includes"
                    >
                        <h2>What's Included?</h2>

                        <div class="tour-reference-inclusion-card">

                            @if(!empty($tour->included_items))
                                <div class="tour-reference-inclusion-column">

                                    <h3>
                                        Included In Your Trip Cost
                                    </h3>

                                    <ul class="tour-reference-check-list">

                                        @foreach($tour->included_items as $item)
                                            <li>
                                                <span>
                                                    <i class="fa-solid fa-check"></i>
                                                </span>

                                                {{ $item }}
                                            </li>
                                        @endforeach

                                    </ul>

                                </div>
                            @endif


                            @if(!empty($tour->excluded_items))
                                <div class="tour-reference-inclusion-column">

                                    <h3>
                                        Not Included In Your Trip Cost
                                    </h3>

                                    <ul class="tour-reference-check-list">

                                        @foreach($tour->excluded_items as $item)
                                            <li>
                                                <span>
                                                    <i class="fa-solid fa-check"></i>
                                                </span>

                                                {{ $item }}
                                            </li>
                                        @endforeach

                                    </ul>

                                </div>
                            @endif

                        </div>

                    </section>
                @endif

            </main>


            {{-- RIGHT BOOKING CARD --}}
            <aside class="tour-reference-booking">

                <div class="tour-reference-booking__inner">

                    <h2>Book This Tour</h2>


                    @if($departures->isNotEmpty())

                        <div class="tour-reference-booking__field">
                            <div>
                                <i class="fa-regular fa-calendar"></i>
                                <span>Available Dates</span>
                            </div>

                            <i class="fa-solid fa-chevron-down"></i>
                        </div>


                        <div class="tour-reference-booking__rows">

                            @foreach($departures as $departure)

                                <div class="tour-reference-departure">

                                    <div class="tour-reference-departure__date">
                                        <strong>
                                            {{ $departure->departure_date->format('d') }}
                                        </strong>

                                        <span>
                                            {{ $departure->departure_date->format('M Y') }}
                                        </span>
                                    </div>


                                    <div class="tour-reference-departure__info">
                                        <strong>
                                            {{ $departure->departure_date->format('D') }}
                                        </strong>

                                        <span>
                                            {{ $departure->available_seats }}
                                            {{ Str::plural('seat', $departure->available_seats) }}
                                            left
                                        </span>
                                    </div>


                                    <div class="tour-reference-departure__price">
                                        <strong>
                                            ₹{{ number_format(
                                                (float) $departure->effective_price,
                                                0
                                            ) }}
                                        </strong>

                                        <a
                                            href="{{ route('bookings.create', [
                                                'tour' => $tour,
                                                'departure' => $departure->id,
                                            ]) }}"
                                        >
                                            Book
                                        </a>
                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="tour-reference-booking__empty">
                            <i class="fa-regular fa-calendar-xmark"></i>

                            <p>
                                No bookable departures are available right now.
                            </p>

                            <a href="{{ route('contact') }}">
                                Contact Us
                            </a>
                        </div>

                    @endif


                    <div class="tour-reference-booking__divider"></div>


                    <div class="tour-reference-booking__note">
                        <i class="fa-solid fa-shield-heart"></i>

                        <span>
                            Secure booking. Select a departure date to continue.
                        </span>
                    </div>

                </div>

            </aside>

        </div>

    </section>

</section>


{{-- =========================================================
   LIGHTWEIGHT GALLERY VIEWER
   ========================================================= --}}

<div
    class="tour-lightbox"
    id="tourLightbox"
    aria-hidden="true"
>
    <button
        type="button"
        class="tour-lightbox__close"
        id="tourLightboxClose"
        aria-label="Close gallery"
    >
        ×
    </button>

    <button
        type="button"
        class="tour-lightbox__arrow tour-lightbox__arrow--prev"
        id="tourLightboxPrev"
        aria-label="Previous image"
    >
        ‹
    </button>

    <img
        id="tourLightboxImage"
        src=""
        alt=""
    >

    <button
        type="button"
        class="tour-lightbox__arrow tour-lightbox__arrow--next"
        id="tourLightboxNext"
        aria-label="Next image"
    >
        ›
    </button>
</div>

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const galleryItems = Array.from(
        document.querySelectorAll('.tour-gallery-hero__main, .tour-gallery-hero__item')
    );

    const lightbox = document.getElementById('tourLightbox');
    const lightboxImage = document.getElementById('tourLightboxImage');
    const closeButton = document.getElementById('tourLightboxClose');
    const previousButton = document.getElementById('tourLightboxPrev');
    const nextButton = document.getElementById('tourLightboxNext');

    if (!galleryItems.length || !lightbox) {
        return;
    }

    const images = galleryItems.map(function (item) {
        return {
            src: item.getAttribute('href'),
            alt: item.querySelector('img')?.getAttribute('alt') || ''
        };
    });

    let currentIndex = 0;

    function showImage(index) {
        if (index < 0) {
            index = images.length - 1;
        }

        if (index >= images.length) {
            index = 0;
        }

        currentIndex = index;

        lightboxImage.src = images[index].src;
        lightboxImage.alt = images[index].alt;

        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');

        document.body.classList.add('tour-lightbox-open');
    }

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('tour-lightbox-open');

        lightboxImage.src = '';
    }

    galleryItems.forEach(function (item, index) {
        item.addEventListener('click', function (event) {
            event.preventDefault();

            showImage(index);
        });
    });

    closeButton.addEventListener('click', closeLightbox);

    previousButton.addEventListener('click', function () {
        showImage(currentIndex - 1);
    });

    nextButton.addEventListener('click', function () {
        showImage(currentIndex + 1);
    });

    document.addEventListener('keydown', function (event) {
        if (!lightbox.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowLeft') {
            showImage(currentIndex - 1);
        }

        if (event.key === 'ArrowRight') {
            showImage(currentIndex + 1);
        }
    });
});
</script>

@endpush