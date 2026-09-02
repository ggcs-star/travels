<header class="site-header kanila-site-header">

    <div class="container site-header-inner">

        {{-- =====================================================
             LOGO
             ===================================================== --}}
        <a
            href="{{ route('home') }}"
            class="site-logo"
            aria-label="{{ config('travels.brand.name', 'Travels') }}"
        >
            <span class="site-logo-image">
                <img
                    src="{{ asset('images/logo.jpeg') }}"
                    alt="{{ config('travels.brand.name', 'Travels') }}"
                    width="155"
                    height="82"
                    loading="eager"
                >
            </span>
        </a>


        {{-- =====================================================
             DESKTOP NAVIGATION
             ===================================================== --}}
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


            {{-- =================================================
                 PAGES
                 ================================================= --}}
            <!-- <div class="kanila-nav-dropdown"> -->

                <!-- <button
                    type="button"
                    class="nav-dropdown-trigger"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span>Pages</span>
                    <span class="kanila-nav-arrow">⌄</span>
                </button> -->

  <a
                href="{{ route('about') }}"
                class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
            >
                About Us
            </a>

            <!-- </div> -->


            {{-- =================================================
                 DESTINATIONS
                 ================================================= --}}
            <div class="kanila-nav-dropdown">

                <button
                    type="button"
                    class="nav-dropdown-trigger"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span>Destinations</span>
                    <span class="kanila-nav-arrow">⌄</span>
                </button>

                <div class="kanila-dropdown-menu">

                    @forelse(config('travels.tour_categories', []) as $category)

                        <a
                            href="{{ route('tours.index', [
                                'category' => $category['category']
                            ]) }}"
                            class="nav-dropdown-item"
                        >
                            <span>{{ $category['label'] }}</span>
                            <span>→</span>
                        </a>

                    @empty

                        <a
                            href="{{ route('tours.index') }}"
                            class="nav-dropdown-item"
                        >
                            <span>Explore Destinations</span>
                            <span>→</span>
                        </a>

                    @endforelse

                </div>

            </div>


            {{-- =================================================
                 TOURS
                 ================================================= --}}
            <div class="kanila-nav-dropdown">

                <button
                    type="button"
                    class="nav-dropdown-trigger"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span>Tours</span>
                    <span class="kanila-nav-arrow">⌄</span>
                </button>

                <div class="kanila-dropdown-menu">

                    @forelse(config('travels.tour_categories', []) as $category)

                        <a
                            href="{{ route('tours.index', [
                                'category' => $category['category']
                            ]) }}"
                            class="nav-dropdown-item"
                        >
                            <span>{{ $category['label'] }}</span>
                            <span>→</span>
                        </a>

                    @empty

                        <a
                            href="{{ route('tours.index') }}"
                            class="nav-dropdown-item"
                        >
                            <span>Explore Tours</span>
                            <span>→</span>
                        </a>

                    @endforelse


                    <div class="nav-dropdown-divider"></div>


                    <a
                        href="{{ route('tours.index') }}"
                        class="nav-dropdown-all"
                    >
                        <span>View All Tours</span>
                        <span>→</span>
                    </a>

                </div>

            </div>


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


        {{-- =====================================================
             RIGHT SIDE
             ===================================================== --}}
        <div class="kanila-header-actions">

            {{-- ONLY MAIN HEADER CTA --}}
            <a
                href="{{ route('contact') }}"
                class="kanila-get-touch"
            >
                <span class="kanila-get-touch-icon">
                    ✧
                </span>

                <span>
                    Get In Touch
                </span>
            </a>


            {{-- MOBILE MENU --}}
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
         ========================================================= --}}
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
                <span>Home</span>
                <span>→</span>
            </a>


            {{-- ABOUT --}}
            <a
                href="{{ route('about') }}"
                class="{{ request()->routeIs('about') ? 'active' : '' }}"
            >
                <span>About Us</span>
                <span>→</span>
            </a>


            {{-- DESTINATIONS --}}
            <div class="kanila-mobile-group">

                <div class="kanila-mobile-group-title">
                    <span>Destinations</span>
                </div>

                @forelse(config('travels.tour_categories', []) as $category)

                    <a
                        href="{{ route('tours.index', [
                            'category' => $category['category']
                        ]) }}"
                        class="kanila-mobile-sub-link"
                    >
                        <span>{{ $category['label'] }}</span>
                        <span>→</span>
                    </a>

                @empty

                    <a
                        href="{{ route('tours.index') }}"
                        class="kanila-mobile-sub-link"
                    >
                        <span>Explore Destinations</span>
                        <span>→</span>
                    </a>

                @endforelse

            </div>


            {{-- TOURS --}}
            <div class="kanila-mobile-group">

                <div class="kanila-mobile-group-title">
                    <span>Tours</span>
                </div>

                @forelse(config('travels.tour_categories', []) as $category)

                    <a
                        href="{{ route('tours.index', [
                            'category' => $category['category']
                        ]) }}"
                        class="kanila-mobile-sub-link"
                    >
                        <span>{{ $category['label'] }}</span>
                        <span>→</span>
                    </a>

                @empty

                    <a
                        href="{{ route('tours.index') }}"
                        class="kanila-mobile-sub-link"
                    >
                        <span>Explore Tours</span>
                        <span>→</span>
                    </a>

                @endforelse


                <a
                    href="{{ route('tours.index') }}"
                    class="kanila-mobile-sub-link kanila-mobile-sub-link--all"
                >
                    <span>View All Tours</span>
                    <span>→</span>
                </a>

            </div>


            {{-- BLOG --}}
            <a
                href="{{ route('blog.index') }}"
                class="{{ request()->routeIs('blog.*') ? 'active' : '' }}"
            >
                <span>Blog</span>
                <span>→</span>
            </a>


            {{-- CONTACT --}}
            <a
                href="{{ route('contact') }}"
                class="{{ request()->routeIs('contact') ? 'active' : '' }}"
            >
                <span>Contact Us</span>
                <span>→</span>
            </a>


            {{-- GET IN TOUCH --}}
            <a
                href="{{ route('contact') }}"
                class="kanila-mobile-touch"
            >
                Get In Touch →
            </a>

        </nav>

    </div>

</header>