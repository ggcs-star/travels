@extends('layouts.app')

@section('title', $tour->name.' | '.config('travels.brand.name'))

@section(
    'meta_description',
    $tour->short_description
        ?: Str::limit(strip_tags($tour->description), 155)
)

@section('content')

@php
    
    $itinerary = data_get($tour, 'itinerary');
    $importantNotes = data_get($tour, 'important_notes');
    $packages = data_get($tour, 'packages');
    $termsConditions = data_get($tour, 'terms_conditions');
    $cancellationPolicy = data_get($tour, 'cancellation_policy');

    $renderRichText = static function ($value): string {
        $value = (string) ($value ?? '');

        if ($value === '') {
            return '';
        }

        $value = preg_replace(
            '/<\s*(script|style|iframe|object|embed|form|base|meta|link)[^>]*>.*?<\s*\/\s*\1\s*>/is',
            '',
            $value
        ) ?? $value;

        $value = preg_replace(
            '/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i',
            '',
            $value
        ) ?? $value;

        $value = preg_replace_callback(
            '/(<a\b[^>]*\bhref\s*=\s*)(["\'])(.*?)(\2)/is',
            static function ($match) {
                $href = trim($match[3]);

                if (preg_match('/^(?:javascript|data|vbscript):/i', $href)) {
                    return $match[1] . $match[2] . '#' . $match[4];
                }

                return $match[0];
            },
            $value
        ) ?? $value;

        return strip_tags(
            $value,
            '<h3><h4><h5><p><ul><ol><li><strong><b><em><i><u><br><a><blockquote>'
        );
    };

    if (is_string($itinerary)) {
        $decoded = json_decode($itinerary, true);
        $itinerary = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    if ($itinerary instanceof \Illuminate\Support\Collection) {
        $itinerary = $itinerary->all();
    }

    if (is_string($importantNotes)) {
        $decoded = json_decode($importantNotes, true);
        $importantNotes = json_last_error() === JSON_ERROR_NONE ? $decoded : [$importantNotes];
    }

    if ($importantNotes instanceof \Illuminate\Support\Collection) {
        $importantNotes = $importantNotes->all();
    }

    if (is_string($packages)) {
        $decoded = json_decode($packages, true);
        $packages = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    if ($packages instanceof \Illuminate\Support\Collection) {
        $packages = $packages->all();
    }

    if (is_string($termsConditions)) {
        $decoded = json_decode($termsConditions, true);
        $termsConditions = json_last_error() === JSON_ERROR_NONE ? $decoded : [$termsConditions];
    }

    if ($termsConditions instanceof \Illuminate\Support\Collection) {
        $termsConditions = $termsConditions->all();
    }

    if (is_string($cancellationPolicy)) {
        $decoded = json_decode($cancellationPolicy, true);
        $cancellationPolicy = json_last_error() === JSON_ERROR_NONE ? $decoded : [$cancellationPolicy];
    }

    if ($cancellationPolicy instanceof \Illuminate\Support\Collection) {
        $cancellationPolicy = $cancellationPolicy->all();
    }

    $hasItinerary = is_array($itinerary) && count($itinerary) > 0;
    $hasImportantNotes = is_array($importantNotes) && count($importantNotes) > 0;
    $hasPackages = is_array($packages) && count($packages) > 0;

    $sidebarPackagePrice = null;

    if ($hasPackages) {
        $firstSidebarPackage = $packages[0] ?? null;

        if (is_array($firstSidebarPackage)) {
            $sidebarPackagePrice = $firstSidebarPackage['price'] ?? null;
        }
    }

    if ($sidebarPackagePrice === null && isset($departures) && $departures->count()) {
        $firstDeparture = $departures->first();
        $sidebarPackagePrice = $firstDeparture->effective_price ?? $firstDeparture->price ?? null;
    }
    $hasIncludesExcludes = !empty($tour->included_items) || !empty($tour->excluded_items);
    $hasHighlights = !empty($tour->highlights);
    $hasTermsConditions = is_array($termsConditions) && count($termsConditions) > 0;
    $hasCancellationPolicy = is_array($cancellationPolicy) && count($cancellationPolicy) > 0;
@endphp

<section class="tour-gallery-hero">

    <div class="tour-gallery-hero__grid">

        <a
            href="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
            class="tour-gallery-hero__main"
            target="_blank"
            rel="noopener"
        >
            <img
                src="{{ $tour->cover_image_url ?: asset('images/hero/tour-bg.jpg') }}"
                alt="{{ $tour->name }}"
            >
            <span class="tour-gallery-hero__main-overlay"></span>
        </a>

        @foreach($tour->images->take(4) as $image)
            @php
                $imagePath = $image->image_path
                    ?? $image->path
                    ?? $image->image
                    ?? null;

                if ($imagePath && \Illuminate\Support\Str::startsWith(
                    $imagePath,
                    ['http://', 'https://', '//']
                )) {
                    $imageUrl = $imagePath;
                } else {
                    $imageUrl = $imagePath
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath)
                        : null;
                }
            @endphp

            @if($imageUrl)
                <a
                    href="{{ $imageUrl }}"
                    class="tour-gallery-hero__item"
                    target="_blank"
                    rel="noopener"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $tour->name }} gallery image"
                        loading="lazy"
                    >
                </a>
            @endif
        @endforeach

        @if($tour->images->count() > 4)
            <div class="tour-gallery-hero__more">
                +{{ $tour->images->count() - 4 }} photos
            </div>
        @endif

    </div>

    <div class="tour-gallery-hero__content">
        <div class="container">

            <div class="tour-gallery-hero__breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <a href="{{ route('tours.index') }}">Tours</a>
                <span>/</span>
                <span>{{ $tour->tour_type ?: 'Tour' }}</span>
            </div>

            <h1>{{ $tour->name }}</h1>

            <div class="tour-gallery-hero__meta">
                <span>📅 {{ $tour->duration_days }} Days / {{ $tour->duration_nights }} Nights</span>

                @if($tour->destination)
                    <span>📍 {{ $tour->destination }}</span>
                @endif

                @if($tour->tour_type)
                    <span>✦ {{ $tour->tour_type }}</span>
                @endif

                @if($tour->starting_city)
                    <span>⇢ {{ $tour->starting_city }}</span>
                @endif
            </div>

        </div>
    </div>

</section>

<nav class="tour-detail-nav" aria-label="Tour sections">
    <div class="container tour-detail-nav__inner" data-tour-tabs>
        <a href="#overview" class="is-active" data-tour-tab="overview">Overview</a>

        @if($hasItinerary)
            <a href="#itinerary" data-tour-tab="itinerary">Itinerary</a>
        @endif

        @if($hasIncludesExcludes)
            <a href="#includes" data-tour-tab="includes">Includes &amp; Excludes</a>
        @endif
    </div>
</nav>

<section class="storefront-section tour-detail-page">
    <div class="container tour-detail-layout">

        <main class="tour-detail-content" data-tour-panels>

<section
    class="tour-detail-block tour-detail-panel is-active"
    id="overview"
    data-tour-panel="overview"
>

    <div class="overview-about">

        <h2>
            About This Tour
        </h2>

        @if($tour->short_description)

            <p class="overview-about__description">
                {{ $tour->short_description }}
            </p>

        @endif

        @if($tour->description)

            <div class="overview-about__rich-content">
                {!! $renderRichText($tour->description) !!}
            </div>

        @endif

    </div>

    @if($hasHighlights)

        <section class="overview-highlights">

            <h2>
                <span aria-hidden="true">★</span>
                Tour Highlights
            </h2>

            <ul>

                @foreach($tour->highlights as $highlight)

                    @php
                        $highlightText = is_array($highlight)
                            ? (
                                $highlight['title']
                                ?? $highlight['name']
                                ?? $highlight['text']
                                ?? ''
                            )
                            : $highlight;
                    @endphp

                    @if(trim((string) $highlightText) !== '')

                        <li>

                            <span
                                class="overview-highlight__icon"
                                aria-hidden="true"
                            >
                                ✓
                            </span>

                            <span class="overview-highlight__text">
                                {{ $highlightText }}
                            </span>

                        </li>

                    @endif

                @endforeach

            </ul>

        </section>

    @endif

