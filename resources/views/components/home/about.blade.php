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

                {{-- Silver Jubilee Badge --}}
                <div class="about-jubilee-badge">

                    <span class="about-jubilee-icon">
                        ♛
                    </span>

                    <span>
                        Silver Jubilee Company
                    </span>

                </div>

            </div>


            {{-- 25 Years Badge --}}
            <div class="about-years-badge">

                <strong>25<sup>+</sup></strong>

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

                For over 25 years, {{ config('travels.brand.name') }}
                has been helping travellers turn their vacation ideas
                into memorable journeys. From peaceful pilgrimages and
                family getaways to corporate trips and international
                holidays, we make travel simple, comfortable, and
                thoughtfully planned.

            </p>


            <p>

                What began as a small travel service has grown into a
                complete travel partner for individuals, families,
                schools, and businesses. Our focus has always remained
                the same — reliable service, carefully planned
                itineraries, and experiences worth remembering.

            </p>


            <p>

                Today, travellers can choose from a wide range of
                destinations and travel experiences, with flexible
                packages designed around different budgets, schedules,
                and interests. Whether you're planning a weekend escape
                or a once-in-a-lifetime international journey, our team
                is here to take care of the details.

            </p>


            {{-- =====================================================
                 FEATURES
            ====================================================== --}}

            <div class="about-features">

                <span class="about-feature">
                    <span>◉</span>
                    25+ Years of Experience
                </span>

                <span class="about-feature">
                    <span>♟</span>
                    Customized Travel Experiences
                </span>

                <span class="about-feature">
                    <span>♜</span>
                    Pilgrimage &amp; Leisure Specialists
                </span>

                <span class="about-feature">
                    <span>✈</span>
                    Domestic &amp; International Tours
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