{{-- Section will be implemented in the UI phase. --}}
<section class="hero-section">

    <div class="hero-background">
        <img
            src="{{ asset('images/hero/travel-hero.webp') }}"
            alt="Beautiful travel destination"
            class="hero-background-image"
            fetchpriority="high"
        >
    </div>

    <div class="hero-overlay"></div>

    <div class="container hero-container">

        <div class="hero-content">

            <span class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                Discover the world with us
            </span>

            <h1 class="hero-title">
                Your next
                <span>great journey</span>
                starts here.
            </h1>

            <p class="hero-description">
                Explore breathtaking destinations, discover unforgettable
                experiences and travel with confidence. Your perfect journey
                is just a few clicks away.
            </p>

            <div class="hero-actions">

                <a
                    href="{{ route('tours.index') }}"
                    class="btn btn-primary hero-primary-btn"
                >
                    Explore Tours
                    <span aria-hidden="true">→</span>
                </a>

                <a
                    href="{{ url('/contact') }}"
                    class="btn btn-outline-light"
                >
                    Plan My Trip
                </a>

            </div>

            <div class="hero-trust">

                <div class="hero-trust-avatars">

                    <span class="hero-avatar">
                        <img
                            src="{{ asset('images/avatars/avatar-1.webp') }}"
                            alt=""
                            loading="lazy"
                        >
                    </span>

                    <span class="hero-avatar">
                        <img
                            src="{{ asset('images/avatars/avatar-2.webp') }}"
                            alt=""
                            loading="lazy"
                        >
                    </span>

                    <span class="hero-avatar">
                        <img
                            src="{{ asset('images/avatars/avatar-3.webp') }}"
                            alt=""
                            loading="lazy"
                        >
                    </span>

                    <span class="hero-avatar hero-avatar-more">
                        +2k
                    </span>

                </div>

                <div class="hero-trust-content">

                    <div class="hero-stars" aria-label="5 out of 5 stars">
                        ★★★★★
                    </div>

                    <p>
                        Trusted by <strong>2,000+</strong> travelers
                    </p>

                </div>

            </div>

        </div>

        {{-- Floating destination card --}}
        <div class="hero-floating-card">

            <div class="hero-floating-icon">
                ✦
            </div>

            <div>
                <span>Popular right now</span>
                <strong>Explore Kashmir</strong>
            </div>

            <a
                href="{{ route('tours.index') }}"
                aria-label="Explore Kashmir tours"
            >
                →
            </a>

        </div>

    </div>

    {{-- Scroll indicator --}}
    <div class="hero-scroll-indicator">

        <span class="hero-scroll-line"></span>

        <span>
            Scroll to explore
        </span>

    </div>

</section>