</section>
            
            @if($hasItinerary)
                <section class="tour-itinerary tour-detail-block tour-detail-panel" id="itinerary" data-tour-panel="itinerary">
                    <div class="tour-section-heading tour-itinerary-heading">
                        <div>
                            <span class="storefront-kicker">DAY-BY-DAY ITINERARY</span>
                            <h2>Tour Itinerary</h2>
                        </div>
                    </div>

                    <a
                        href="{{ route('tours.itinerary.pdf', ['tour' => $tour->slug]) }}"
                        class="tour-itinerary-download"
                        target="_blank"
                        rel="noopener"
                    >
                        <span class="tour-itinerary-download__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3v12"></path>
                                <path d="m7 10 5 5 5-5"></path>
                                <path d="M5 21h14"></path>
                            </svg>
                        </span>
                        <span class="tour-itinerary-download__text">
                            <strong>Download Itinerary</strong>
                            <small>Full day-by-day plan as a PDF</small>
                        </span>
                        <span class="tour-itinerary-download__arrow" aria-hidden="true">↓</span>
                    </a>

                    <div class="tour-itinerary-list">
                        @foreach($itinerary as $key => $day)
                            @php
                                $dayNumber = is_array($day)
                                    ? ($day['day'] ?? ($key + 1))
                                    : ($key + 1);

                                $dayTitle = null;
                                $dayDescription = null;
                                $dayItems = [];

                                if (is_array($day)) {
                                    $dayTitle = $day['title']
                                        ?? $day['name']
                                        ?? $day['heading']
                                        ?? null;

                                    $dayDescription = $day['description']
                                        ?? $day['details']
                                        ?? $day['content']
                                        ?? null;

                                    $dayItems = $day['activities']
                                        ?? $day['items']
                                        ?? [];
                                } else {
                                    $dayDescription = $day;
                                }

                                if (!$dayTitle) {
                                    $dayTitle = 'Day '.($dayNumber ?: ($key + 1));
                                }

                                if (is_string($dayItems)) {
                                    $dayItems = preg_split('/\r\n|\r|\n/', $dayItems, -1, PREG_SPLIT_NO_EMPTY);
                                }

                                if (!is_array($dayItems)) {
                                    $dayItems = [];
                                }

                                $dayDescription = is_array($dayDescription)
                                    ? implode("\n", array_filter(array_map('strval', $dayDescription)))
                                    : (string) $dayDescription;
                            @endphp

                            <article class="tour-itinerary-item">
                                <div class="tour-itinerary-item__marker">
                                    <span>Day {{ (int) $dayNumber }}</span>
                                </div>

                                <div class="tour-itinerary-item__body">
                                    <h3>{{ $dayTitle }}</h3>

                                    @if(trim($dayDescription) !== '')
                                        <div class="tour-itinerary-item__copy">
                                            {!! nl2br(e($dayDescription)) !!}
                                        </div>
                                    @endif

                                    @if(count($dayItems))
                                        <ul class="tour-itinerary-item__activities">
                                            @foreach($dayItems as $item)
                                                @php
                                                    $activityText = is_array($item)
                                                        ? ($item['title'] ?? $item['name'] ?? $item['activity'] ?? $item['text'] ?? '')
                                                        : $item;
                                                @endphp

                                                @if(trim((string) $activityText) !== '')
                                                    <li>{{ $activityText }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif

                                    @php
                                        $location = is_array($day) ? ($day['location'] ?? $day['city'] ?? null) : null;
                                        $meals = is_array($day) ? ($day['meals'] ?? null) : null;
                                        $overnight = is_array($day) ? ($day['overnight'] ?? $day['stay'] ?? null) : null;

                                        if (is_array($meals)) {
                                            $meals = implode(', ', array_filter(array_map('strval', $meals)));
                                        }
                                    @endphp

                                    @if($location || $meals || $overnight)
                                        <div class="tour-itinerary-item__meta">
                                            @if($location)
                                                <span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                                        <circle cx="12" cy="10" r="2.5"></circle>
                                                    </svg>
                                                    {{ $location }}
                                                </span>
                                            @endif

                                            @if($meals)
                                                <span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M7 3v8"></path>
                                                        <path d="M4.5 3v4.5A2.5 2.5 0 0 0 7 10a2.5 2.5 0 0 0 2.5-2.5V3"></path>
                                                        <path d="M7 10v11"></path>
                                                        <path d="M15 3v18"></path>
                                                        <path d="M15 3c3 0 4.5 1.8 4.5 4.5S18 12 15 12"></path>
                                                    </svg>
                                                    {{ $meals }}
                                                </span>
                                            @endif

                                            @if($overnight)
                                                <span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 18v-7"></path>
                                                        <path d="M3 15h18"></path>
                                                        <path d="M21 18v-4a3 3 0 0 0-3-3H8a5 5 0 0 0-5 5"></path>
                                                        <path d="M7 15v3"></path>
                                                        <path d="M17 15v3"></path>
                                                    </svg>
                                                    Overnight: {{ $overnight }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($hasIncludesExcludes)

    <section
        class="tour-inclusions tour-detail-block tour-detail-panel"
        id="includes"
        data-tour-panel="includes"
    >

        <div class="tour-section-heading">
            <h2>Includes &amp; Excludes</h2>
        </div>

        <div class="tour-inclusion-columns">

            @if(!empty($tour->included_items))

                <div class="tour-inclusion-card tour-inclusion-card--included">

                    <div class="tour-inclusion-card__header">
                        <span class="tour-inclusion-card__header-icon">
                            ✓
                        </span>

                        <strong>
                            What's Included
                        </strong>
                    </div>

                    <div class="tour-inclusion-card__body">

                        <ul>
                            @foreach($tour->included_items as $item)

                                <li>
                                    <span class="tour-inclusion-card__icon">
                                        ✓
                                    </span>

                                    <span class="tour-inclusion-card__text">
                                        {{ $item }}
                                    </span>
                                </li>

                            @endforeach
                        </ul>

                    </div>

                </div>

            @endif

            @if(!empty($tour->excluded_items))

                <div class="tour-inclusion-card tour-inclusion-card--excluded">

                    <div class="tour-inclusion-card__header">
                        <span class="tour-inclusion-card__header-icon">
                            ×
                        </span>

                        <strong>
                            What's Excluded
                        </strong>
                    </div>

                    <div class="tour-inclusion-card__body">

                        <ul>
                            @foreach($tour->excluded_items as $item)

                                <li>
                                    <span class="tour-inclusion-card__icon">
                                        ×
                                    </span>

                                    <span class="tour-inclusion-card__text">
                                        {{ $item }}
                                    </span>
                                </li>

                            @endforeach
                        </ul>

                    </div>

                </div>

            @endif

        </div>

    </section>

@endif

            @if($hasImportantNotes)
                <section class="tour-important-notes tour-detail-block tour-detail-panel" id="important-notes" data-tour-panel="important-notes">
                    <span class="storefront-kicker">Before You Travel</span>
                    <h2>Important Notes</h2>

                    @php
                        
                        $importantNoteItems = [];

                        foreach ($importantNotes as $note) {
                            $rawNote = is_array($note)
                                ? ($note['description'] ?? $note['content'] ?? $note['text'] ?? '')
                                : $note;

                            $rawNote = (string) $rawNote;

                            if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $rawNote, $matches)) {
                                foreach ($matches[1] as $li) {
                                    $text = trim(html_entity_decode(strip_tags($li), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                                    if ($text !== '') {
                                        $importantNoteItems[] = $text;
                                    }
                                }
                            } else {
                                $plain = trim(html_entity_decode(strip_tags($rawNote), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                                foreach (preg_split('/\r\n|\r|\n/', $plain) as $line) {
                                    $line = trim($line);

                                    if ($line !== '') {
                                        $importantNoteItems[] = $line;
                                    }
                                }
                            }
                        }
                    @endphp

                    <div class="tour-important-notes__list">
                        @foreach($importantNoteItems as $index => $noteText)
                            <div class="tour-important-note">
                                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div class="tour-rich-content">
                                        {!! $renderRichText(e($noteText)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($hasTermsConditions)
                <section class="tour-important-notes tour-detail-block tour-detail-panel" id="terms-conditions" data-tour-panel="terms-conditions">
                    <span class="storefront-kicker">Please Read</span>
                    <h2>Terms &amp; Conditions</h2>

                    <div class="tour-important-notes__list">
                        @php
                            $termItems = [];

                            foreach ($termsConditions as $term) {
                                $rawTerm = is_array($term)
                                    ? ($term['description'] ?? $term['content'] ?? $term['text'] ?? $term['title'] ?? '')
                                    : $term;

                                $rawTerm = (string) $rawTerm;

                                if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $rawTerm, $matches)) {
                                    foreach ($matches[1] as $li) {
                                        $plain = trim(html_entity_decode(strip_tags($li), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                        if ($plain !== '') {
                                            $termItems[] = $plain;
                                        }
                                    }
                                } else {
                                    $plain = trim(html_entity_decode(strip_tags($rawTerm), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                    foreach (preg_split('/\r\n|\r|\n/', $plain) as $line) {
                                        $line = trim($line);
                                        if ($line !== '') {
                                            $termItems[] = $line;
                                        }
                                    }
                                }
                            }
                        @endphp

                        @foreach($termItems as $index => $termText)
                            <div class="tour-important-note tour-terms-note">
                                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="tour-terms-note__body">
                                    <div class="tour-rich-content">
                                        {!! $renderRichText(e($termText)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($hasCancellationPolicy)
                @php
                    
                    $cancellationItems = [];

                    foreach ($cancellationPolicy as $policy) {
                        $rawPolicy = is_array($policy)
                            ? ($policy['description'] ?? $policy['content'] ?? $policy['text'] ?? '')
                            : $policy;

                        $rawPolicy = (string) $rawPolicy;

                        if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $rawPolicy, $matches)) {
                            foreach ($matches[1] as $li) {
                                $plain = trim(
                                    html_entity_decode(
                                        strip_tags($li),
                                        ENT_QUOTES | ENT_HTML5,
                                        'UTF-8'
                                    )
                                );

                                if ($plain !== '') {
                                    $cancellationItems[] = $plain;
                                }
                            }
                        } else {
                            $plain = trim(
                                html_entity_decode(
                                    strip_tags($rawPolicy),
                                    ENT_QUOTES | ENT_HTML5,
                                    'UTF-8'
                                )
                            );

                            foreach (preg_split('/\r\n|\r|\n/', $plain) as $line) {
                                $line = trim($line);

                                if ($line !== '') {
                                    $cancellationItems[] = $line;
                                }
                            }
                        }
                    }
                @endphp

                <section class="tour-cancellation tour-detail-block tour-detail-panel" id="cancellation-policy" data-tour-panel="cancellation-policy">
                    <span class="storefront-kicker">Policy</span>
                    <h2>Cancellation Policy</h2>

                    <p class="tour-cancellation-intro">
                        We understand plans can change. Below is our fair cancellation policy for this tour.
                    </p>

                    <div class="tour-cancellation-list">
                        @foreach($cancellationItems as $policyText)
                            <div class="tour-cancellation-item">
                                <span class="tour-cancellation-item__dot" aria-hidden="true"></span>
                                <div class="tour-cancellation-item__text">
                                    {!! $renderRichText(e($policyText)) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="tour-cancellation-contact">
                        <span class="tour-cancellation-contact__icon">i</span>
                        <span>Need to cancel or reschedule?</span>
                        <a href="{{ route('contact') }}">Contact us</a>
                        <span>— we'll do our best to help.</span>
                    </div>
                </section>
            @endif

        </main>

        @php
            $sidebarSettingsService = app(\App\Services\SettingsService::class);
            $sidebarSettings = $sidebarSettingsService->all() ?: [];

            $sidebarPhone = trim((string) ($sidebarSettings['site.phone'] ?? ''));
            $sidebarWhatsapp = trim((string) (
                $sidebarSettings['site.whatsapp']
                ?? $sidebarSettings['site.phone']
                ?? ''
            ));

            $sidebarPhoneNumber = preg_replace('/[^0-9]/', '', (string) (preg_split('/[,;|]/', $sidebarPhone)[0] ?? ''));
            $sidebarWhatsappNumber = preg_replace('/[^0-9]/', '', (string) (preg_split('/[,;|]/', $sidebarWhatsapp)[0] ?? ''));

            if (strlen($sidebarPhoneNumber) === 10) {
                $sidebarPhoneNumber = '91' . $sidebarPhoneNumber;
            }

            if (strlen($sidebarWhatsappNumber) === 10) {
                $sidebarWhatsappNumber = '91' . $sidebarWhatsappNumber;
            }

            $sidebarTourName = rawurlencode($tour->name);
        @endphp

        <aside class="departure-panel" id="departures">

            @if($hasItinerary)
                <div class="overview-sidebar-card overview-sidebar-card--itinerary">
                    <div class="overview-sidebar-card__head">
                        <span class="overview-sidebar-card__icon" aria-hidden="true">▣</span>
                        <div>
                            <strong>TOUR ITINERARY</strong>
                            <small>Complete day-by-day plan</small>
                        </div>
                    </div>

                    <a
                        href="{{ route('tours.itinerary.pdf', ['tour' => $tour->slug]) }}"
                        class="overview-sidebar-card__button"
                        target="_blank"
                        rel="noopener"
                    >
                        <span>⇩ &nbsp; Download Itinerary</span>
                    </a>

                    <p>Full day-by-day plan · PDF</p>
                </div>
            @endif

            @php
                
                $sidebarCategoryName = trim((string) (
                    data_get($tour, 'category.name')
                    ?? data_get($tour, 'category.title')
                    ?? data_get($tour, 'category.label')
                    ?? 'Tour'
                ));

                if ($sidebarCategoryName === '') {
                    $sidebarCategoryName = 'Tour';
                }

                $sidebarBookDateMessage = rawurlencode(
                    'Hi, I am interested in ' . $tour->name
                    . ' (' . $sidebarCategoryName . '). '
                    . 'Please share the available ' . $sidebarCategoryName
                    . ' dates and booking details.'
                );

                $sidebarWhatsappMessage = rawurlencode(
                    'Hi, I am interested in ' . $tour->name
                    . ' (' . $sidebarCategoryName . '). '
                    . 'Please share the booking details.'
                );
            @endphp

            <div class="sidebar-dates-card">
                <div class="sidebar-dates-card__head">
                    <span class="sidebar-card-icon" aria-hidden="true">▣</span>
                    <strong>DEPARTURE DATES</strong>
                </div>

                <div class="sidebar-dates-card__label">
                    <span aria-hidden="true">▣</span>
                    <strong>{{ $sidebarCategoryName }}</strong>
                </div>

                <div class="sidebar-dates-card__single-action">
                    @if($sidebarWhatsappNumber)
                        <a
                            href="https://wa.me/{{ $sidebarWhatsappNumber }}?text={{ $sidebarBookDateMessage }}"
                            target="_blank"
                            rel="noopener"
                            class="sidebar-book-date sidebar-book-date--full"
                        >
                            <span aria-hidden="true">◉</span>
                            Book This Date
                        </a>
                    @else
                        <a
                            href="{{ route('contact') }}"
                            class="sidebar-book-date sidebar-book-date--full"
                        >
                            <span aria-hidden="true">◉</span>
                            Book This Date
                        </a>
                    @endif
                </div>

                <div class="sidebar-dates-card__note">
                    <span aria-hidden="true">●</span>
                    <span>Can't find your date?</span>
                    <a href="{{ route('contact') }}">Contact us</a>
                </div>
            </div>

            @php
                
                $sidebarPackages = $hasPackages
                    ? $packages
                    : [[
                        'name' => 'Standard Package',
                        'price' => $sidebarPackagePrice,
                    ]];
            @endphp

            <div class="sidebar-pkg-card">
                <div class="sidebar-pkg-card__head">
                    <span aria-hidden="true">◆</span>
                    <strong>PACKAGES &amp; PRICING</strong>
                </div>

                @foreach($sidebarPackages as $package)
                    @php
                        $packageName = is_array($package)
                            ? ($package['name'] ?? $package['title'] ?? 'Standard Package')
                            : 'Standard Package';

                        $packagePrice = is_array($package)
                            ? ($package['price'] ?? null)
                            : null;

                        $packageDescription = is_array($package)
                            ? ($package['description'] ?? null)
                            : null;
                    @endphp

                    <div class="sidebar-pkg-card__item">
                        <span class="sidebar-pkg-card__badge">Most Popular</span>

                        <strong class="sidebar-pkg-card__name">
                            {{ $packageName }}
                        </strong>

                        @if($packagePrice !== null)
                            <strong class="sidebar-pkg-card__price">
                                ₹{{ number_format((float) $packagePrice, 0) }}
                            </strong>

                            <span class="sidebar-pkg-card__per">
                                per person
                            </span>
                        @endif

                        @if($packageDescription)
                            <p class="sidebar-pkg-card__description">
                                {{ $packageDescription }}
                            </p>
                        @endif

                        <a
                            href="{{ route('bookings.create', ['tour' => $tour]) }}"
                            class="sidebar-pkg-card__book"
                        >
                            Book Now
                        </a>
                    </div>
                @endforeach

                <div class="sidebar-pkg-card__actions">
                    @if($sidebarPhoneNumber)
                        <a
                            href="tel:+{{ $sidebarPhoneNumber }}"
                            class="sidebar-action sidebar-action--call"
                        >
                            <span>☎</span>
                            Call Now
                        </a>
                    @endif

                    @if($sidebarWhatsappNumber)
                        <a
                            href="https://wa.me/{{ $sidebarWhatsappNumber }}?text={{ $sidebarWhatsappMessage }}"
                            target="_blank"
                            rel="noopener"
                            class="sidebar-action sidebar-action--whatsapp"
                        >
                            <span>◉</span>
                            WhatsApp Us
                        </a>
                    @endif
                </div>
            </div>

        </aside>

    </div>
</section>

<style>

.tour-rich-content {
    color: inherit;
    line-height: 1.75;
}

.tour-rich-content p {
    margin: 0 0 12px;
}

.tour-rich-content p:last-child {
    margin-bottom: 0;
}

.tour-rich-content h3,
.tour-rich-content h4,
.tour-rich-content h5 {
    margin: 0 0 10px;
    color: #132238;
    font-weight: 700;
}

.tour-rich-content ul,
.tour-rich-content ol {
    margin: 8px 0 14px;
    padding-left: 24px;
}

.tour-rich-content li {
    margin: 5px 0;
}

.tour-rich-content strong,
.tour-rich-content b {
    font-weight: 700;
}

.tour-rich-content a {
    color: #d88900;
    font-weight: 600;
    text-decoration: underline;
}

.tour-rich-content blockquote {
    margin: 12px 0;
    padding-left: 14px;
    border-left: 3px solid #f5a623;
}

.tour-rich-content br {
    line-height: 1.75;
}

.tour-itinerary {
    --itinerary-orange: #f59a17;
    --itinerary-orange-dark: #e98608;
    --itinerary-border: #dfe7ef;
    --itinerary-card: #f2f6fa;
    --itinerary-text: #17283a;
}

.tour-itinerary-heading {
    margin-bottom: 24px;
}

.tour-itinerary-heading h2 {
    margin: 6px 0 0;
}

.tour-itinerary-download {
    display: flex;
    align-items: center;
    gap: 16px;
    width: 100%;
    min-height: 74px;
    margin: 0 0 22px;
    padding: 14px 20px;
    border: 1px solid #f0b2b2;
    border-left: 4px solid #e31e24;
    border-radius: 20px;
    background: #fff;
    color: var(--itinerary-text);
    text-decoration: none;
    box-shadow: 0 8px 25px rgba(22, 40, 58, .05);
    transition: transform .2s ease, box-shadow .2s ease;
}

.tour-itinerary-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(22, 40, 58, .09);
}

.tour-itinerary-download__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    color: #e31e24;
}

.tour-itinerary-download__icon svg {
    width: 30px;
    height: 30px;
}

.tour-itinerary-download__text {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.tour-itinerary-download__text strong {
    font-size: 15px;
    font-weight: 800;
}

.tour-itinerary-download__text small {
    color: #708094;
    font-size: 12px;
}

.tour-itinerary-download__arrow {
    margin-left: auto;
    color: #e31e24;
    font-size: 25px;
    line-height: 1;
}

.tour-itinerary-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.tour-itinerary-item {
    display: grid;
    grid-template-columns: 64px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
}

.tour-itinerary-item__marker {
    display: flex;
    justify-content: flex-start;
    padding-top: 0;
}

.tour-itinerary-item__marker span {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--itinerary-orange);
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.1;
    text-align: center;
    box-shadow: 0 6px 15px rgba(245, 154, 23, .18);
}

.tour-itinerary-item__body {
    min-width: 0;
    padding: 22px 24px;
    border: 1px solid var(--itinerary-border);
    border-radius: 17px;
    background: var(--itinerary-card);
}

.tour-itinerary-item__body h3 {
    margin: 0 0 10px;
    color: var(--itinerary-text);
    font-size: 17px;
    font-weight: 800;
    line-height: 1.35;
}

.tour-itinerary-item__copy {
    color: #354a60;
    font-size: 13px;
    line-height: 1.75;
}

.tour-itinerary-item__activities {
    margin: 14px 0 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 7px;
}

.tour-itinerary-item__activities li {
    position: relative;
    padding-left: 19px;
    color: #354a60;
    font-size: 13px;
    line-height: 1.5;
}

.tour-itinerary-item__activities li::before {
    content: "•";
    position: absolute;
    left: 2px;
    top: 0;
    color: var(--itinerary-orange-dark);
    font-size: 18px;
    font-weight: 900;
    line-height: 1.25;
}

.tour-itinerary-item__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px 22px;
    margin-top: 15px;
    padding-top: 12px;
    border-top: 1px solid rgba(118, 139, 160, .22);
}

.tour-itinerary-item__meta span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #526b82;
    font-size: 11px;
    font-weight: 600;
}

.tour-itinerary-item__meta svg {
    width: 14px;
    height: 14px;
    flex: 0 0 14px;
    color: var(--itinerary-orange-dark);
}

@media (min-width: 992px) {
    .tour-itinerary-item {
        grid-template-columns: 64px minmax(0, 1fr);
    }
}

@media (max-width: 767px) {
    .tour-itinerary-download {
        min-height: 68px;
        padding: 12px 14px;
        border-radius: 15px;
    }

    .tour-itinerary-download__icon {
        width: 36px;
        height: 36px;
        flex-basis: 36px;
    }

    .tour-itinerary-download__icon svg {
        width: 25px;
        height: 25px;
    }

    .tour-itinerary-download__text strong {
        font-size: 14px;
    }

    .tour-itinerary-download__text small {
        font-size: 11px;
    }

    .tour-itinerary-item {
        grid-template-columns: 48px minmax(0, 1fr);
        gap: 10px;
    }

    .tour-itinerary-item__marker span {
        width: 48px;
        height: 48px;
        font-size: 10px;
    }

    .tour-itinerary-item__body {
        padding: 16px;
        border-radius: 14px;
    }

    .tour-itinerary-item__body h3 {
        font-size: 15px;
        margin-bottom: 8px;
    }

    .tour-itinerary-item__copy,
    .tour-itinerary-item__activities li {
        font-size: 12px;
    }

    .tour-itinerary-item__meta {
        display: grid;
        gap: 8px;
    }
}

.kanila-topbar {
    display: none !important;
}

.site-header.kanila-site-header,
.site-header.kanila-site-header.is-fixed {
    top: 0 !important;
}

.tour-detail-panel[data-tour-panel="overview"],
.tour-detail-panel[data-tour-panel="important-notes"],
.tour-detail-panel[data-tour-panel="terms-conditions"],
.tour-detail-panel[data-tour-panel="cancellation-policy"] {
    scroll-margin-top: 110px;
}

.overview-sidebar-card {
    margin-bottom: 16px;
    border: 1px solid #e7e1d1;
    border-radius: 18px;
    background: #fffdf7;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(25, 43, 61, .06);
}

.overview-sidebar-card__head {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 15px 16px;
    border-bottom: 1px solid #eee8d8;
}

.overview-sidebar-card__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #fff0d8;
    color: #d97900;
    font-size: 16px;
    font-weight: 900;
}

.overview-sidebar-card__head strong {
    display: block;
    color: #17283a;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .25px;
}

.overview-sidebar-card__head small {
    display: block;
    margin-top: 3px;
    color: #778392;
    font-size: 10px;
}

.overview-sidebar-card__button {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 14px 15px 8px;
    padding: 12px 15px;
    border-radius: 30px;
    background: #f59a17;
    color: #17283a;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    transition: transform .2s ease, background .2s ease;
}

.overview-sidebar-card__button:hover {
    background: #ed8d0a;
    transform: translateY(-1px);
}

.overview-sidebar-card p {
    margin: 0 15px 14px;
    color: #687684;
    text-align: center;
    font-size: 9.5px;
}

@media (max-width: 767px) {
    .overview-sidebar-card {
        border-radius: 15px;
    }
}

body:has(.tour-detail-page) {
    background: #f4f7fa !important;
}

body:has(.tour-detail-page) .kanila-topbar,
body:has(.tour-detail-page) .topbar,
body:has(.tour-detail-page) .top-bar,
body:has(.tour-detail-page) .site-topbar,
body:has(.tour-detail-page) .header-topbar,
body:has(.tour-detail-page) .top-header,
body:has(.tour-detail-page) .utility-bar,
body:has(.tour-detail-page) .header-utility,
body:has(.tour-detail-page) .contact-topbar {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    min-height: 0 !important;
    overflow: hidden !important;
}

body:has(.tour-detail-page) .site-header,
body:has(.tour-detail-page) header.site-header,
body:has(.tour-detail-page) .kanila-site-header,
body:has(.tour-detail-page) .kanila-site-header.is-fixed {
    top: 0 !important;
}

body:has(.tour-detail-page) .tour-detail-nav {
    position: relative !important;
    top: auto !important;
    z-index: 20 !important;
}

.tour-detail-page #terms-conditions .tour-important-notes__list {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
}

.tour-detail-page #terms-conditions .tour-terms-note {
    display: grid !important;
    grid-template-columns: 38px minmax(0, 1fr) !important;
    align-items: start !important;
    gap: 14px !important;
    min-height: 54px !important;
    padding: 13px 16px !important;
    border: 1px solid #b8defa !important;
    border-radius: 16px !important;
    background: #eef8ff !important;
    box-sizing: border-box !important;
}

.tour-detail-page #terms-conditions .tour-terms-note > span {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #1293d5 !important;
    color: #fff !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

.tour-detail-page #terms-conditions .tour-terms-note__body,
.tour-detail-page #terms-conditions .tour-rich-content {
    min-width: 0 !important;
    margin: 0 !important;
}

.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content,
.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content p,
.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content strong,
.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content b {
    color: #173b5d !important;
    font-size: 13px !important;
    line-height: 1.55 !important;
}

.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content p {
    margin: 0 !important;
}

.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content h3,
.tour-detail-page #terms-conditions .tour-terms-note .tour-rich-content h4 {
    display: none !important;
}

