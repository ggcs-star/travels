@extends('layouts.app')

@section('title', $tour->name.' | '.config('travels.brand.name'))

@section(
    'meta_description',
    $tour->short_description
        ?: Str::limit(strip_tags($tour->description), 155)
)

@section('content')

    {{-- =====================================================
        TOUR GALLERY HERO
    ====================================================== --}}

    <section class="tour-gallery-hero">

        <div class="tour-gallery-hero__grid">

            {{-- MAIN COVER IMAGE --}}
            <a
                href="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                class="tour-gallery-hero__main"
                target="_blank"
            >
                <img
                    src="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                    alt="{{ $tour->name }}"
                >

                <span class="tour-gallery-hero__main-overlay"></span>
            </a>


            {{-- GALLERY IMAGES --}}
            @foreach($tour->images->take(4) as $image)

                @php
                    $imagePath = $image->image_path
                        ?? $image->path
                        ?? $image->image
                        ?? null;

                    if ($imagePath && \Illuminate\Support\Str::startsWith(
                        $imagePath,
                        ['http://', 'https://', '//']
                    )) {
                        $imageUrl = $imagePath;
                    } else {
                        $imageUrl = $imagePath
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath)
                            : null;
                    }
                @endphp

                @if($imageUrl)

                    <a
                        href="{{ $imageUrl }}"
                        class="tour-gallery-hero__item"
                        target="_blank"
                    >
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $tour->name }} gallery image"
                            loading="lazy"
                        >
                    </a>

                @endif

            @endforeach


            {{-- MORE COUNT --}}
            @if($tour->images->count() > 4)

                <div class="tour-gallery-hero__more">
                    +{{ $tour->images->count() - 4 }} photos
                </div>

            @endif

        </div>


        {{-- HERO CONTENT --}}
        <div class="tour-gallery-hero__content">

            <div class="container">

                <div class="tour-gallery-hero__breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>/</span>
                    <a href="{{ route('tours.index') }}">Tours</a>
                    <span>/</span>
                    <span>{{ $tour->tour_type ?: 'Tour' }}</span>
                </div>


                <h1>
                    {{ $tour->name }}
                </h1>


                <div class="tour-gallery-hero__meta">

                    <span>
                        📅
                        {{ $tour->duration_days }}
                        Days /
                        {{ $tour->duration_nights }}
                        Nights
                    </span>

                    @if($tour->destination)
                        <span>
                            📍 {{ $tour->destination }}
                        </span>
                    @endif

                    @if($tour->tour_type)
                        <span>
                            ✦ {{ $tour->tour_type }}
                        </span>
                    @endif

                    @if($tour->starting_city)
                        <span>
                            ⇢ {{ $tour->starting_city }}
                        </span>
                    @endif

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
        TOUR NAVIGATION
    ====================================================== --}}

    <nav class="tour-detail-nav">

        <div class="container tour-detail-nav__inner">

            <a href="#overview" class="is-active">
                Overview
            </a>

            @if(!empty($tour->highlights))
                <a href="#highlights">
                    Highlights
                </a>
            @endif

            @if(
                !empty($tour->included_items)
                || !empty($tour->excluded_items)
            )
                <a href="#includes">
                    Includes & Excludes
                </a>
            @endif

            <a href="#departures">
                Departures
            </a>

        </div>

    </nav>


    {{-- =====================================================
        CONTENT
    ====================================================== --}}

    <section class="storefront-section">

        <div class="container tour-detail-layout">

            <main class="tour-detail-content">

                {{-- OVERVIEW --}}
                <section
                    class="tour-detail-block"
                    id="overview"
                >

                    <span class="storefront-kicker">
                        Overview
                    </span>

                    <h2>
                        About This Tour
                    </h2>

                    @if($tour->short_description)

                        <p class="tour-detail-lead">
                            {{ $tour->short_description }}
                        </p>

                    @endif

                    @if($tour->description)

                        <div class="tour-detail-copy">
                            {!! nl2br(e($tour->description)) !!}
                        </div>

                    @endif

                </section>


                {{-- FACTS --}}
                <section class="tour-facts">

                    <div>
                        <span>Duration</span>

                        <strong>
                            {{ $tour->duration_days }}
                            Days /
                            {{ $tour->duration_nights }}
                            Nights
                        </strong>
                    </div>

                    <div>
                        <span>Tour Type</span>

                        <strong>
                            {{ $tour->tour_type
                                ? ucfirst($tour->tour_type)
                                : 'Guided Group Tour' }}
                        </strong>
                    </div>

                    <div>
                        <span>Destination</span>

                        <strong>
                            {{ $tour->destination ?: 'India' }}
                        </strong>
                    </div>

                    <div>
                        <span>Best Time</span>

                        <strong>
                            {{ $tour->best_time ?: 'Contact us for guidance' }}
                        </strong>
                    </div>

                </section>


                {{-- HIGHLIGHTS --}}
                @if(!empty($tour->highlights))

                    <section
                        class="tour-highlights"
                        id="highlights"
                    >

                        <span class="storefront-kicker">
                            Experience
                        </span>

                        <h2>
                            Tour Highlights
                        </h2>

                        <ul>

                            @foreach($tour->highlights as $highlight)

                                <li>
                                    <span>✓</span>

                                    <div>
                                        {{ $highlight }}
                                    </div>
                                </li>

                            @endforeach

                        </ul>

                    </section>

                @endif


                {{-- INCLUDES / EXCLUDES --}}
                @if(
                    !empty($tour->included_items)
                    || !empty($tour->excluded_items)
                )

                    <section
                        class="tour-inclusions"
                        id="includes"
                    >

                        @if(!empty($tour->included_items))

                            <div>

                                <span class="storefront-kicker">
                                    Included
                                </span>

                                <h2>
                                    What's Included
                                </h2>

                                <ul>

                                    @foreach(
                                        $tour->included_items
                                        as $item
                                    )

                                        <li>

                                            <span
                                                class="tour-inclusion-icon tour-inclusion-icon--included"
                                            >
                                                ✓
                                            </span>

                                            {{ $item }}

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        @if(!empty($tour->excluded_items))

                            <div>

                                <span class="storefront-kicker">
                                    Excluded
                                </span>

                                <h2>
                                    Not Included
                                </h2>

                                <ul>

                                    @foreach(
                                        $tour->excluded_items
                                        as $item
                                    )

                                        <li>

                                            <span
                                                class="tour-inclusion-icon tour-inclusion-icon--excluded"
                                            >
                                                —
                                            </span>

                                            {{ $item }}

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif

                    </section>

                @endif

            </main>


            {{-- =================================================
                DEPARTURES
            ================================================== --}}

            <aside
                class="departure-panel"
                id="departures"
            >

                <span class="storefront-kicker">
                    Book Online
                </span>

                <h2>
                    Choose a Departure
                </h2>

                <p class="departure-panel__intro">
                    Select an available departure date to continue
                    with your booking.
                </p>


                @forelse($departures as $departure)

                    <div class="departure-option">

                        <div class="departure-option__details">

                            <strong>
                                {{ $departure->departure_date->format('D, d M Y') }}
                            </strong>

                            <span>
                                to
                                {{ $departure->return_date->format('D, d M Y') }}
                            </span>

                            <small>
                                {{ $departure->available_seats }}
                                {{ Str::plural(
                                    'seat',
                                    $departure->available_seats
                                ) }}
                                left
                            </small>

                        </div>


                        <div class="departure-option__action">

                            <strong>
                                ₹{{ number_format(
                                    (float) $departure->effective_price,
                                    0
                                ) }}
                            </strong>

                            <a
                                href="{{ route(
                                    'bookings.create',
                                    [
                                        'tour' => $tour,
                                        'departure' => $departure->id
                                    ]
                                ) }}"
                                class="storefront-button storefront-button--small"
                            >
                                Book
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="departure-empty">

                        <p class="storefront-muted">
                            There are no bookable departures
                            at the moment.
                        </p>

                        <a
                            href="{{ route('contact') }}"
                            class="storefront-button storefront-button--wide"
                        >
                            Contact Us
                        </a>

                    </div>

                @endforelse

            </aside>

        </div>

    </section>

@endsection