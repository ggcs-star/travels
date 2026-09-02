<section class="kanila-hero">

    {{-- Background --}}
    <div class="kanila-hero-background"></div>

    {{-- Decorative background glow --}}
    <div class="kanila-bg-glow kanila-bg-glow-left"></div>
    <div class="kanila-bg-glow kanila-bg-glow-right"></div>

    <div class="kanila-hero-inner">

        <div class="kanila-polaroid kanila-polaroid-one">
            <img
                src="{{ asset('images/hero/hero1.jpg') }}"
                alt="Travel"
                loading="lazy"
            >
        </div>

        <div class="kanila-polaroid kanila-polaroid-two">
            <img
                src="{{ asset('images/hero/h1-banner2.jpg') }}"
                alt="Travel destinations"
                loading="lazy"
            >
        </div>

        <img
            class="kanila-paper-plane kanila-paper-plane-left"
            src="{{ asset('images/hero/h1-paper-plane.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <div class="kanila-hero-copy">

            <div class="kanila-script">
                It's Time To
            </div>

            <h1 class="kanila-title">
                <span>TRAVEL</span>
                <span>EXPLORE</span>
            </h1>

            <div class="kanila-tagline">
                THE WORLD WITH US!
            </div>

            <a
                href="#tours"
                class="kanila-book-now"
            >
                <span>Book Now</span>
                <span class="kanila-book-icon">»→</span>
            </a>

        </div>

        <img
            class="kanila-airplane"
            src="{{ asset('images/hero/h1-airplane.png') }}"
            alt="Airplane"
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-heart kanila-heart-red"
            src="{{ asset('images/hero/h1-heart.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-heart kanila-heart-orange"
            src="{{ asset('images/hero/h1-heart-orange.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-heart kanila-heart-blue"
            src="{{ asset('images/hero/h1-heart-blue.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-cloud kanila-cloud-left"
            src="{{ asset('images/hero/h1-cloud.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-cloud kanila-cloud-right"
            src="{{ asset('images/hero/h1-cloud.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-paper-plane kanila-paper-plane-top"
            src="{{ asset('images/hero/h1-paper-plane.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <img
            class="kanila-paper-plane kanila-paper-plane-right"
            src="{{ asset('images/hero/h1-paper-plane.png') }}"
            alt=""
            loading="lazy"
            aria-hidden="true"
        >

        <svg
            class="kanila-flight-path"
            viewBox="0 0 900 500"
            preserveAspectRatio="none"
            aria-hidden="true"
        >
            <path
                d="
                    M30 300
                    C130 150 250 380 390 250
                    C510 140 590 80 720 100
                    C790 110 830 80 870 30
                "
            />
        </svg>

        <div
            class="kanila-slider"
            role="tablist"
            aria-label="Slider navigation"
        >

            <button
                class="is-active"
                type="button"
                role="tab"
                aria-label="Slide 1"
                aria-selected="true"
                data-slide="0"
            ></button>

            <button
                type="button"
                role="tab"
                aria-label="Slide 2"
                aria-selected="false"
                data-slide="1"
            ></button>

            <button
                type="button"
                role="tab"
                aria-label="Slide 3"
                aria-selected="false"
                data-slide="2"
            ></button>

        </div>

    </div>

</section>