.tour-detail-page {
    background: #f4f7fa !important;
    padding-top: 0 !important;
}

.tour-detail-page .tour-detail-layout {
    max-width: 1068px !important;
    margin: 0 auto !important;
    padding-top: 28px !important;
    padding-bottom: 45px !important;
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 252px !important;
    gap: 28px !important;
    align-items: start !important;
}

.tour-detail-page .tour-detail-content {
    min-width: 0 !important;
}

.tour-detail-nav {
    background: #fff !important;
    border-bottom: 1px solid #dfe5eb !important;
    box-shadow: 0 1px 4px rgba(18, 37, 54, .04) !important;
}

.tour-detail-nav__inner {
    max-width: 1068px !important;
    min-height: 48px !important;
}

.tour-detail-nav__inner a {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #17283a !important;
    padding: 15px 18px 12px !important;
    border-bottom: 3px solid transparent !important;
}

.tour-detail-nav__inner a.is-active {
    color: #d88900 !important;
    border-bottom-color: #f59a17 !important;
}

.tour-detail-page .tour-detail-panel {
    margin: 0 0 22px !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

.tour-detail-page .tour-detail-panel:not(.is-active) {
    display: none !important;
}

.tour-detail-page .tour-detail-panel.is-active {
    display: block !important;
}

.tour-detail-page .storefront-kicker {
    display: block !important;
    margin: 0 0 5px !important;
    color: #d58a22 !important;
    font-size: 9px !important;
    line-height: 1.2 !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: .7px !important;
}

.tour-detail-page .tour-detail-block h2,
.tour-detail-page .tour-detail-block .tour-section-heading h2,
.tour-detail-page .tour-inclusion-column h2 {
    margin: 0 0 12px !important;
    color: #132238 !important;
    font-family: inherit !important;
    font-size: 16px !important;
    line-height: 1.3 !important;
    font-weight: 800 !important;
    letter-spacing: 0 !important;
}

.tour-detail-page .tour-detail-lead,
.tour-detail-page .tour-detail-copy {
    margin: 0 0 18px !important;
    color: #38516a !important;
    font-family: inherit !important;
    font-size: 12px !important;
    line-height: 1.75 !important;
}

.tour-detail-page .tour-important-notes {
    margin-top: 25px !important;
}

.tour-detail-page .tour-important-notes__list {
    display: grid !important;
    gap: 9px !important;
}

.tour-detail-page .tour-important-note {
    display: grid !important;
    grid-template-columns: 28px minmax(0, 1fr) !important;
    gap: 10px !important;
    align-items: start !important;
    margin: 0 !important;
    padding: 12px 13px !important;
    border: 1px solid #f5d77b !important;
    border-radius: 16px !important;
    background: #fffdf3 !important;
    color: #304a61 !important;
    font-size: 11px !important;
    line-height: 1.65 !important;
}

.tour-detail-page .tour-important-note > span {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 23px !important;
    height: 23px !important;
    border-radius: 50% !important;
    background: #f7a719 !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

.tour-detail-page .tour-important-note > div > strong {
    display: block !important;
    margin: 0 0 3px !important;
    color: #173651 !important;
    font-size: 11px !important;
    line-height: 1.35 !important;
    font-weight: 800 !important;
}

.tour-detail-page .tour-important-note .tour-rich-content,
.tour-detail-page .tour-rich-content {
    color: #304a61 !important;
    font-family: inherit !important;
    font-size: 11px !important;
    line-height: 1.65 !important;
}

.tour-detail-page .tour-important-note .tour-rich-content h3,
.tour-detail-page .tour-important-note .tour-rich-content h4,
.tour-detail-page .tour-important-note .tour-rich-content h5 {
    margin: 7px 0 5px !important;
    color: #173651 !important;
    font-family: inherit !important;
    font-size: 12px !important;
    line-height: 1.4 !important;
}

.tour-detail-page .tour-important-note .tour-rich-content p {
    margin: 0 0 7px !important;
}

.tour-detail-page .tour-important-note .tour-rich-content ul,
.tour-detail-page .tour-important-note .tour-rich-content ol {
    margin: 5px 0 7px !important;
    padding-left: 19px !important;
}

.tour-detail-page .tour-important-note .tour-rich-content li {
    margin: 3px 0 !important;
}

.tour-detail-page #terms-conditions .tour-important-note {
    border-color: #b9e0ff !important;
    background: #f0f8ff !important;
}

.tour-detail-page #terms-conditions .tour-important-note > span {
    background: #1594d1 !important;
}

