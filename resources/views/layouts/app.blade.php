<!DOCTYPE html>
<html lang="en">

<head>

    @php
        /*
        |--------------------------------------------------------------------------
        | SETTINGS SERVICE
        |--------------------------------------------------------------------------
        */

        $siteSettings = app(\App\Services\SettingsService::class);


        /*
        |--------------------------------------------------------------------------
        | WEBSITE SETTINGS
        |--------------------------------------------------------------------------
        */

        $siteName = trim(
            (string) $siteSettings->get(
                'site.name',
                config('travels.brand.name', 'Travels')
            )
        );

        $siteTagline = trim(
            (string) $siteSettings->get(
                'site.tagline',
                ''
            )
        );

        $siteDescription = trim(
            (string) $siteSettings->get(
                'site.description',
                'Discover unforgettable travel experiences.'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | FAVICON
        |--------------------------------------------------------------------------
        */

        $favicon = $siteSettings->get(
            'visual.favicon',
            $siteSettings->get('site.favicon', null)
        );

        if ($favicon) {

            if (filter_var($favicon, FILTER_VALIDATE_URL)) {

                $faviconUrl = $favicon;

            } else {

                $faviconUrl = asset(
                    'storage/' . ltrim($favicon, '/')
                );

            }

        } else {

            $faviconUrl = asset('images/favicon.ico');

        }


        /*
        |--------------------------------------------------------------------------
        | VISUAL SETTINGS
        |--------------------------------------------------------------------------
        */

        $visualPrimary = (string) $siteSettings->get(
            'visual.primary_color',
            '#2563eb'
        );

        $visualSecondary = (string) $siteSettings->get(
            'visual.secondary_color',
            '#64748b'
        );

        $visualAccent = (string) $siteSettings->get(
            'visual.accent_color',
            '#f59e0b'
        );

        $visualText = (string) $siteSettings->get(
            'visual.text_color',
            '#111827'
        );

        $visualBackground = (string) $siteSettings->get(
            'visual.background_color',
            '#ffffff'
        );

        $visualContainerWidth = (int) $siteSettings->get(
            'visual.container_width',
            1200
        );

        $visualBorderRadius = (int) $siteSettings->get(
            'visual.border_radius',
            8
        );

        $visualButtonStyle = (string) $siteSettings->get(
            'visual.button_style',
            'rounded'
        );

        $visualCardStyle = (string) $siteSettings->get(
            'visual.card_style',
            'flat'
        );

        $visualThemeMode = (string) $siteSettings->get(
            'visual.theme_mode',
            'light'
        );

        $visualPageLoader = filter_var(
            $siteSettings->get(
                'visual.page_loader',
                true
            ),
            FILTER_VALIDATE_BOOLEAN
        );

        $visualAnimations = filter_var(
            $siteSettings->get(
                'visual.animations',
                true
            ),
            FILTER_VALIDATE_BOOLEAN
        );

        $visualBackToTop = filter_var(
            $siteSettings->get(
                'visual.back_to_top',
                true
            ),
            FILTER_VALIDATE_BOOLEAN
        );


        /*
        |--------------------------------------------------------------------------
        | TYPOGRAPHY SETTINGS
        |--------------------------------------------------------------------------
        */

        $visualBodyFont = trim(
            (string) $siteSettings->get(
                'visual.body_font',
                'Inter'
            )
        );

        $visualHeadingFont = trim(
            (string) $siteSettings->get(
                'visual.heading_font',
                'Poppins'
            )
        );

        $visualNavFont = trim(
            (string) $siteSettings->get(
                'visual.nav_font',
                'Inter'
            )
        );

        $visualButtonFont = trim(
            (string) $siteSettings->get(
                'visual.button_font',
                'Inter'
            )
        );

        $visualBodyWeight = (int) $siteSettings->get(
            'visual.body_weight',
            400
        );

        $visualHeadingWeight = (int) $siteSettings->get(
            'visual.heading_weight',
            700
        );

        $visualFontSize = (float) $siteSettings->get(
            'visual.font_size',
            16
        );

        $visualLineHeight = (float) $siteSettings->get(
            'visual.line_height',
            1.6
        );


        /*
        |--------------------------------------------------------------------------
        | LOGOS
        |--------------------------------------------------------------------------
        */

        $visualLogo = $siteSettings->get(
            'visual.logo',
            null
        );

        $visualLoginLogo = $siteSettings->get(
            'visual.login_logo',
            null
        );


        /*
        |--------------------------------------------------------------------------
        | PAGE TITLE
        |--------------------------------------------------------------------------
        */

        $pageTitle = trim(
            (string) $__env->yieldContent('title')
        );


        /*
        |--------------------------------------------------------------------------
        | META DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $pageDescription = trim(
            (string) $__env->yieldContent('meta_description')
        );

    @endphp


    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >


    {{-- =========================================================
         TITLE
    ========================================================== --}}

    @if($pageTitle)

        <title>
            {{ $pageTitle }}
        </title>

    @elseif($siteTagline)

        <title>
            {{ $siteName }} — {{ $siteTagline }}
        </title>

    @else

        <title>
            {{ $siteName }}
        </title>

    @endif


    {{-- =========================================================
         META DESCRIPTION
    ========================================================== --}}

    <meta
        name="description"
        content="{{ $pageDescription ?: $siteDescription }}"
    >


    {{-- =========================================================
         FAVICON
    ========================================================== --}}

    <link
        rel="icon"
        href="{{ $faviconUrl }}"
    >

    <link
        rel="shortcut icon"
        href="{{ $faviconUrl }}"
    >


    {{-- =========================================================
         HEAD STACK
    ========================================================== --}}

    @stack('head')


    {{-- =========================================================
         VITE
         
         IMPORTANT:
         visual-settings.css first
         app.css second
         
         Page/theme CSS in app.css should be able to
         override generic visual settings.
    ========================================================== --}}

    @vite([
        <!-- 'resources/css/visual-settings.css', -->
        'resources/css/app.css',
        'resources/js/app.js'
    ])


    {{-- =========================================================
         PAGE-SPECIFIC STYLES
    ========================================================== --}}

    @stack('styles')

</head>


<body
    data-site-primary="{{ $visualPrimary }}"
    data-site-secondary="{{ $visualSecondary }}"
    data-site-accent="{{ $visualAccent }}"
    data-site-text="{{ $visualText }}"
    data-site-background="{{ $visualBackground }}"
    data-site-container-width="{{ $visualContainerWidth }}"
    data-site-border-radius="{{ $visualBorderRadius }}"
    data-button-style="{{ $visualButtonStyle }}"
    data-card-style="{{ $visualCardStyle }}"
    data-theme="{{ $visualThemeMode }}"
    data-animations="{{ $visualAnimations ? 'true' : 'false' }}"
    data-page-loader="{{ $visualPageLoader ? 'true' : 'false' }}"
    data-back-to-top="{{ $visualBackToTop ? 'true' : 'false' }}"
>


    {{-- =========================================================
         PAGE LOADER
    ========================================================== --}}

    @if($visualPageLoader)

        <div
            id="site-page-loader"
            aria-hidden="true"
        >
            <div class="site-loader-spinner"></div>
        </div>

    @endif


    {{-- =========================================================
         TOPBAR
    ========================================================== --}}

    @include('components.layout.topbar')


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    @include('components.layout.header')


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}

    <main id="main-content">

        @yield('content')

    </main>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    @include('components.layout.footer')


    {{-- =========================================================
         BACK TO TOP
    ========================================================== --}}

    @if($visualBackToTop)

        <button
            type="button"
            id="site-back-to-top"
            aria-label="Back to top"
        >
            <i class="fas fa-arrow-up"></i>
        </button>

    @endif


    {{-- =========================================================
         VISUAL SETTINGS JS
    ========================================================== --}}

    <script
        src="{{ asset('js/visual-settings.js') }}"
        defer
    ></script>


    {{-- =========================================================
         SCRIPT STACK
    ========================================================== --}}

    @stack('scripts')

</body>

</html>