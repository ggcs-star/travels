@extends('layouts.app')

@section('title')
    @if($selectedCategory)
        {{ $selectedCategory->name }} Tours | {{ config('travels.brand.name') }}
    @else
        Tours | {{ config('travels.brand.name') }}
    @endif
@endsection


@section('content')

    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section
        class="storefront-hero tours-index-hero"
        @if($selectedCategory?->image)
            style="--tour-hero-image: url('{{ asset('storage/' . $selectedCategory->image) }}');"
        @endif
    >

        <div class="tours-index-hero__overlay"></div>

        <div class="container tours-index-hero__content">

            @if($selectedCategory)

                <span class="storefront-kicker">
                    {{ $selectedCategory->name }}
                </span>

                <h1>
                    Explore {{ $selectedCategory->name }}
                </h1>

                <p>
                    Discover carefully planned
                    {{ strtolower($selectedCategory->name) }}
                    tours, memorable experiences and upcoming departures.
                </p>

            @else

                <span class="storefront-kicker">
                    Find your next journey
                </span>

                <h1>
                    Travel experiences made for memorable moments.
                </h1>

                <p>
                    Choose from thoughtfully planned tours,
                    transparent pricing, and departures you can book online.
                </p>

            @endif

        </div>

    </section>


    {{-- =========================================================
         TOURS SECTION
         ========================================================= --}}

    <section class="storefront-section">

        <div class="container">


            {{-- =====================================================
                 SEARCH
                 ===================================================== --}}

            <form
                method="GET"
                action="{{ route('tours.index') }}"
                class="tour-search-card"
            >

                <div class="tour-search-field">

                    <label for="search">
                        Search tours
                    </label>

                    <input
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Destination or tour name"
                    >

                </div>


                <div class="tour-search-field">

                    <label for="category">
                        Category
                    </label>

                    <select
                        id="category"
                        name="category"
                    >

                        <option value="">
                            All categories
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->slug }}"
                                @selected(request('category') === $category->slug)
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <button
                    type="submit"
                    class="storefront-button"
                >
                    Find tours
                </button>

            </form>


            {{-- =====================================================
                 ALERTS
                 ===================================================== --}}

            @if(session('success'))

                <div class="storefront-alert storefront-alert--success">
                    {{ session('success') }}
                </div>

            @endif


            @if(session('error'))

                <div class="storefront-alert storefront-alert--error">
                    {{ session('error') }}
                </div>

            @endif


            {{-- =====================================================
                 HEADING
                 ===================================================== --}}

            <div class="storefront-heading-row">

                <div>

                    <span class="storefront-kicker">

                        @if($selectedCategory)
                            {{ $selectedCategory->name }} tours
                        @else
                            Available trips
                        @endif

                    </span>


                    <h2>
                        {{ $tours->total() }}

                        {{ Str::plural('tour', $tours->total()) }}

                        to explore
                    </h2>

                </div>

            </div>


            {{-- =====================================================
                 TOUR GRID
                 ===================================================== --}}

            @if($tours->isNotEmpty())

                <div class="tour-grid">

                    @foreach($tours as $tour)

                        @php
                            $departure = $tour->departures->first();
                        @endphp


                        <article class="tour-card">


                            {{-- IMAGE --}}

                            <a
                                href="{{ route('tours.show', $tour) }}"
                                class="tour-card__image-link"
                            >

                                <img
                                    src="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                                    alt="{{ $tour->name }}"
                                    class="tour-card__image"
                                    loading="lazy"
                                >

                            </a>


                            {{-- BODY --}}

                            <div class="tour-card__body">


                                {{-- META --}}

                                <div class="tour-card__meta">

                                    <span>
                                        {{ $tour->category?->name ?: 'Curated travel' }}
                                    </span>

                                    <span>
                                        {{ $tour->duration_days }}D /
                                        {{ $tour->duration_nights }}N
                                    </span>

                                </div>


                                {{-- TITLE --}}

                                <h3>

                                    <a
                                        href="{{ route('tours.show', $tour) }}"
                                    >
                                        {{ $tour->name }}
                                    </a>

                                </h3>


                                {{-- DESTINATION --}}

                                <p class="tour-card__destination">
                                    {{ $tour->destination ?: $tour->starting_city ?: 'India' }}
                                </p>


                                {{-- DESCRIPTION --}}

                                <p>
                                    {{ Str::limit(
                                        $tour->short_description ?: $tour->description,
                                        120
                                    ) }}
                                </p>


                                {{-- FOOTER --}}

                                <div class="tour-card__footer">

                                    <div>

                                        @if($departure)

                                            <span class="storefront-muted">
                                                From
                                            </span>

                                            <strong>
                                                ₹{{ number_format(
                                                    (float) $departure->effective_price,
                                                    0
                                                ) }}
                                            </strong>

                                            <small>
                                                per traveller
                                            </small>

                                        @else

                                            <span class="storefront-muted">
                                                New dates coming soon
                                            </span>

                                        @endif

                                    </div>


                                    <a
                                        href="{{ route('tours.show', $tour) }}"
                                        class="storefront-link"
                                    >
                                        View tour →
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


                {{-- =================================================
                     PAGINATION
                     ================================================= --}}

                <div class="storefront-pagination">

                    {{ $tours->links() }}

                </div>


            @else

                {{-- =================================================
                     EMPTY
                     ================================================= --}}

                <div class="storefront-empty">

                    <h2>
                        No tours matched those filters.
                    </h2>

                    <p>
                        Try another destination or view every available tour.
                    </p>

                    <a
                        href="{{ route('tours.index') }}"
                        class="storefront-button"
                    >
                        View all tours
                    </a>

                </div>

            @endif

        </div>

    </section>

@endsection