.tour-detail-page #cancellation-policy {
    margin-top: 28px !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-intro {
    margin: 0 0 15px !important;
    color: #304a61 !important;
    font-size: 12px !important;
    line-height: 1.65 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-list {
    display: grid !important;
    gap: 8px !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-item {
    display: grid !important;
    grid-template-columns: 12px minmax(0, 1fr) !important;
    gap: 9px !important;
    align-items: start !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    color: #304a61 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-item__dot {
    width: 7px !important;
    height: 7px !important;
    margin-top: 6px !important;
    border-radius: 50% !important;
    background: #ee9200 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-item__text,
.tour-detail-page #cancellation-policy .tour-cancellation-item__text p {
    margin: 0 !important;
    color: #304a61 !important;
    font-size: 11.5px !important;
    line-height: 1.65 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-item__text strong,
.tour-detail-page #cancellation-policy .tour-cancellation-item__text b {
    color: #173651 !important;
    font-weight: 800 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-contact {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 5px !important;
    margin-top: 17px !important;
    padding: 11px 13px !important;
    border: 1px solid #f3d27a !important;
    border-radius: 8px !important;
    background: #fffaf0 !important;
    color: #8a5b1c !important;
    font-size: 10.5px !important;
    line-height: 1.5 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-contact__icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 14px !important;
    height: 14px !important;
    border-radius: 50% !important;
    background: #a96500 !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

.tour-detail-page #cancellation-policy .tour-cancellation-contact a {
    color: #b66b00 !important;
    font-weight: 800 !important;
    text-decoration: underline !important;
}

.tour-detail-page .departure-panel {
    position: sticky !important;
    top: 72px !important;
}

.tour-detail-page .overview-sidebar-card {
    margin: 0 0 15px !important;
    border: 1px solid #e7e1d1 !important;
    border-radius: 16px !important;
    background: #fffdf7 !important;
    overflow: hidden !important;
    box-shadow: 0 4px 13px rgba(25, 43, 61, .06) !important;
}

.tour-detail-page .overview-sidebar-card__head {
    padding: 12px 13px !important;
}

.tour-detail-page .overview-sidebar-card__button {
    margin: 12px 12px 7px !important;
    padding: 11px 13px !important;
    border-radius: 25px !important;
    background: #f7a719 !important;
    font-size: 10px !important;
}

.tour-detail-page .departure-panel h2,
.tour-detail-page .departure-panel h3 {
    font-family: inherit !important;
}

@media (max-width: 991px) {
    .tour-detail-page .tour-detail-layout {
        grid-template-columns: 1fr !important;
    }

    .tour-detail-page .departure-panel {
        position: static !important;
    }
}

@media (max-width: 600px) {
    .tour-detail-page .tour-detail-layout {
        padding: 18px 14px 35px !important;
    }
}

.tour-detail-page .tour-detail-layout {
    max-width: 1120px !important;
    grid-template-columns: minmax(0, 1fr) 300px !important;
    gap: 24px !important;
}

.tour-detail-page .departure-panel {
    width: 100% !important;
    min-width: 0 !important;
    position: sticky !important;
    top: 82px !important;
    align-self: start !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card {
    width: 100% !important;
    margin: 0 0 14px !important;
    box-sizing: border-box !important;
    border: 1px solid #e4e9ee !important;
    border-radius: 16px !important;
    background: #fff !important;
    overflow: hidden !important;
    box-shadow: 0 5px 18px rgba(24, 42, 59, .07) !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card__head {
    min-height: 54px !important;
    box-sizing: border-box !important;
    padding: 12px 14px !important;
    background: #fffdf7 !important;
    border-bottom: 1px solid #eee8d8 !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card__head > div {
    min-width: 0 !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card__head strong {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card__button {
    width: calc(100% - 24px) !important;
    min-height: 40px !important;
    box-sizing: border-box !important;
    margin: 12px !important;
    padding: 10px 13px !important;
    border-radius: 24px !important;
    justify-content: center !important;
    gap: 8px !important;
    background: #f7a719 !important;
    color: #17283a !important;
    font-size: 10px !important;
    line-height: 1.2 !important;
    text-align: center !important;
}

.tour-detail-page .departure-panel .overview-sidebar-card p {
    margin: -3px 12px 13px !important;
    font-size: 9px !important;
}

.tour-detail-page .departure-panel__top {
    box-sizing: border-box !important;
    margin: 0 !important;
    padding: 14px 15px 11px !important;
    border: 1px solid #e4e9ee !important;
    border-bottom: 0 !important;
    border-radius: 16px 16px 0 0 !important;
    background: #fffdf7 !important;
    box-shadow: 0 5px 18px rgba(24, 42, 59, .05) !important;
}

.tour-detail-page .departure-panel__top .storefront-kicker {
    margin-bottom: 4px !important;
}

.tour-detail-page .departure-panel__top h2 {
    margin: 0 0 4px !important;
    color: #17283a !important;
    font-size: 14px !important;
    line-height: 1.35 !important;
    font-weight: 800 !important;
}

.tour-detail-page .departure-panel__intro {
    margin: 0 !important;
    color: #778392 !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
}

.tour-detail-page .departure-panel__list {
    box-sizing: border-box !important;
    margin: 0 !important;
    padding: 0 15px !important;
    border-left: 1px solid #e4e9ee !important;
    border-right: 1px solid #e4e9ee !important;
    background: #fff !important;
    box-shadow: 0 5px 18px rgba(24, 42, 59, .05) !important;
}

.tour-detail-page .departure-option {
    width: 100% !important;
    box-sizing: border-box !important;
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto !important;
    gap: 12px !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 15px 0 !important;
    border-bottom: 1px dashed #dce2e8 !important;
}

.tour-detail-page .departure-option:last-child {
    border-bottom: 0 !important;
}

.tour-detail-page .departure-option__details,
.tour-detail-page .departure-option__action {
    min-width: 0 !important;
}

.tour-detail-page .departure-option__details {
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
}

.tour-detail-page .departure-option__details strong {
    color: #17283a !important;
    font-size: 12px !important;
    line-height: 1.35 !important;
    font-weight: 800 !important;
}

.tour-detail-page .departure-option__details span {
    color: #8794a1 !important;
    font-size: 9px !important;
    line-height: 1.4 !important;
}

.tour-detail-page .departure-option__details small {
    color: #3d9564 !important;
    font-size: 9px !important;
    line-height: 1.35 !important;
    font-weight: 700 !important;
}

.tour-detail-page .departure-option__action {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-end !important;
    gap: 7px !important;
}

.tour-detail-page .departure-option__action > strong {
    color: #ed7800 !important;
    font-size: 15px !important;
    line-height: 1.2 !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
}

.tour-detail-page .departure-option__action .storefront-button {
    width: auto !important;
    min-width: 102px !important;
    min-height: 34px !important;
    padding: 8px 11px !important;
    border-radius: 20px !important;
    font-size: 9px !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.tour-detail-page .departure-empty {
    padding: 18px 4px !important;
    text-align: center !important;
}

.tour-detail-page .departure-empty p {
    margin: 0 0 12px !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
}

.tour-detail-page .departure-empty .storefront-button {
    width: 100% !important;
}

.tour-detail-page .departure-packages {
    box-sizing: border-box !important;
    width: 100% !important;
    margin: 14px 0 0 !important;
    padding: 15px !important;
    border: 1px solid #f0a31a !important;
    border-radius: 16px !important;
    background: #fffdf7 !important;
    box-shadow: 0 5px 18px rgba(24, 42, 59, .07) !important;
}

.tour-detail-page .departure-packages > .storefront-kicker {
    margin-bottom: 4px !important;
}

.tour-detail-page .departure-packages > h3 {
    margin: 0 0 12px !important;
    color: #17283a !important;
    font-size: 14px !important;
    line-height: 1.35 !important;
    font-weight: 800 !important;
}

.tour-detail-page .departure-package {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    margin: 0 0 8px !important;
    padding: 11px 12px !important;
    border: 1px solid #e9dfc9 !important;
    border-radius: 10px !important;
    background: #fff !important;
}

.tour-detail-page .departure-package:last-child {
    margin-bottom: 0 !important;
}

.tour-detail-page .departure-package > div {
    min-width: 0 !important;
}

.tour-detail-page .departure-package strong {
    display: block !important;
    color: #17283a !important;
    font-size: 10px !important;
    line-height: 1.35 !important;
    font-weight: 800 !important;
}

.tour-detail-page .departure-package span {
    display: block !important;
    margin-top: 3px !important;
    color: #7a8998 !important;
    font-size: 9px !important;
    line-height: 1.4 !important;
}

.tour-detail-page .departure-package b {
    flex: 0 0 auto !important;
    color: #e88a00 !important;
    font-size: 13px !important;
    line-height: 1.2 !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
}

@media (max-width: 991px) {
    .tour-detail-page .tour-detail-layout {
        grid-template-columns: 1fr !important;
        max-width: 760px !important;
        gap: 26px !important;
    }

    .tour-detail-page .departure-panel {
        position: static !important;
        width: 100% !important;
    }

    .tour-detail-page .departure-panel .overview-sidebar-card,
    .tour-detail-page .departure-panel__top,
    .tour-detail-page .departure-panel__list,
    .tour-detail-page .departure-packages {
        width: 100% !important;
    }

    .tour-detail-page .departure-panel__list {
        padding-left: 18px !important;
        padding-right: 18px !important;
    }
}

@media (max-width: 600px) {
    .tour-detail-page .tour-detail-layout {
        width: 100% !important;
        max-width: none !important;
        box-sizing: border-box !important;
        padding: 16px 12px 32px !important;
        gap: 20px !important;
    }

    .tour-detail-page .departure-panel {
        width: 100% !important;
        max-width: none !important;
        overflow: visible !important;
    }

    .tour-detail-page .departure-panel .overview-sidebar-card {
        margin-bottom: 12px !important;
        border-radius: 14px !important;
    }

    .tour-detail-page .departure-panel__top {
        padding: 13px 13px 10px !important;
        border-radius: 14px 14px 0 0 !important;
    }

    .tour-detail-page .departure-panel__list {
        padding: 0 13px !important;
    }

    .tour-detail-page .departure-option {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        padding: 13px 0 !important;
    }

    .tour-detail-page .departure-option__action {
        width: 100% !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
    }

    .tour-detail-page .departure-option__action > strong {
        font-size: 15px !important;
    }

    .tour-detail-page .departure-option__action .storefront-button {
        flex: 1 1 auto !important;
        max-width: 150px !important;
    }

    .tour-detail-page .departure-packages {
        margin-top: 12px !important;
        padding: 13px !important;
        border-radius: 14px !important;
    }

    .tour-detail-page .departure-package {
        padding: 10px !important;
    }
}

@media (max-width: 380px) {
    .tour-detail-page .tour-detail-layout {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .tour-detail-page .departure-option__action {
        align-items: stretch !important;
        flex-direction: column !important;
    }

    .tour-detail-page .departure-option__action .storefront-button {
        width: 100% !important;
        max-width: none !important;
    }

    .tour-detail-page .departure-package {
        align-items: flex-start !important;
        flex-direction: column !important;
    }

    .tour-detail-page .departure-package b {
        align-self: flex-start !important;
    }
}

.tour-detail-page .departure-panel {
    width: 100% !important;
    min-width: 0 !important;
    position: sticky !important;
    top: 78px !important;
    align-self: start !important;
}

.tour-detail-page .departure-panel > * {
    box-sizing: border-box !important;
}

.tour-detail-page .overview-sidebar-card {
    width: 100% !important;
    margin: 0 0 12px !important;
    border: 1px solid #e1e6eb !important;
    border-radius: 16px !important;
    background: #fff !important;
    overflow: hidden !important;
    box-shadow: 0 3px 12px rgba(22,40,58,.06) !important;
}

.tour-detail-page .overview-sidebar-card__head {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-height: 42px !important;
    padding: 10px 12px !important;
    background: #fffdf4 !important;
    border-bottom: 1px solid #eee7d6 !important;
}

.tour-detail-page .overview-sidebar-card__icon,
.tour-detail-page .sidebar-card-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important;
    height: 18px !important;
    flex: 0 0 18px !important;
    border-radius: 50% !important;
    color: #b86a00 !important;
    background: #fff1d7 !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

.tour-detail-page .overview-sidebar-card__head > div {
    min-width: 0 !important;
}

.tour-detail-page .overview-sidebar-card__head strong {
    display: block !important;
    color: #17283a !important;
    font-size: 9px !important;
    line-height: 1.25 !important;
    font-weight: 800 !important;
    letter-spacing: .15px !important;
    white-space: nowrap !important;
}

.tour-detail-page .overview-sidebar-card__head small {
    display: none !important;
}

.tour-detail-page .overview-sidebar-card__button {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: calc(100% - 22px) !important;
    min-height: 32px !important;
    margin: 10px 11px 6px !important;
    padding: 8px 10px !important;
    border-radius: 20px !important;
    background: #f7a719 !important;
    color: #17283a !important;
    font-size: 9px !important;
    line-height: 1.2 !important;
    font-weight: 800 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
}

.tour-detail-page .overview-sidebar-card p {
    margin: 0 10px 10px !important;
    color: #7b8792 !important;
    font-size: 8px !important;
    line-height: 1.3 !important;
    text-align: center !important;
}

.tour-detail-page .sidebar-dates-card {
    width: 100% !important;
    margin: 0 0 12px !important;
    border: 1px solid #e1e6eb !important;
    border-radius: 16px !important;
    background: #fff !important;
    overflow: hidden !important;
    box-shadow: 0 3px 12px rgba(22,40,58,.06) !important;
}

.tour-detail-page .sidebar-dates-card__head {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-height: 42px !important;
    padding: 10px 12px !important;
    background: #fffdf4 !important;
    border-bottom: 1px solid #eee7d6 !important;
}

.tour-detail-page .sidebar-dates-card__head strong {
    color: #17283a !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-dates-card__label {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    padding: 9px 12px 4px !important;
    color: #17283a !important;
}

.tour-detail-page .sidebar-dates-card__label span {
    color: #ef9200 !important;
    font-size: 8px !important;
}

.tour-detail-page .sidebar-dates-card__label strong {
    font-size: 8px !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-dates-card__items {
    padding: 0 12px !important;
}

.tour-detail-page .sidebar-date-item {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto !important;
    gap: 8px !important;
    align-items: center !important;
    padding: 10px 0 !important;
    border-bottom: 1px dashed #dfe4e9 !important;
}

.tour-detail-page .sidebar-date-item:last-child {
    border-bottom: 0 !important;
}

.tour-detail-page .sidebar-date-item__details {
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
}

.tour-detail-page .sidebar-date-item__details strong {
    color: #17283a !important;
    font-size: 11px !important;
    line-height: 1.25 !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-date-item__details span {
    color: #8a96a2 !important;
    font-size: 8px !important;
    line-height: 1.35 !important;
}

.tour-detail-page .sidebar-date-item__details small {
    color: #3b9867 !important;
    font-size: 8px !important;
    line-height: 1.3 !important;
    font-weight: 700 !important;
}

.tour-detail-page .sidebar-date-item__action {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-end !important;
    gap: 6px !important;
    min-width: 0 !important;
}

.tour-detail-page .sidebar-date-item__action > strong {
    color: #ed7b00 !important;
    font-size: 13px !important;
    line-height: 1 !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
}

.tour-detail-page .sidebar-book-date {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 30px !important;
    padding: 7px 11px !important;
    border: 0 !important;
    border-radius: 18px !important;
    background: #ff741b !important;
    color: #fff !important;
    font-size: 8px !important;
    line-height: 1.15 !important;
    font-weight: 800 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
}

.tour-detail-page .sidebar-book-date:hover {
    background: #ed640d !important;
    color: #fff !important;
}

.tour-detail-page .sidebar-dates-card__empty {
    padding: 12px !important;
    text-align: center !important;
}

.tour-detail-page .sidebar-dates-card__empty p {
    margin: 0 0 8px !important;
    color: #788694 !important;
    font-size: 9px !important;
}

.tour-detail-page .sidebar-book-date--full {
    width: 100% !important;
}

.tour-detail-page .sidebar-dates-card__note {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    padding: 8px 10px !important;
    border-top: 1px solid #eee5d3 !important;
    background: #fffdf7 !important;
    color: #9a6b36 !important;
    font-size: 7.5px !important;
    line-height: 1.3 !important;
}

.tour-detail-page .sidebar-dates-card__note > span:first-child {
    color: #a95e18 !important;
    font-size: 6px !important;
}

.tour-detail-page .sidebar-dates-card__note a {
    color: #bd7200 !important;
    font-weight: 800 !important;
    text-decoration: underline !important;
}

.tour-detail-page .sidebar-dates-card__single-action {
    padding: 8px 12px 11px !important;
    background: #fff !important;
}

.tour-detail-page .sidebar-dates-card__single-action .sidebar-book-date {
    width: 100% !important;
    min-height: 31px !important;
    box-sizing: border-box !important;
    gap: 5px !important;
    background: #ff741b !important;
    color: #fff !important;
}

.tour-detail-page .sidebar-dates-card__single-action .sidebar-book-date:hover {
    background: #ed640d !important;
    color: #fff !important;
}

.tour-detail-page .sidebar-pkg-card {
    width: 100% !important;
    border: 1px solid #f0a31a !important;
    border-radius: 16px !important;
    background: #fffdf7 !important;
    overflow: hidden !important;
    box-shadow: 0 3px 12px rgba(22,40,58,.06) !important;
}

.tour-detail-page .sidebar-pkg-card__head {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    min-height: 42px !important;
    padding: 10px 12px !important;
    border-bottom: 1px solid #eee3cf !important;
    background: #fffdf7 !important;
    color: #17283a !important;
}

.tour-detail-page .sidebar-pkg-card__head > span {
    color: #17283a !important;
    font-size: 8px !important;
}

.tour-detail-page .sidebar-pkg-card__head strong {
    font-size: 9px !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-pkg-card__item {
    padding: 14px 12px 12px !important;
    text-align: center !important;
    border-bottom: 1px solid #eee3cf !important;
}

.tour-detail-page .sidebar-pkg-card__badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 7px !important;
    padding: 4px 9px !important;
    border-radius: 20px !important;
    background: #f7a719 !important;
    color: #17283a !important;
    font-size: 7px !important;
    line-height: 1 !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-pkg-card__name {
    display: block !important;
    color: #17283a !important;
    font-size: 10px !important;
    line-height: 1.3 !important;
    font-weight: 800 !important;
}

.tour-detail-page .sidebar-pkg-card__price {
    display: block !important;
    margin-top: 5px !important;
    color: #df8500 !important;
    font-size: 19px !important;
    line-height: 1.05 !important;
    font-weight: 900 !important;
}

.tour-detail-page .sidebar-pkg-card__per {
    display: block !important;
    margin-top: 3px !important;
    color: #8a9199 !important;
    font-size: 8px !important;
}

.tour-detail-page .sidebar-pkg-card__description {
    margin: 7px 0 0 !important;
    color: #7b8792 !important;
    font-size: 8px !important;
    line-height: 1.4 !important;
}

.tour-detail-page .sidebar-pkg-card__book {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    min-height: 32px !important;
    margin-top: 11px !important;
    border-radius: 20px !important;
    background: #f7a719 !important;
    color: #17283a !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    text-decoration: none !important;
}

.tour-detail-page .sidebar-pkg-card__actions {
    display: grid !important;
    gap: 7px !important;
    padding: 10px 12px 12px !important;
    background: #fff !important;
}

.tour-detail-page .sidebar-action {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    min-height: 31px !important;
    border-radius: 18px !important;
    color: #fff !important;
    font-size: 8px !important;
    line-height: 1 !important;
    font-weight: 800 !important;
    text-decoration: none !important;
}

.tour-detail-page .sidebar-action--call {
    background: #ed1644 !important;
}

.tour-detail-page .sidebar-action--whatsapp {
    background: #20c965 !important;
}

@media (max-width: 991px) {
    .tour-detail-page .departure-panel {
        position: static !important;
        top: auto !important;
    }
}

@media (max-width: 600px) {
    .tour-detail-page .departure-panel {
        width: 100% !important;
    }

    .tour-detail-page .sidebar-date-item {
        grid-template-columns: minmax(0, 1fr) auto !important;
        gap: 8px !important;
    }

    .tour-detail-page .sidebar-date-item__action > strong {
        font-size: 14px !important;
    }

    .tour-detail-page .sidebar-book-date {
        min-height: 32px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        font-size: 8px !important;
    }
}

@media (max-width: 420px) {
    .tour-detail-page .sidebar-date-item {
        grid-template-columns: 1fr !important;
    }

    .tour-detail-page .sidebar-date-item__action {
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
    }

    .tour-detail-page .sidebar-date-item__action .sidebar-book-date {
        flex: 0 0 auto !important;
    }
}

.tour-detail-page .departure-panel {
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: visible !important;
}

.tour-detail-page .departure-panel > .overview-sidebar-card,
.tour-detail-page .departure-panel > .sidebar-dates-card,
.tour-detail-page .departure-panel > .sidebar-pkg-card {
    box-sizing: border-box !important;
}

.tour-detail-page .tour-inclusions {
    width: 100% !important;
    margin: 0 0 22px !important;
}

.tour-detail-page .tour-inclusions .tour-section-heading {
    margin: 0 0 14px !important;
    padding: 0 !important;
}

.tour-detail-page .tour-inclusions .tour-section-heading h2 {
    display: inline-block !important;
    position: relative !important;

    margin: 0 !important;
    padding: 0 0 9px !important;

    color: #17283a !important;
    font-family: inherit !important;
    font-size: 16px !important;
    line-height: 1.3 !important;
    font-weight: 800 !important;
}

.tour-detail-page .tour-inclusions .tour-section-heading h2::after {
    content: "" !important;

    position: absolute !important;
    left: 0 !important;
    bottom: 0 !important;

    width: 116px !important;
    height: 2px !important;

    background: #f59a17 !important;
    border-radius: 2px !important;
}

.tour-detail-page .tour-inclusion-columns {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;

    gap: 12px !important;

    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.tour-detail-page .tour-inclusion-card {
    min-width: 0 !important;

    overflow: hidden !important;

    border: 1px solid #dce5ec !important;
    border-radius: 14px !important;

    background: #f8fafc !important;
}

.tour-detail-page .tour-inclusion-card__header {
    display: flex !important;
    align-items: center !important;

    min-height: 38px !important;

    padding: 0 12px !important;

    border-bottom: 1px solid transparent !important;
}

.tour-detail-page .tour-inclusion-card__header strong {
    color: #173b5d !important;

    font-size: 10px !important;
    line-height: 1.3 !important;

    font-weight: 800 !important;
}

.tour-detail-page .tour-inclusion-card--included
.tour-inclusion-card__header {
    background: #ecfbf2 !important;
    border-bottom-color: #ccefd9 !important;
}

.tour-detail-page .tour-inclusion-card--excluded
.tour-inclusion-card__header {
    background: #fff0f2 !important;
    border-bottom-color: #f7d4da !important;
}

.tour-detail-page .tour-inclusion-card__header-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 13px !important;
    height: 13px !important;

    margin-right: 7px !important;

    border-radius: 50% !important;

    font-size: 9px !important;
    line-height: 13px !important;
    font-weight: 900 !important;
}

.tour-detail-page .tour-inclusion-card--included
.tour-inclusion-card__header-icon {
    background: #159447 !important;
    color: #fff !important;
}

.tour-detail-page .tour-inclusion-card--excluded
.tour-inclusion-card__header-icon {
    background: #dc1f45 !important;
    color: #fff !important;
}

.tour-detail-page .tour-inclusion-card__body {
    padding: 9px 11px 10px !important;
}

.tour-detail-page .tour-inclusion-card__body ul {
    display: flex !important;
    flex-direction: column !important;

    gap: 7px !important;

    margin: 0 !important;
    padding: 0 !important;

    list-style: none !important;
}

.tour-detail-page .tour-inclusion-card__body li {
    display: flex !important;
    align-items: flex-start !important;

    gap: 7px !important;

    margin: 0 !important;
    padding: 0 !important;

    color: #476078 !important;

    font-size: 9.5px !important;
    line-height: 1.45 !important;
}

.tour-detail-page .tour-inclusion-card__icon {
    flex: 0 0 11px !important;

    width: 11px !important;
    height: 11px !important;

    margin-top: 1px !important;

    font-size: 9px !important;
    line-height: 11px !important;

    font-weight: 900 !important;

    text-align: center !important;
}

.tour-detail-page .tour-inclusion-card--included
.tour-inclusion-card__icon {
    color: #159447 !important;
}

.tour-detail-page .tour-inclusion-card--excluded
.tour-inclusion-card__icon {
    color: #dc1f45 !important;
}

.tour-detail-page .tour-inclusion-card__text {
    min-width: 0 !important;

    color: #476078 !important;

    font-size: 9.5px !important;
    line-height: 1.45 !important;
}

@media (max-width: 991px) {

    .tour-detail-page .tour-inclusion-columns {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
    }

}

@media (max-width: 600px) {

    .tour-detail-page .tour-inclusion-columns {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }

    .tour-detail-page .tour-inclusion-card {
        border-radius: 12px !important;
    }

    .tour-detail-page .tour-inclusion-card__header {
        min-height: 38px !important;
        padding: 0 11px !important;
    }

    .tour-detail-page .tour-inclusion-card__body {
        padding: 10px !important;
    }

    .tour-detail-page .tour-inclusion-card__body ul {
        gap: 8px !important;
    }

    .tour-detail-page .tour-inclusion-card__body li,
    .tour-detail-page .tour-inclusion-card__text {
        font-size: 11px !important;
        line-height: 1.5 !important;
    }

}

@media (max-width: 380px) {

    .tour-detail-page .tour-inclusion-card__body li,
    .tour-detail-page .tour-inclusion-card__text {
        font-size: 10.5px !important;
    }

}

.tour-detail-page #overview {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.tour-detail-page #overview .overview-about {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.tour-detail-page #overview .overview-about h2 {
    position: relative !important;

    display: inline-block !important;

    margin: 0 0 15px !important;
    padding: 0 0 9px !important;

    color: #17283a !important;

    font-family: inherit !important;

    font-size: 16px !important;
    line-height: 1.3 !important;

    font-weight: 800 !important;

    letter-spacing: 0 !important;
}

.tour-detail-page #overview .overview-about h2::after {
    content: "" !important;

    position: absolute !important;

    left: 0 !important;
    bottom: 0 !important;

    width: 116px !important;
    height: 2px !important;

    background: #f59a17 !important;

    border-radius: 2px !important;
}

.tour-detail-page #overview .overview-about__description {
    margin: 0 0 7px !important;

    color: #38516a !important;

    font-family: inherit !important;

    font-size: 11px !important;
    line-height: 1.7 !important;

    font-weight: 400 !important;
}

.tour-detail-page #overview .overview-about__rich-content {
    margin: 0 !important;

    color: #38516a !important;

    font-family: inherit !important;

    font-size: 11px !important;

    line-height: 1.7 !important;
}

.tour-detail-page #overview .overview-about__rich-content p {
    margin: 0 0 8px !important;

    color: #38516a !important;

    font-size: 11px !important;
    line-height: 1.7 !important;
}

.tour-detail-page #overview .overview-about__rich-content p:last-child {
    margin-bottom: 0 !important;
}

.tour-detail-page #overview .overview-about__rich-content h3,
.tour-detail-page #overview .overview-about__rich-content h4,
.tour-detail-page #overview .overview-about__rich-content h5 {
    margin: 8px 0 5px !important;

    color: #17283a !important;

    font-family: inherit !important;

    font-size: 12px !important;
    line-height: 1.4 !important;

    font-weight: 800 !important;
}

.tour-detail-page #overview .overview-about__rich-content ul,
.tour-detail-page #overview .overview-about__rich-content ol {
    margin: 5px 0 8px !important;
    padding-left: 18px !important;
}

.tour-detail-page #overview .overview-about__rich-content li {
    margin: 3px 0 !important;

    color: #38516a !important;

    font-size: 11px !important;
    line-height: 1.55 !important;
}

.tour-detail-page #overview .overview-highlights {
    width: 100% !important;

    margin: 22px 0 0 !important;
    padding: 0 !important;
}

.tour-detail-page #overview .overview-highlights h2 {
    display: flex !important;
    align-items: center !important;

    gap: 6px !important;

    margin: 0 0 11px !important;
    padding: 0 !important;

    color: #17283a !important;

    font-family: inherit !important;

    font-size: 12px !important;
    line-height: 1.35 !important;

    font-weight: 800 !important;
}

.tour-detail-page #overview .overview-highlights h2 span {
    color: #df8a00 !important;

    font-size: 12px !important;
    line-height: 1 !important;
}

