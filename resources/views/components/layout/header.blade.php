@php
    /*
    |--------------------------------------------------------------------------
    | HEADER SETTINGS
    |--------------------------------------------------------------------------
    */

    $settings = app(\App\Services\SettingsService::class);

    $headerEnabled = $settings->get(
        'header.enabled',
        true
    );

$headerLogo = $settings->get(
    'visual.logo',
    $settings->get(
        'header.logo',
        null
    )
);

    $headerLogoAlt = $settings->get(
        'header.logo_alt',
        $settings->get(
            'site.name',
            config('travels.brand.name', 'Travels')
        )
    );

    $headerCtaText = $settings->get(
        'header.cta_text',
        'Get In Touch'
    );

    $headerCtaUrl = $settings->get(
        'header.cta_url',
        route('contact')
    );


    /*
    |--------------------------------------------------------------------------
    | HEADER LOGO URL
    |--------------------------------------------------------------------------
    */

    if ($headerLogo) {

        /*
         * External URL
         * Example:
         * https://example.com/logo.png
         */
        if (filter_var($headerLogo, FILTER_VALIDATE_URL)) {

            $headerLogoUrl = $headerLogo;

        /*
         * Uploaded storage file
         * Example:
         * settings/logo/abc.webp
         */
        } elseif (str_starts_with(
            ltrim($headerLogo, '/'),
            'settings/'
        )) {

            $headerLogoUrl = asset(
                'storage/' . ltrim($headerLogo, '/')
            );

        /*
         * Already contains storage/
         */
        } elseif (str_starts_with(
            ltrim($headerLogo, '/'),
            'storage/'
        )) {

            $headerLogoUrl = asset(
                ltrim($headerLogo, '/')
            );

        /*
         * Existing public file
         * Example:
         * images/travel_logo.png
         */
        } else {

            $headerLogoUrl = asset(
                ltrim($headerLogo, '/')
            );
        }

    } else {

        /*
         * Default existing logo
         */
        $headerLogoUrl = asset('images/travel_logo_white.png');
    }


    /*
    |--------------------------------------------------------------------------
    | HEADER DATABASE DATA
    |--------------------------------------------------------------------------
    */

    $headerCategories = \App\Models\TourCategory::query()
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'slug',
        ]);

    /*
    |--------------------------------------------------------------------------
    | CMS HEADER PAGES
    |--------------------------------------------------------------------------
    |
    | Only published pages explicitly assigned to the header are shown.
    | Header parent/child relationships are respected.
    |
    */
    $headerPages = collect();

    if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
        $headerPages = \App\Models\Page::query()
            ->published()
            ->whereIn('menu_location', ['header', 'both'])
            ->whereNull('header_parent_id')
            ->with([
                'headerChildren' => function ($query) {
                    $query
                        ->published()
                        ->whereIn('menu_location', ['header', 'both'])
                        ->orderBy('header_position')
                        ->orderBy('title');
                },
            ])
            ->orderBy('header_position')
            ->orderBy('title')
            ->get();
    }
@endphp


@if($headerEnabled)

