@php
    $settingsService = app(\App\Services\SettingsService::class);
    $settings = $settingsService->all() ?: [];

    $siteName = trim((string) ($settings['site.name'] ?? 'SSB Travelz'));

    $footerEnabled = true;

    if (array_key_exists('footer.enabled', $settings)) {
        $footerEnabled = filter_var(
            $settings['footer.enabled'],
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        // Only an explicit false disables the footer.
        $footerEnabled = $footerEnabled !== false;
    }

    $footerLogo = $settings['footer.logo']
        ?? $settings['visual.logo']
        ?? $settings['header.logo']
        ?? null;

    $footerLogoUrl = $footerLogo
        ? (filter_var($footerLogo, FILTER_VALIDATE_URL)
            ? $footerLogo
            : asset('storage/' . ltrim($footerLogo, '/')))
        : asset('images/logo.jpeg');

    $footerLogoAlt = trim((string) (
        $settings['footer.logo_alt']
        ?? $settings['header.logo_alt']
        ?? $siteName
    ));

    $footerDescription = trim((string) (
        $settings['footer.description']
        ?? 'Based in Hyderabad, SSB Travelz crafts personalised journeys across India and the world – spiritual pilgrimages, family holidays, school excursions and corporate trips, all with faith and care.'
    ));

    $footerCtaEnabled = array_key_exists('footer.cta_enabled', $settings)
        ? (bool) $settings['footer.cta_enabled']
        : true;

    $footerCtaTitle = trim((string) (
        $settings['footer.cta_title']
        ?? 'Ready to Travel With Faith?'
    ));

    $footerCtaDescription = trim((string) (
        $settings['footer.cta_description']
        ?? 'Tell us your destination and dates – we will craft the perfect journey for you.'
    ));

    $footerCtaButtonText = trim((string) (
        $settings['footer.cta_button_text']
        ?? 'Plan My Trip'
    ));

    $footerCtaButtonUrl = trim((string) (
        $settings['footer.cta_button_url']
        ?? url('/contact')
    ));

    $ctaBadges = !empty($settings['footer.cta_badges']) ? $settings['footer.cta_badges'] : [
        ['text' => 'Free Quote', 'icon' => '✓', 'enabled' => true],
        ['text' => 'Custom Itinerary', 'icon' => '✓', 'enabled' => true],
        ['text' => '24-hr Response', 'icon' => '✓', 'enabled' => true],
    ];

    $socials = !empty($settings['footer.socials']) ? $settings['footer.socials'] : [
        ['name' => 'Facebook', 'icon' => 'fa-brands fa-facebook-f', 'url' => '#', 'enabled' => true],
        ['name' => 'YouTube', 'icon' => 'fa-brands fa-youtube', 'url' => '#', 'enabled' => true],
        ['name' => 'Instagram', 'icon' => 'fa-brands fa-instagram', 'url' => '#', 'enabled' => true],
        ['name' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'url' => '#', 'enabled' => true],
    ];

    $footerColumns = !empty($settings['footer.columns']) ? $settings['footer.columns'] : [
        [
            'title' => 'TOUR PACKAGES',
            'enabled' => true,
            'links' => [
                ['label' => 'Spiritual & Pilgrimage', 'icon' => 'fa-solid fa-hands-praying', 'url' => '#', 'enabled' => true],
                ['label' => 'Holidays', 'icon' => 'fa-solid fa-umbrella-beach', 'url' => '#', 'enabled' => true],
                ['label' => 'School & College', 'icon' => 'fa-solid fa-person-hiking', 'url' => '#', 'enabled' => true],
                ['label' => 'Business Trips', 'icon' => 'fa-solid fa-briefcase', 'url' => '#', 'enabled' => true],
                ['label' => 'Monthly Tours', 'icon' => 'fa-solid fa-repeat', 'url' => '#', 'enabled' => true],
                ['label' => 'View All Packages', 'icon' => 'fa-solid fa-plane', 'url' => '#', 'enabled' => true],
            ],
        ],
        [
            'title' => 'QUICK LINKS',
            'enabled' => true,
            'links' => [
                ['label' => 'About Us', 'icon' => 'fa-solid fa-circle-info', 'url' => url('/about'), 'enabled' => true],
                ['label' => 'Travel Blog', 'icon' => 'fa-solid fa-pen-nib', 'url' => url('/blog'), 'enabled' => true],
                ['label' => 'Contact Us', 'icon' => 'fa-solid fa-headset', 'url' => url('/contact'), 'enabled' => true],
                ['label' => 'Get Free Quote', 'icon' => 'fa-solid fa-file-lines', 'url' => url('/contact'), 'enabled' => true],
                ['label' => 'Become a Partner', 'icon' => 'fa-solid fa-handshake', 'url' => url('/contact'), 'enabled' => true],
                ['label' => 'Terms & Conditions', 'icon' => 'fa-solid fa-scale-balanced', 'url' => '/terms', 'enabled' => true],
                ['label' => 'Privacy Policy', 'icon' => 'fa-solid fa-shield-halved', 'url' => '/privacy', 'enabled' => true],
            ],
        ],
    ];

    $footerContactTitle = trim((string) (
        $settings['footer.contact_title'] ?? 'GET IN TOUCH'
    ));

    $footerContacts = !empty($settings['footer.contacts']) ? $settings['footer.contacts'] : [
        ['label' => 'PHONE / WHATSAPP', 'value' => '+91-9182498843, +91-9014534878', 'url' => 'tel:+919182498843', 'icon' => 'fa-solid fa-phone', 'enabled' => true],
        ['label' => 'EMAIL US', 'value' => 'bookings@ssbtravelz.com', 'url' => 'mailto:bookings@ssbtravelz.com', 'icon' => 'fa-solid fa-envelope', 'enabled' => true],
        ['label' => 'OUR OFFICE', 'value' => 'Hyderabad, Telangana, India', 'url' => '', 'icon' => 'fa-solid fa-location-dot', 'enabled' => true],
        ['label' => 'REGISTERED ADDRESS', 'value' => '32-83/2, SN 14 Sainik Nagar, Ramanakrishna Puram, Hyderabad - 500056', 'url' => '', 'icon' => 'fa-solid fa-building', 'enabled' => true],
        ['label' => 'BRANCH OFFICE', 'value' => 'Near Kamineni Hospital Bypass Rd, beside HP petrol bunk, Sivaganga Colony, LB Nagar to Nagol Rd, Hyderabad, Telangana, 500074 India', 'url' => '', 'icon' => 'fa-solid fa-location-dot', 'enabled' => true],
        ['label' => 'WORKING HOURS', 'value' => 'Mon–Sat · 9am–7pm', 'url' => '', 'icon' => 'fa-solid fa-clock', 'enabled' => true],
    ];

    $trustBadges = !empty($settings['footer.trust_badges']) ? $settings['footer.trust_badges'] : [
        ['title' => '4.9 Google Rating', 'subtitle' => '', 'icon' => 'fa-solid fa-star', 'enabled' => true],
        ['title' => 'IATA Affiliated', 'subtitle' => '', 'icon' => 'fa-solid fa-circle-check', 'enabled' => true],
        ['title' => 'Secure Payments', 'subtitle' => '', 'icon' => 'fa-solid fa-lock', 'enabled' => true],
    ];

    $destinationsTitle = trim((string) ($settings['footer.destinations_title'] ?? ''));
    if ($destinationsTitle === '') {
        $destinationsTitle = 'POPULAR DESTINATIONS';
    }

    $destinations = !empty($settings['footer.destinations']) ? $settings['footer.destinations'] : [
        ['label' => 'Varanasi', 'url' => '#', 'enabled' => true],
        ['label' => 'Ayodhya', 'url' => '#', 'enabled' => true],
        ['label' => 'Char Dham', 'url' => '#', 'enabled' => true],
        ['label' => 'Prayagraj', 'url' => '#', 'enabled' => true],
        ['label' => 'Shirdi', 'url' => '#', 'enabled' => true],
        ['label' => 'Dubai', 'url' => '#', 'enabled' => true],
        ['label' => 'Sri Lanka', 'url' => '#', 'enabled' => true],
        ['label' => 'Hong Kong', 'url' => '#', 'enabled' => true],
        ['label' => 'Muktinath', 'url' => '#', 'enabled' => true],
        ['label' => 'Tirupati', 'url' => '#', 'enabled' => true],
    ];

    $bottomLinks = !empty($settings['footer.bottom_links']) ? $settings['footer.bottom_links'] : [
        ['label' => 'Terms & Conditions', 'url' => '/terms', 'enabled' => true],
        ['label' => 'Privacy Policy', 'url' => '/privacy', 'enabled' => true],
    ];

    $footerCopyright = trim((string) ($settings['footer.copyright_text'] ?? ''));
    if ($footerCopyright === '') {
        $footerCopyright = '© ' . date('Y') . ' ' . $siteName . ' · Tourism With Faith · All rights reserved.';
    }

    $showCrafted = array_key_exists('footer.show_crafted', $settings)
        ? (bool) $settings['footer.show_crafted']
        : true;

    $craftedText = trim((string) ($settings['footer.crafted_text'] ?? ''));
    if ($craftedText === '') {
        $craftedText = 'Crafted with ♥ in Hyderabad';
    }

    $footerUrl = function ($url) {
        $url = trim((string) $url);

        if (!$url) {
            return '#';
        }

        if (
            filter_var($url, FILTER_VALIDATE_URL)
            || str_starts_with($url, 'tel:')
            || str_starts_with($url, 'mailto:')
        ) {
            return $url;
        }

        return url($url);
    };

    /*
    |--------------------------------------------------------------------------
    | CMS FOOTER PAGES
    |--------------------------------------------------------------------------
    | Page CMS uses menu_location instead of show_in_footer.
    */
    $footerPages = collect();

    if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
        $footerPages = \App\Models\Page::query()
            ->published()
            ->whereIn('menu_location', ['footer', 'both'])
            ->orderBy('footer_position')
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'slug',
                'footer_column',
                'footer_position',
            ]);
    }

    $socialClass = function ($name) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string) $name));
        return match (true) {
            str_contains($slug, 'facebook') => 'social-facebook',
            str_contains($slug, 'youtube') => 'social-youtube',
            str_contains($slug, 'instagram') => 'social-instagram',
            str_contains($slug, 'whatsapp') => 'social-whatsapp',
            default => 'social-custom',
        };
    };