.tour-detail-page #overview .overview-highlights > ul {
    display: grid !important;

    grid-template-columns:
        repeat(4, minmax(0, 1fr)) !important;

    gap: 7px !important;

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    list-style: none !important;
}

.tour-detail-page #overview .overview-highlights > ul > li {
    display: flex !important;

    align-items: flex-start !important;

    gap: 7px !important;

    min-width: 0 !important;

    min-height: 43px !important;

    box-sizing: border-box !important;

    margin: 0 !important;

    padding: 8px 9px !important;

    border: 1px solid #dfe6ed !important;

    border-radius: 6px !important;

    background: #eef3f7 !important;

    color: #173651 !important;

    font-size: 9.5px !important;

    line-height: 1.45 !important;

    font-weight: 400 !important;
}

.tour-detail-page #overview
.overview-highlight__icon {

    flex: 0 0 14px !important;

    width: 14px !important;
    height: 14px !important;

    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    margin-top: 0 !important;

    border-radius: 50% !important;

    background: #159447 !important;

    color: #fff !important;

    font-size: 8px !important;

    line-height: 14px !important;

    font-weight: 900 !important;

    text-align: center !important;
}

.tour-detail-page #overview
.overview-highlight__text {

    min-width: 0 !important;

    color: #173651 !important;

    font-size: 9.5px !important;

    line-height: 1.45 !important;

    font-weight: 400 !important;
}