<header class="site-header kanila-site-header {{ request()->routeIs('blog.*') ? 'site-header--blog' : '' }}">

    <div class="container site-header-inner">


        {{-- =========================================================
             LOGO
        ========================================================== --}}

        <a
            href="{{ route('home') }}"
            class="site-logo"
            aria-label="{{ $headerLogoAlt }}"
        >

            <span class="site-logo-image">

                <img
                    src="{{ $headerLogoUrl }}"
                    alt="{{ $headerLogoAlt }}"
                    width="155"
                    height="82"
                    loading="eager"
                >

            </span>

        </a>


        {{-- =========================================================
             DESKTOP NAVIGATION
        ========================================================== --}}

        <nav
            class="kanila-navigation"
            aria-label="Primary navigation"
        >

            {{-- HOME --}}

            <a
                href="{{ route('home') }}"
                class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
            >
                Home
            </a>


            {{-- ABOUT US --}}

            <a
                href="{{ route('about') }}"
                class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
            >
                About Us
            </a>


            {{-- DESTINATIONS --}}

            <div class="kanila-nav-dropdown">

                <button
                    type="button"
                    class="nav-dropdown-trigger"
                    aria-expanded="false"
                    aria-haspopup="true"
                >

                    <span>
                        Destinations
                    </span>

                    <span class="kanila-nav-arrow">
                        ⌄
                    </span>

                </button>


                <div class="kanila-dropdown-menu">

                    @forelse($headerCategories as $category)

                        <a
                            href="{{ route('tours.index', [
                                'category' => $category->slug
                            ]) }}"
                            class="nav-dropdown-item"
                        >

                            <span>
                                {{ $category->name }}
                            </span>

                            <span>
                                →
                            </span>

                        </a>

                    @empty

                        <a
                            href="{{ route('tours.index') }}"
                            class="nav-dropdown-item"
                        >

                            <span>
                                Explore Destinations
                            </span>

                            <span>
                                →
                            </span>

                        </a>

                    @endforelse

                </div>

            </div>


            {{-- TOURS --}}

            <div class="kanila-nav-dropdown">

                <button
                    type="button"
                    class="nav-dropdown-trigger"
                    aria-expanded="false"
                    aria-haspopup="true"
                >

                    <span>
                        Tours
                    </span>

                    <span class="kanila-nav-arrow">
                        ⌄
                    </span>

                </button>


                <div class="kanila-dropdown-menu">

                    @forelse($headerCategories as $category)

                        <a
                            href="{{ route('tours.index', [
                                'category' => $category->slug
                            ]) }}"
                            class="nav-dropdown-item"
                        >

                            <span>
                                {{ $category->name }}
                            </span>

                            <span>
                                →
                            </span>

                        </a>

                    @empty

                        <a
                            href="{{ route('tours.index') }}"
                            class="nav-dropdown-item"
                        >

                            <span>
                                Explore Tours
                            </span>

                            <span>
                                →
                            </span>

                        </a>

                    @endforelse


                    {{-- VIEW ALL TOURS --}}

                    <div class="nav-dropdown-divider"></div>

                    <a
                        href="{{ route('tours.index') }}"
                        class="nav-dropdown-all"
                    >

                        <span>
                            View All Tours
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                </div>

            </div>


            {{-- CMS PAGES --}}

            @foreach($headerPages as $page)
                @if($page->headerChildren->isNotEmpty())
                    <div class="kanila-nav-dropdown">

                        <button
                            type="button"
                            class="nav-dropdown-trigger"
                            aria-expanded="false"
                            aria-haspopup="true"
                        >
                            <span>{{ $page->title }}</span>

                            <span class="kanila-nav-arrow">
                                ⌄
                            </span>
                        </button>

                        <div class="kanila-dropdown-menu">

                            <a
                                href="{{ $page->url }}"
                                class="nav-dropdown-item"
                            >
                                <span>{{ $page->title }}</span>
                                <span>→</span>
                            </a>

                            <div class="nav-dropdown-divider"></div>

                            @foreach($page->headerChildren as $child)
                                <a
                                    href="{{ $child->url }}"
                                    class="nav-dropdown-item"
                                >
                                    <span>{{ $child->title }}</span>
                                    <span>→</span>
                                </a>
                            @endforeach

                        </div>
                    </div>
                @else
                    <a
                        href="{{ $page->url }}"
                        class="nav-link {{ request()->is(ltrim($page->slug, '/')) ? 'active' : '' }}"
                    >
                        {{ $page->title }}
                    </a>
                @endif
            @endforeach


            {{-- BLOG --}}

            <a
                href="{{ route('blog.index') }}"
                class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}"
            >
                Blog
            </a>


            {{-- CONTACT --}}

            <a
                href="{{ route('contact') }}"
                class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
            >
                Contact Us
            </a>

        </nav>


        {{-- =========================================================
             RIGHT SIDE
        ========================================================== --}}

        <div class="kanila-header-actions">


            {{-- GET IN TOUCH --}}

            <a
                href="{{ $headerCtaUrl }}"
                class="kanila-get-touch"
            >

                <span class="kanila-get-touch-icon">
                    ✧
                </span>

                <span>
                    {{ $headerCtaText }}
                </span>

            </a>


            {{-- MOBILE MENU TOGGLE --}}

            <button
                type="button"
                class="mobile-menu-toggle"
                aria-label="Open navigation"
                aria-expanded="false"
                aria-controls="kanila-mobile-navigation"
            >

                <span></span>
                <span></span>
                <span></span>

            </button>

        </div>

    </div>


    {{-- =========================================================
         MOBILE NAVIGATION
    ========================================================== --}}

    <div
        class="kanila-mobile-navigation"
        id="kanila-mobile-navigation"
    >

        <nav aria-label="Mobile navigation">


            {{-- HOME --}}

            <a
                href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'active' : '' }}"
            >

                <span>
                    Home
                </span>

                <span>
                    →
                </span>

            </a>


            {{-- ABOUT --}}

            <a
                href="{{ route('about') }}"
                class="{{ request()->routeIs('about') ? 'active' : '' }}"
            >

                <span>
                    About Us
                </span>

                <span>
                    →
                </span>

            </a>


            {{-- DESTINATIONS --}}

            <div class="kanila-mobile-group">

                <button
                    type="button"
                    class="kanila-mobile-group-title"
                    aria-expanded="false"
                >
                    <span>
                        Destinations
                    </span>
                    <span class="kanila-mobile-group-arrow" aria-hidden="true">›</span>
                </button>


                @forelse($headerCategories as $category)

                    <a
                        href="{{ route('tours.index', [
                            'category' => $category->slug
                        ]) }}"
                        class="kanila-mobile-sub-link"
                    >

                        <span>
                            {{ $category->name }}
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                @empty

                    <a
                        href="{{ route('tours.index') }}"
                        class="kanila-mobile-sub-link"
                    >

                        <span>
                            Explore Destinations
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                @endforelse

            </div>


            {{-- TOURS --}}

            <div class="kanila-mobile-group">

                <button
                    type="button"
                    class="kanila-mobile-group-title"
                    aria-expanded="false"
                >
                    <span>
                        Tours
                    </span>
                    <span class="kanila-mobile-group-arrow" aria-hidden="true">›</span>
                </button>


                @forelse($headerCategories as $category)

                    <a
                        href="{{ route('tours.index', [
                            'category' => $category->slug
                        ]) }}"
                        class="kanila-mobile-sub-link"
                    >

                        <span>
                            {{ $category->name }}
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                @empty

                    <a
                        href="{{ route('tours.index') }}"
                        class="kanila-mobile-sub-link"
                    >

                        <span>
                            Explore Tours
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                @endforelse


                {{-- VIEW ALL TOURS --}}

                <a
                    href="{{ route('tours.index') }}"
                    class="kanila-mobile-sub-link kanila-mobile-sub-link--all"
                >

                    <span>
                        View All Tours
                    </span>

                    <span>
                        →
                    </span>

                </a>

            </div>


            {{-- CMS PAGES --}}

            @foreach($headerPages as $page)

                @if($page->headerChildren->isNotEmpty())

                    <div class="kanila-mobile-group">

                        <button
                            type="button"
                            class="kanila-mobile-group-title"
                            aria-expanded="false"
                        >
                            <span>{{ $page->title }}</span>
                            <span class="kanila-mobile-group-arrow" aria-hidden="true">›</span>
                        </button>

                        <a
                            href="{{ $page->url }}"
                            class="kanila-mobile-sub-link"
                        >
                            <span>{{ $page->title }}</span>
                            <span>→</span>
                        </a>

                        @foreach($page->headerChildren as $child)
                            <a
                                href="{{ $child->url }}"
                                class="kanila-mobile-sub-link"
                            >
                                <span>{{ $child->title }}</span>
                                <span>→</span>
                            </a>
                        @endforeach

                    </div>

                @else

                    <a
                        href="{{ $page->url }}"
                        class="{{ request()->is(ltrim($page->slug, '/')) ? 'active' : '' }}"
                    >
                        <span>
                            {{ $page->title }}
                        </span>

                        <span>
                            →
                        </span>
                    </a>

                @endif

            @endforeach


            {{-- BLOG --}}

            <a
                href="{{ route('blog.index') }}"
                class="{{ request()->routeIs('blog.*') ? 'active' : '' }}"
            >

                <span>
                    Blog
                </span>

                <span>
                    →
                </span>

            </a>


            {{-- CONTACT --}}

            <a
                href="{{ route('contact') }}"
                class="{{ request()->routeIs('contact') ? 'active' : '' }}"
            >

                <span>
                    Contact Us
                </span>

                <span>
                    →
                </span>

            </a>


            {{-- GET IN TOUCH --}}

            <a
                href="{{ $headerCtaUrl }}"
                class="kanila-mobile-touch"
            >
                {{ $headerCtaText }} →
            </a>

        </nav>

    </div>

