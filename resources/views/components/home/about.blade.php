<section class="about-travelz-section">

    <div class="container about-travelz-container">

        {{-- =====================================================
             LEFT IMAGE
        ====================================================== --}}

        <div class="about-travelz-visual">

            <div class="about-travelz-image-wrap">

                <img
                    src="{{ asset('images/hero/about-us.png') }}"
                    alt="Travellers taking in a mountain sunset together"
                    class="about-travelz-image"
                    loading="lazy"
                    decoding="async"
                >

                {{-- Diamond Jubilee Badge --}}
                <div class="about-jubilee-badge">

                    <span class="about-jubilee-icon">
                        ♛
                    </span>

                    <span>
                        Diamond Jubilee Company
                    </span>

                </div>

            </div>


            {{-- 75 Years Badge --}}
            <div class="about-years-badge">

                <strong>75<sup>+</sup></strong>

                <span>
                    YEARS OF<br>
                    EXCELLENCE
                </span>

            </div>

        </div>


        {{-- =====================================================
             RIGHT CONTENT
        ====================================================== --}}

        <div class="about-travelz-content">

            <span class="about-eyebrow">

                <span class="about-eyebrow-dot"></span>

                OUR STORY

            </span>


            <h2 class="about-travelz-title">

                Built On Trust,

                <span>
                    Carried By Experience
                </span>

            </h2>


            <p class="about-lead">

                {{ config('travels.brand.name') }} has been planning
                journeys out of
                <strong>Hyderabad, Vijayawada &amp; Visakhapatnam</strong>
                for more than seventy years, looking after everyone from
                corporate travellers to families on their first big trip.

            </p>


            <p>

                It all started in 1950, when our founder,
                the late Veeramallu Venkaiah Garu, set out with a simple
                goal: make pilgrimage travel safe and dependable for
                ordinary families. The path wasn't easy, but his
                insistence on doing right by every traveller earned a
                reputation that spread by word of mouth and still
                carries our name today.

            </p>


            <p>

                As travel changed through the 1990s and beyond, we
                changed with it — adding new destinations and modern
                booking conveniences without losing the personal
                attention that has always set us apart.

            </p>


            <p>

                These days our itineraries cover everything from
                temple pilgrimages and family holidays to school trips
                and corporate offsites, plus a set of monthly fixed
                departures for travellers who'd rather book a seat
                than plan a route from scratch.

            </p>


            {{-- =====================================================
                 FEATURES
            ====================================================== --}}

            <div class="about-features">

                <span class="about-feature">
                    <span>◉</span>
                    Offices in 3 Cities
                </span>

                <span class="about-feature">
                    <span>♟</span>
                    Corporate &amp; Leisure Travel
                </span>

                <span class="about-feature">
                    <span>♜</span>
                    Pilgrimage Tour Experts
                </span>

                <span class="about-feature">
                    <span>✈</span>
                    India &amp; Worldwide
                </span>

            </div>


            {{-- =====================================================
                 CTA
            ====================================================== --}}

            <a
                href="{{ route('about') }}"
                class="about-read-button"
            >

                <span>
                    Read Our Full Story
                </span>

                <span>
                    →
                </span>

            </a>

        </div>

    </div>

</section>