@endphp

@if($footerEnabled)
<footer class="site-footer" style="display:block !important; visibility:visible !important; opacity:1 !important; width:100% !important; position:relative !important; z-index:99999 !important;">

    @if($footerCtaEnabled)
        <section class="footer-cta-section">
            <div class="container">
                <div class="footer-cta">
                    <div class="footer-cta-content">
                        @if(!empty($ctaBadges))
                            <div class="footer-cta-badges">
                                @foreach($ctaBadges as $badge)
                                    @if(!empty($badge['enabled']) && !empty($badge['text']))
                                        <span>{{ $badge['icon'] ?? '✓' }} {{ $badge['text'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if($footerCtaTitle)
                            <h2>{{ $footerCtaTitle }}</h2>
                        @endif

                        @if($footerCtaDescription)
                            <p>{{ $footerCtaDescription }}</p>
                        @endif
                    </div>

                    <div class="footer-cta-actions">
                        @if($footerCtaButtonText)
                            <a href="{{ $footerUrl($footerCtaButtonUrl) }}" class="footer-plan-button">
                                {{ $footerCtaButtonText }}
                            </a>
                        @endif

                        @foreach($footerContacts as $contact)
                            @if(!empty($contact['enabled']) && !empty($contact['value']) && str_starts_with((string)($contact['url'] ?? ''), 'tel:'))
                                <a href="{{ $footerUrl($contact['url']) }}" class="footer-call-button">
                                    <span><i class="{{ $contact['icon'] ?? 'fa-solid fa-phone' }}"></i></span>
                                    {{ $contact['value'] }}
                                </a>
                                @break
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="footer-main">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-column footer-company">
                    <a href="{{ route('home') }}" class="footer-logo">
                        <img src="{{ $footerLogoUrl }}" alt="{{ $footerLogoAlt }}">
                    </a>

                    @if($footerDescription)
                        <p class="footer-description">{{ $footerDescription }}</p>
                    @endif

                    @if(!empty($socials))
                        <div class="footer-social">
                            @foreach($socials as $social)
                                @if(!empty($social['enabled']) && !empty($social['url']) && !empty($social['name']))
                                    <a href="{{ $footerUrl($social['url']) }}"
                                       class="{{ $socialClass($social['name']) }}"
                                       aria-label="{{ $social['name'] }}"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        <i class="{{ $social['icon'] ?? 'fa-solid fa-arrow-up-right' }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($trustBadges))
                        <div class="footer-badges">
                            @foreach($trustBadges as $badge)
                                @if(!empty($badge['enabled']) && !empty($badge['title']))
                                    <span>
                                        <i class="{{ $badge['icon'] ?? 'fa-solid fa-circle-check' }}"></i>
                                        {{ $badge['title'] }}
                                        @if(!empty($badge['subtitle']))
                                            · {{ $badge['subtitle'] }}
                                        @endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @foreach($footerColumns as $column)
                    @if(!empty($column['enabled']) && (!empty($column['title']) || !empty($column['links'])))
                        <div class="footer-column">
                            @if(!empty($column['title']))
                                <h3><span></span>{{ $column['title'] }}</h3>
                            @endif

                            @if(!empty($column['links']))
                                <ul>
                                    @foreach($column['links'] as $link)
                                        @if(!empty($link['enabled']) && !empty($link['label']))
                                            <li>
                                                <a href="{{ $footerUrl($link['url'] ?? '#') }}">
                                                    @if(!empty($link['icon']))<i class="{{ $link['icon'] }}"></i> @endif{{ $link['label'] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                @endforeach

                @if($footerPages->isNotEmpty())
                    <div class="footer-column">
                        <h3><span></span>PAGES</h3>
                        <ul>
                            @foreach($footerPages as $page)
                                <li>
                                    <a href="{{ $page->url }}">
                                        {{ $page->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="footer-column footer-contact">
                    <h3><span></span>{{ $footerContactTitle }}</h3>

                    @foreach($footerContacts as $contact)
                        @if(!empty($contact['enabled']) && !empty($contact['value']))
                            <div class="footer-contact-item">
                                <div class="footer-contact-icon"><i class="{{ $contact['icon'] ?? 'fa-solid fa-circle' }}"></i></div>
                                <div>
                                    @if(!empty($contact['label']))
                                        <small>{{ $contact['label'] }}</small>
                                    @endif

                                    @if(!empty($contact['url']))
                                        <a href="{{ $footerUrl($contact['url']) }}">
                                            <strong>{{ $contact['value'] }}</strong>
                                        </a>
                                    @else
                                        <strong>{{ $contact['value'] }}</strong>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    @if(!empty($destinations))
        <section class="footer-destinations">
            <div class="container">
                <div class="footer-destination-inner">
                    <div class="footer-destination-title">
                        <span><i class="fa-solid fa-location-dot"></i></span>
                        {{ $destinationsTitle }}
                    </div>

                    <div class="footer-destination-list">
                        @foreach($destinations as $destination)
                            @if(!empty($destination['enabled']) && !empty($destination['label']))
                                <a href="{{ $footerUrl($destination['url'] ?? '#') }}">
                                    {{ $destination['label'] ?? $destination['name'] ?? '' }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">

                @if($footerCopyright)
                    <p>{!! $footerCopyright !!}</p>
                @endif

                @if(!empty($bottomLinks) || $showCrafted)
                    <div class="footer-bottom-links">
                        @foreach($bottomLinks as $link)
                            @if(!empty($link['enabled']) && !empty($link['label']))
                                <a href="{{ $footerUrl($link['url'] ?? '#') }}">
                                    {{ $link['label'] }}
                                </a>
                                <span>·</span>
                            @endif
                        @endforeach

                        @if($showCrafted && $craftedText)
                            <span>{{ $craftedText }}</span>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </section>

</footer>
@endif
