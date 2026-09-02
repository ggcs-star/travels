<section class="journey-section">

    <div class="container">

        {{-- =====================================================
             SECTION HEADER
        ====================================================== --}}

        <div class="journey-section-header">

            <span class="journey-eyebrow">
                <span class="journey-eyebrow-dot"></span>
                WHAT WE OFFER
            </span>

            <h2 class="journey-section-title">
                Every Journey,
                <span>Perfectly Crafted</span>
            </h2>

            <p class="journey-section-description">
                From sacred pilgrimages to exotic holidays,
                we specialise in every kind of travel
                experience.
            </p>

        </div>


        {{-- =====================================================
             DYNAMIC CATEGORY CARDS
        ====================================================== --}}

        @if($categories->isNotEmpty())

            <div class="journey-grid">

                @foreach($categories as $category)

                    <a
                        href="{{ route('tours.index', ['category' => $category->slug]) }}"
                        class="journey-card"
                    >

                        {{-- =================================================
                             CATEGORY IMAGE
                        ================================================== --}}

                        <div class="journey-card-image">

                            @php
                                /*
                                 * If category image support is added in the
                                 * future, use it here.
                                 *
                                 * For now, use a common fallback image.
                                 */
                                $categoryImage = $category->image_url
                                    ?? asset('images/hero/tour-bg.jpg');
                            @endphp

                            <img
                                src="{{ $categoryImage }}"
                                alt="{{ $category->name }} Tours"
                                loading="lazy"
                                decoding="async"
                            >

                            <div class="journey-card-overlay"></div>

                        </div>


                        {{-- =================================================
                             CATEGORY CONTENT
                        ================================================== --}}

                        <div class="journey-card-content">

                            <span class="journey-card-icon">
                                ✦
                            </span>

                            <h3>
                                {{ $category->name }}
                            </h3>

                            <p>
                                Explore {{ $category->name }} tours
                            </p>

                            <span class="journey-card-link">
                                Explore Tours
                                <span>→</span>
                            </span>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="journey-empty">

                <h3>No tour categories available</h3>

                <p>
                    New travel categories will appear here once they are
                    published by the administrator.
                </p>

            </div>

        @endif

    </div>

</section>