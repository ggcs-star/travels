<section class="blog-section">
    <div class="container">

        {{-- Section Header --}}
        <div class="section-header blog-header">

            <div>
                <span class="section-eyebrow">
                    Travel inspiration
                </span>

                <h2 class="section-title">
                    Stories from the
                    <span>road.</span>
                </h2>

                <p class="section-description">
                    Get inspired with travel guides, destination ideas,
                    practical tips and stories from around the world.
                </p>
            </div>

            <a
                href="{{ url('/blog') }}"
                class="section-view-all"
            >
                View all stories
                <span aria-hidden="true">→</span>
            </a>

        </div>


        {{-- Blog Grid --}}
        <div class="blog-grid">

            {{-- Featured Post --}}
            <article class="blog-card blog-card-featured">

                <a
                    href="{{ url('/blog/best-places-to-visit-kashmir') }}"
                    class="blog-card-image"
                >
                    <img
                        src="{{ asset('images/blog/kashmir-guide.webp') }}"
                        alt="Beautiful Kashmir landscape"
                        loading="lazy"
                    >

                    <span class="blog-card-category">
                        Destination Guide
                    </span>
                </a>

                <div class="blog-card-body">

                    <div class="blog-card-meta">
                        <span>
                            12 Aug 2026
                        </span>

                        <span>
                            6 min read
                        </span>
                    </div>

                    <h3>
                        <a href="{{ url('/blog/best-places-to-visit-kashmir') }}">
                            The ultimate guide to exploring Kashmir
                        </a>
                    </h3>

                    <p>
                        Discover the best places to visit, experiences to
                        try and things to know before planning your Kashmir
                        adventure.
                    </p>

                    <a
                        href="{{ url('/blog/best-places-to-visit-kashmir') }}"
                        class="blog-card-link"
                    >
                        Read story
                        <span aria-hidden="true">→</span>
                    </a>

                </div>

            </article>


            {{-- Post 2 --}}
            <article class="blog-card">

                <a
                    href="{{ url('/blog/goa-travel-guide') }}"
                    class="blog-card-image"
                >
                    <img
                        src="{{ asset('images/blog/goa-guide.webp') }}"
                        alt="Goa beach travel guide"
                        loading="lazy"
                    >

                    <span class="blog-card-category">
                        Travel Guide
                    </span>
                </a>

                <div class="blog-card-body">

                    <div class="blog-card-meta">
                        <span>
                            08 Aug 2026
                        </span>

                        <span>
                            5 min read
                        </span>
                    </div>

                    <h3>
                        <a href="{{ url('/blog/goa-travel-guide') }}">
                            A complete guide to planning your Goa getaway
                        </a>
                    </h3>

                    <a
                        href="{{ url('/blog/goa-travel-guide') }}"
                        class="blog-card-link"
                    >
                        Read story
                        <span aria-hidden="true">→</span>
                    </a>

                </div>

            </article>


            {{-- Post 3 --}}
            <article class="blog-card">

                <a
                    href="{{ url('/blog/how-to-plan-perfect-trip') }}"
                    class="blog-card-image"
                >
                    <img
                        src="{{ asset('images/blog/travel-planning.webp') }}"
                        alt="Traveler planning a trip"
                        loading="lazy"
                    >

                    <span class="blog-card-category">
                        Travel Tips
                    </span>
                </a>

                <div class="blog-card-body">

                    <div class="blog-card-meta">
                        <span>
                            03 Aug 2026
                        </span>

                        <span>
                            4 min read
                        </span>
                    </div>

                    <h3>
                        <a href="{{ url('/blog/how-to-plan-perfect-trip') }}">
                            How to plan the perfect trip without the stress
                        </a>
                    </h3>

                    <a
                        href="{{ url('/blog/how-to-plan-perfect-trip') }}"
                        class="blog-card-link"
                    >
                        Read story
                        <span aria-hidden="true">→</span>
                    </a>

                </div>

            </article>

        </div>

    </div>
</section>