@media (min-width: 992px) {

    .tour-detail-page .tour-detail-layout {
        max-width: 1120px !important;

        grid-template-columns:
            minmax(0, 1fr) 300px !important;

        gap: 24px !important;
    }

    .tour-detail-page .tour-detail-content {
        min-width: 0 !important;
    }

}

@media (max-width: 991px) {

    .tour-detail-page #overview
    .overview-highlights > ul {

        grid-template-columns:
            repeat(2, minmax(0, 1fr)) !important;

        gap: 8px !important;
    }

}

@media (max-width: 600px) {

    .tour-detail-page #overview
    .overview-about h2 {

        font-size: 16px !important;

        margin-bottom: 14px !important;
    }

    .tour-detail-page #overview
    .overview-about__description,
    .tour-detail-page #overview
    .overview-about__rich-content,
    .tour-detail-page #overview
    .overview-about__rich-content p {

        font-size: 11px !important;

        line-height: 1.7 !important;
    }

    .tour-detail-page #overview
    .overview-highlights {

        margin-top: 20px !important;
    }

    .tour-detail-page #overview
    .overview-highlights > ul {

        grid-template-columns: 1fr !important;

        gap: 7px !important;
    }

    .tour-detail-page #overview
    .overview-highlights > ul > li {

        min-height: 42px !important;

        padding: 9px 10px !important;

        font-size: 11px !important;
    }

    .tour-detail-page #overview
    .overview-highlight__text {

        font-size: 11px !important;

        line-height: 1.5 !important;
    }

}

