<section class="monthly-tours-section">

    <div class="container">

        <div class="monthly-tours-header">

            <span class="monthly-eyebrow">
                <span class="monthly-eyebrow-dot"></span>
                EVERY MONTH
            </span>

            <h2 class="monthly-tours-title">
                Monthly <span>Tours</span>
            </h2>

            <p class="monthly-tours-description">
                Fixed departures that run every month – reserve your seat on the next one.
            </p>

        </div>

    </div>


    @if($monthlyTours->isNotEmpty())

        <div class="monthly-carousel">

            <div class="monthly-track">

                {{-- ORIGINAL CARDS --}}

                @foreach($monthlyTours as $tour)

                    <a
                        href="{{ route('tours.show', $tour->slug) }}"
                        class="monthly-tour-card"
                    >

                        <div class="monthly-tour-image">

                            <img
                                src="{{ $tour->cover_image_url }}"
                                alt="{{ $tour->name }}"
                                loading="lazy"
                            >

                            <div class="monthly-tour-overlay"></div>

                        </div>


                        <div class="monthly-tour-badge">

                            <span>⟳</span>

                            {{ $tour->category?->name ?? 'Monthly' }}

                        </div>


                        <div class="monthly-tour-content">

                            <h3>
                                {{ $tour->name }}
                            </h3>

                            <div class="monthly-tour-meta">

                                <span>
                                    ◷ {{ $tour->duration_days }} Days
                                </span>

                                @if($tour->lowest_price !== null)

                                    <span>· from</span>

                                    <strong>
                                        ₹{{ number_format($tour->lowest_price) }}
                                    </strong>

                                @endif

                            </div>

                        </div>

                    </a>

                @endforeach


                {{-- DUPLICATE CARDS FOR SEAMLESS INFINITE SLIDE --}}

                @foreach($monthlyTours as $tour)

                    <a
                        href="{{ route('tours.show', $tour->slug) }}"
                        class="monthly-tour-card"
                        aria-hidden="true"
                        tabindex="-1"
                    >

                        <div class="monthly-tour-image">

                            <img
                                src="{{ $tour->cover_image_url }}"
                                alt=""
                                loading="eager"
                                aria-hidden="true"
                            >

                            <div class="monthly-tour-overlay"></div>

                        </div>


                        <div class="monthly-tour-badge">

                            <span>⟳</span>

                            {{ $tour->category?->name ?? 'Monthly' }}

                        </div>


                        <div class="monthly-tour-content">

                            <h3>
                                {{ $tour->name }}
                            </h3>

                            <div class="monthly-tour-meta">

                                <span>
                                    ◷ {{ $tour->duration_days }} Days
                                </span>

                                @if($tour->lowest_price !== null)

                                    <span>· from</span>

                                    <strong>
                                        ₹{{ number_format($tour->lowest_price) }}
                                    </strong>

                                @endif

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    @else

        <div class="container">

            <div class="monthly-tours-empty">
                <p>New monthly tours are coming soon.</p>
            </div>

        </div>

    @endif

</section>