<section class="handpicked-tours-section">

    {{-- =====================================================
         BACKGROUND
    ====================================================== --}}

    <div class="handpicked-bg"></div>


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="container">

        <div class="handpicked-header">

            <span class="handpicked-eyebrow">
                <span class="handpicked-eyebrow-dot"></span>
                POPULAR PACKAGES
            </span>

            <h2 class="handpicked-title">
                Handpicked <span>Tours</span>
            </h2>

        </div>

    </div>


    {{-- =====================================================
         TOUR CAROUSEL
    ====================================================== --}}

    <div class="handpicked-carousel">

        <div class="handpicked-track">

            @forelse($allTours as $tour)

                <a
                    href="{{ route('tours.show', $tour->slug) }}"
                    class="handpicked-card {{ $tour->featured ? 'handpicked-card-active' : '' }}"
                >

                    {{-- =================================================
                         IMAGE
                    ================================================== --}}

                    <div class="handpicked-card-image">

                        @if($tour->cover_image_url)

                            <img
                                src="{{ $tour->cover_image_url }}"
                                alt="{{ $tour->name }}"
                                loading="lazy"
                            >

                        @else

                            <div
                                style="
                                    width:100%;
                                    height:100%;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    background:#f1f1f1;
                                "
                            >
                                No Image
                            </div>

                        @endif


                        <div class="handpicked-image-overlay"></div>


                        {{-- Featured --}}

                        @if($tour->featured)

                            <span class="handpicked-featured">
                                ★ Featured
                            </span>

                        @endif


                        {{-- Duration --}}

                        @if($tour->duration_days)

                            <span class="handpicked-duration">
                                {{ $tour->duration_days }}
                                {{ $tour->duration_days == 1 ? 'Day' : 'Days' }}
                            </span>

                        @endif

                    </div>


                    {{-- =================================================
                         CARD BODY
                    ================================================== --}}

                    <div class="handpicked-card-body">


                        {{-- Category --}}

                        <span class="handpicked-category">

                            <span>♜</span>

                            {{ strtoupper($tour->category?->name ?? 'TOURS') }}

                        </span>


                        {{-- Tour Name --}}

                        <h3>
                            {{ $tour->name }}
                        </h3>


                        {{-- Location --}}

                        <p class="handpicked-location">

                            <span>●</span>

                            {{ $tour->destination }}

                        </p>


                        {{-- Bottom --}}

                        <div class="handpicked-card-bottom">

                            <div>

                                <small>
                                    from
                                </small>


                                @php
                                    $price = $tour->lowestUpcomingPrice();
                                @endphp

                                @if($price !== null)

                                    <strong>
                                        ₹{{ number_format($price) }}
                                    </strong>

                                @else

                                    <strong>
                                        Contact Us
                                    </strong>

                                @endif

                            </div>


                            <span class="handpicked-arrow">
                                →
                            </span>

                        </div>

                    </div>

                </a>

            @empty

                {{-- =================================================
                     NO TOURS
                ================================================== --}}

                <div
                    style="
                        width:100%;
                        padding:60px 20px;
                        text-align:center;
                    "
                >

                    <h3>
                        No tours available right now.
                    </h3>

                    <p>
                        Please check back soon for upcoming packages.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- =====================================================
         VIEW ALL BUTTON
    ====================================================== --}}

    <div class="handpicked-view-all">

        <a href="{{ route('tours.index') }}">
            View All Tours
        </a>

    </div>

</section>