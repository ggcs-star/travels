<section class="destinations-section">
    <div class="container">

        {{-- Section Header --}}
        <div class="section-header destinations-header">

            <div>
                <span class="section-eyebrow">
                    Explore the world
                </span>

                <h2 class="section-title">
                    Popular <span>destinations</span>
                </h2>

                <p class="section-description">
                    From peaceful mountain escapes to vibrant coastal
                    getaways, find a destination that matches your mood.
                </p>
            </div>

            <a
                href="{{ url('/destinations') }}"
                class="section-view-all"
            >
                Explore destinations
                <span aria-hidden="true">→</span>
            </a>

        </div>


        {{-- Destinations Grid --}}
        <div class="destinations-grid">

            {{-- Large Destination --}}
            <a
                href="{{ url('/destinations/kashmir') }}"
                class="destination-card destination-card-large"
            >

                <img
                    src="{{ asset('images/destinations/kashmir.webp') }}"
                    alt="Kashmir mountains and Dal Lake"
                    loading="lazy"
                >

                <div class="destination-card-overlay"></div>

                <div class="destination-card-content">

                    <span class="destination-card-country">
                        India
                    </span>

                    <h3>
                        Kashmir
                    </h3>

                    <span class="destination-card-link">
                        Explore
                        <span aria-hidden="true">→</span>
                    </span>

                </div>

            </a>


            {{-- Goa --}}
            <a
                href="{{ url('/destinations/goa') }}"
                class="destination-card"
            >

                <img
                    src="{{ asset('images/destinations/goa.webp') }}"
                    alt="Goa beach"
                    loading="lazy"
                >

                <div class="destination-card-overlay"></div>

                <div class="destination-card-content">

                    <span class="destination-card-country">
                        India
                    </span>

                    <h3>
                        Goa
                    </h3>

                    <span class="destination-card-link">
                        Explore
                        <span aria-hidden="true">→</span>
                    </span>

                </div>

            </a>


            {{-- Dubai --}}
            <a
                href="{{ url('/destinations/dubai') }}"
                class="destination-card"
            >

                <img
                    src="{{ asset('images/destinations/dubai.webp') }}"
                    alt="Dubai skyline"
                    loading="lazy"
                >

                <div class="destination-card-overlay"></div>

                <div class="destination-card-content">

                    <span class="destination-card-country">
                        UAE
                    </span>

                    <h3>
                        Dubai
                    </h3>

                    <span class="destination-card-link">
                        Explore
                        <span aria-hidden="true">→</span>
                    </span>

                </div>

            </a>


            {{-- Manali --}}
            <a
                href="{{ url('/destinations/manali') }}"
                class="destination-card"
            >

                <img
                    src="{{ asset('images/destinations/manali.webp') }}"
                    alt="Manali mountain landscape"
                    loading="lazy"
                >

                <div class="destination-card-overlay"></div>

                <div class="destination-card-content">

                    <span class="destination-card-country">
                        India
                    </span>

                    <h3>
                        Manali
                    </h3>

                    <span class="destination-card-link">
                        Explore
                        <span aria-hidden="true">→</span>
                    </span>

                </div>

            </a>


            {{-- Rajasthan --}}
            <a
                href="{{ url('/destinations/rajasthan') }}"
                class="destination-card"
            >

                <img
                    src="{{ asset('images/destinations/rajasthan.webp') }}"
                    alt="Rajasthan palace architecture"
                    loading="lazy"
                >

                <div class="destination-card-overlay"></div>

                <div class="destination-card-content">

                    <span class="destination-card-country">
                        India
                    </span>

                    <h3>
                        Rajasthan
                    </h3>

                    <span class="destination-card-link">
                        Explore
                        <span aria-hidden="true">→</span>
                    </span>

                </div>

            </a>

        </div>

    </div>
</section>