<section class="featured-tours-section">
    <div class="container">

        {{-- Section Header --}}
        <div class="section-header">

            <div>
                <span class="section-eyebrow">
                    Handpicked for you
                </span>

                <h2 class="section-title">
                    Explore our <span>featured journeys</span>
                </h2>
            </div>

            <a
                href="{{ route('tours.index') }}"
                class="section-view-all"
            >
                View all tours
                <span aria-hidden="true">→</span>
            </a>

        </div>


        {{-- Tour Grid --}}
        <div class="tour-grid">

            {{-- Tour Card 1 --}}
            <article class="tour-card">

                <a
                    href="{{ url('/tours/kashmir-escape') }}"
                    class="tour-card-image"
                >
                    <img
                        src="{{ asset('images/tours/kashmir.webp') }}"
                        alt="Kashmir mountains and lake"
                        loading="lazy"
                    >

                    <span class="tour-card-badge">
                        Popular
                    </span>

                    <button
                        type="button"
                        class="tour-card-wishlist"
                        aria-label="Add Kashmir Escape to wishlist"
                    >
                        ♡
                    </button>
                </a>

                <div class="tour-card-body">

                    <div class="tour-card-location">
                        <span aria-hidden="true">◎</span>
                        Kashmir, India
                    </div>

                    <h3 class="tour-card-title">
                        <a href="{{ url('/tours/kashmir-escape') }}">
                            Kashmir Escape
                        </a>
                    </h3>

                    <p class="tour-card-description">
                        Experience breathtaking valleys, peaceful lakes
                        and the beauty of the Himalayas.
                    </p>

                    <div class="tour-card-meta">

                        <span>
                            <span aria-hidden="true">◷</span>
                            6 Days / 5 Nights
                        </span>

                        <span>
                            <span aria-hidden="true">★</span>
                            4.9
                        </span>

                    </div>

                    <div class="tour-card-footer">

                        <div class="tour-card-price">
                            <small>Starting from</small>
                            <strong>₹24,999</strong>
                            <span>/ person</span>
                        </div>

                        <a
                            href="{{ url('/tours/kashmir-escape') }}"
                            class="tour-card-action"
                            aria-label="View Kashmir Escape"
                        >
                            →
                        </a>

                    </div>

                </div>

            </article>


            {{-- Tour Card 2 --}}
            <article class="tour-card">

                <a
                    href="{{ url('/tours/goa-beach-escape') }}"
                    class="tour-card-image"
                >
                    <img
                        src="{{ asset('images/tours/goa.webp') }}"
                        alt="Goa tropical beach"
                        loading="lazy"
                    >

                    <span class="tour-card-badge">
                        Best Seller
                    </span>

                    <button
                        type="button"
                        class="tour-card-wishlist"
                        aria-label="Add Goa Beach Escape to wishlist"
                    >
                        ♡
                    </button>
                </a>

                <div class="tour-card-body">

                    <div class="tour-card-location">
                        <span aria-hidden="true">◎</span>
                        Goa, India
                    </div>

                    <h3 class="tour-card-title">
                        <a href="{{ url('/tours/goa-beach-escape') }}">
                            Goa Beach Escape
                        </a>
                    </h3>

                    <p class="tour-card-description">
                        Relax on beautiful beaches, explore local culture
                        and enjoy an unforgettable coastal getaway.
                    </p>

                    <div class="tour-card-meta">

                        <span>
                            <span aria-hidden="true">◷</span>
                            4 Days / 3 Nights
                        </span>

                        <span>
                            <span aria-hidden="true">★</span>
                            4.8
                        </span>

                    </div>

                    <div class="tour-card-footer">

                        <div class="tour-card-price">
                            <small>Starting from</small>
                            <strong>₹14,999</strong>
                            <span>/ person</span>
                        </div>

                        <a
                            href="{{ url('/tours/goa-beach-escape') }}"
                            class="tour-card-action"
                            aria-label="View Goa Beach Escape"
                        >
                            →
                        </a>

                    </div>

                </div>

            </article>


            {{-- Tour Card 3 --}}
            <article class="tour-card">

                <a
                    href="{{ url('/tours/dubai-discovery') }}"
                    class="tour-card-image"
                >
                    <img
                        src="{{ asset('images/tours/dubai.webp') }}"
                        alt="Dubai skyline"
                        loading="lazy"
                    >

                    <span class="tour-card-badge">
                        International
                    </span>

                    <button
                        type="button"
                        class="tour-card-wishlist"
                        aria-label="Add Dubai Discovery to wishlist"
                    >
                        ♡
                    </button>
                </a>

                <div class="tour-card-body">

                    <div class="tour-card-location">
                        <span aria-hidden="true">◎</span>
                        Dubai, UAE
                    </div>

                    <h3 class="tour-card-title">
                        <a href="{{ url('/tours/dubai-discovery') }}">
                            Dubai Discovery
                        </a>
                    </h3>

                    <p class="tour-card-description">
                        Discover iconic landmarks, desert adventures
                        and the vibrant lifestyle of Dubai.
                    </p>

                    <div class="tour-card-meta">

                        <span>
                            <span aria-hidden="true">◷</span>
                            5 Days / 4 Nights
                        </span>

                        <span>
                            <span aria-hidden="true">★</span>
                            4.9
                        </span>

                    </div>

                    <div class="tour-card-footer">

                        <div class="tour-card-price">
                            <small>Starting from</small>
                            <strong>₹39,999</strong>
                            <span>/ person</span>
                        </div>

                        <a
                            href="{{ url('/tours/dubai-discovery') }}"
                            class="tour-card-action"
                            aria-label="View Dubai Discovery"
                        >
                            →
                        </a>

                    </div>

                </div>

            </article>

        </div>

    </div>
</section>