@media (max-width: 380px) {

    .tour-detail-page #overview
    .overview-highlights > ul > li {

        padding: 8px 9px !important;
    }

}
</style>

<script>
(function () {
    var tabsWrap = document.querySelector('[data-tour-tabs]');
    var panelsWrap = document.querySelector('[data-tour-panels]');

    if (!tabsWrap || !panelsWrap) {
        return;
    }

    var tabs = tabsWrap.querySelectorAll('[data-tour-tab]');
    var panels = panelsWrap.querySelectorAll('[data-tour-panel]');

    function activateTab(name) {
        var found = false;

        tabs.forEach(function (tab) {
            if (tab.getAttribute('data-tour-tab') === name) {
                tab.classList.add('is-active');
                found = true;
            } else {
                tab.classList.remove('is-active');
            }
        });

        if (!found) {
            name = tabs.length ? tabs[0].getAttribute('data-tour-tab') : null;
            tabs.forEach(function (tab) {
                tab.classList.toggle(
                    'is-active',
                    tab.getAttribute('data-tour-tab') === name
                );
            });
        }

        var overviewPanels = [
            'overview',
            'important-notes',
            'terms-conditions',
            'cancellation-policy'
        ];

        panels.forEach(function (panel) {
            var panelName = panel.getAttribute('data-tour-panel');
            var shouldShow = name === 'overview'
                ? overviewPanels.indexOf(panelName) !== -1
                : panelName === name;

            panel.classList.toggle('is-active', shouldShow);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            var name = tab.getAttribute('data-tour-tab');

            activateTab(name);

            if (history.pushState) {
                history.pushState(null, '', '#' + name);
            } else {
                window.location.hash = name;
            }

            panelsWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    var initialHash = window.location.hash ? window.location.hash.substring(1) : '';

    if (initialHash) {
        activateTab(initialHash);
    } else {
        activateTab(tabs.length ? tabs[0].getAttribute('data-tour-tab') : null);
    }
})();
</script>

@endsection