</header>

{{-- =========================================================
     MOBILE NAVIGATION ACCORDION
========================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const mobileNavigation = document.getElementById('kanila-mobile-navigation');

    if (!mobileNavigation) {
        return;
    }

    const groups = mobileNavigation.querySelectorAll('.kanila-mobile-group');

    groups.forEach(function (group) {

        const trigger = group.querySelector('.kanila-mobile-group-title');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const willOpen = !group.classList.contains('is-expanded');

            /* Close all other accordion groups */
            groups.forEach(function (otherGroup) {
                if (otherGroup !== group) {
                    otherGroup.classList.remove('is-expanded');

                    const otherTrigger = otherGroup.querySelector('.kanila-mobile-group-title');

                    if (otherTrigger) {
                        otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            /* Toggle current group */
            group.classList.toggle('is-expanded', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    /* Close accordion after selecting a submenu link */
    mobileNavigation.querySelectorAll('.kanila-mobile-sub-link').forEach(function (link) {
        link.addEventListener('click', function () {
            groups.forEach(function (group) {
                group.classList.remove('is-expanded');

                const trigger = group.querySelector('.kanila-mobile-group-title');

                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });

    /* Reset accordion when the mobile menu itself closes */
    const menuToggle = document.querySelector('.kanila-site-header .mobile-menu-toggle');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

            if (!isExpanded) {
                groups.forEach(function (group) {
                    group.classList.remove('is-expanded');

                    const trigger = group.querySelector('.kanila-mobile-group-title');

                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    }
});
</script>

@endif