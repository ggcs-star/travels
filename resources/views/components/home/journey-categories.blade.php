<section class="journey-section">

    {{-- DECORATIVE BACKDROP --}}

    <svg class="journey-decor journey-decor--plane" viewBox="0 0 140 90" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M6 78C30 70 48 54 58 36C68 18 86 6 128 4" stroke="currentColor" stroke-width="2" stroke-dasharray="1 8" stroke-linecap="round"/>
        <g transform="translate(100,-2) rotate(28)">
            <path d="M0 12L32 0L28 7L14 10L9 19L4 16Z" fill="currentColor"/>
        </g>
    </svg>

    <svg class="journey-decor journey-decor--compass" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="60" cy="60" r="52" stroke="currentColor" stroke-width="1.4"/>
        <circle cx="60" cy="60" r="3" fill="currentColor"/>
        <path d="M60 12v14M60 94v14M12 60h14M94 60h14" stroke="currentColor" stroke-width="1.4"/>
        <path d="M60 26L67 53L60 60L53 53Z" fill="currentColor"/>
    </svg>

    <svg class="journey-decor journey-decor--leaf-left" viewBox="0 0 180 180" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g transform="translate(14,178)">
            <path transform="rotate(-55)" d="M0,0 C-15,-24 -13,-64 0,-108 C13,-64 15,-24 0,0 Z"/>
            <path transform="rotate(-30)" d="M0,0 C-16,-26 -14,-70 0,-124 C14,-70 16,-26 0,0 Z"/>
            <path transform="rotate(-4)" d="M0,0 C-15,-24 -13,-64 0,-110 C13,-64 15,-24 0,0 Z"/>
            <path transform="rotate(20)" d="M0,0 C-13,-20 -11,-52 0,-88 C11,-52 13,-20 0,0 Z"/>
            <path transform="rotate(44)" d="M0,0 C-11,-16 -9,-40 0,-68 C9,-40 11,-16 0,0 Z"/>
        </g>
    </svg>

    <svg class="journey-decor journey-decor--leaf-right" viewBox="0 0 180 180" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g transform="translate(166,178)">
            <path transform="rotate(55)" d="M0,0 C15,-24 13,-64 0,-108 C-13,-64 -15,-24 0,0 Z"/>
            <path transform="rotate(30)" d="M0,0 C16,-26 14,-70 0,-124 C-14,-70 -16,-26 0,0 Z"/>
            <path transform="rotate(4)" d="M0,0 C15,-24 13,-64 0,-110 C-13,-64 -15,-24 0,0 Z"/>
            <path transform="rotate(-20)" d="M0,0 C13,-20 11,-52 0,-88 C-11,-52 -13,-20 0,0 Z"/>
            <path transform="rotate(-44)" d="M0,0 C11,-16 9,-40 0,-68 C-9,-40 -11,-16 0,0 Z"/>
        </g>
    </svg>

    <span class="journey-decor journey-decor--cloud journey-decor--cloud-1"></span>
    <span class="journey-decor journey-decor--cloud journey-decor--cloud-2"></span>


    <div class="container">

        {{-- SECTION HEADER --}}
        <div class="journey-section-header">

            <span class="journey-eyebrow">
                <span class="journey-eyebrow-dash"></span>
                WHAT WE OFFER
                <span class="journey-eyebrow-dash"></span>
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


        {{-- DYNAMIC CATEGORY CARDS --}}
        @if($categories->isNotEmpty())

            @php
                $journeyIcons = [
                    'spiritual-pilgrimage' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v3"/><path d="M4 9h16"/><path d="M3 21V11l9-4 9 4v10"/><path d="M7 21v-7h10v7"/><path d="M2 21h20"/></svg>',
                    'holidays' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 7 12 4H5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4.5Z"/><circle cx="12" cy="13" r="3.5"/></svg>',
                    'school-college' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m2 9 10-5 10 5-10 5-10-5Z"/><path d="M6 11v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"/><path d="M22 9v7"/></svg>',
                    'business-trips' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/></svg>',
                    'monthly-tours' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-7.5 7-13a7 7 0 1 0-14 0c0 5.5 7 13 7 13Z"/><circle cx="12" cy="9" r="2.5"/></svg>',
                ];

                $journeyFallbackIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.9 6.6L22 10l-5 4.9L18.2 22 12 18.6 5.8 22 7 14.9 2 10l7.1-1.4L12 2Z"/></svg>';

                $journeyFallbackImages = [
                    'spiritual-pilgrimage' => 'images/hero/hero3.jpg',
                    'school-college' => 'images/hero/cat-school.jpg',
                ];
            @endphp

            <div class="journey-grid">

                @foreach($categories as $index => $category)

                    <a
                        href="{{ route('tours.index', ['category' => $category->slug]) }}"
                        class="journey-card {{ $index % 2 === 0 ? 'journey-card--green' : 'journey-card--orange' }}"
                    >

                        {{-- POLAROID FRAME --}}
                        <div class="journey-card-frame">

                            <div class="journey-card-photo">

                                @php
                                    $categoryImage = $category->image
                                        ? asset('storage/' . $category->image)
                                        : asset($journeyFallbackImages[$category->slug] ?? 'images/hero/tour-bg.jpg');
                                @endphp

                                <img
                                    src="{{ $categoryImage }}"
                                    alt="{{ $category->name }} Tours"
                                    loading="lazy"
                                    decoding="async"
                                >

                                <span class="journey-card-badge">
                                    {!! $journeyIcons[$category->slug] ?? $journeyFallbackIcon !!}
                                </span>

                            </div>

                            <div class="journey-card-footer">

                                <span class="journey-card-dot">
                                    {!! $journeyIcons[$category->slug] ?? $journeyFallbackIcon !!}
                                </span>

                                <h3>
                                    {{ $category->name }}
                                </h3>

                            </div>

                        </div>


                        {{-- CAPTION + LINK --}}
                        <p class="journey-card-caption">
                            {{ $category->short_description
                                ?? 'Explore ' . $category->name . ' tours'
                            }}
                        </p>

                        <span class="journey-card-link">
                            Explore Tours
                            <span>→</span>
                        </span>

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
