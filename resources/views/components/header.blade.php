<header class="site-header">
    <div class="container header-inner">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="site-logo" aria-label="Travels Home">
            <span class="site-logo-mark">T</span>
            <span class="site-logo-text">Travels</span>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="main-navigation" aria-label="Main navigation">
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                Home
            </a>

            <a href="{{ route('tours.index') }}" class="nav-link {{ request()->is('tours*') ? 'active' : '' }}">
                Tours
            </a>

            <a href="{{ url('/destinations') }}" class="nav-link {{ request()->is('destinations*') ? 'active' : '' }}">
                Destinations
            </a>

            <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}">
                About Us
            </a>

            <a href="{{ url('/contact') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">
                Contact
            </a>
        </nav>

        {{-- Header Actions --}}
        <div class="header-actions">

            <a href="tel:+919999999999" class="header-phone">
                <span class="header-phone-icon" aria-hidden="true">
                    ☎
                </span>

                <span>
                    <small>Need help?</small>
                    <strong>+91 99999 99999</strong>
                </span>
            </a>

            <a href="{{ url('/contact') }}" class="header-cta">
                Plan Your Trip
                <span aria-hidden="true">→</span>
            </a>

            {{-- Mobile Menu Button --}}
            <button
                type="button"
                class="mobile-menu-toggle"
                id="mobileMenuToggle"
                aria-label="Open navigation"
                aria-controls="mobileNavigation"
                aria-expanded="false"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>

    </div>

    {{-- Mobile Navigation --}}
    <div class="mobile-navigation" id="mobileNavigation">

        <nav aria-label="Mobile navigation">

            <a href="{{ url('/') }}">
                Home
            </a>

            <a href="{{ route('tours.index') }}">
                Tours
            </a>

            <a href="{{ url('/destinations') }}">
                Destinations
            </a>

            <a href="{{ url('/about') }}">
                About Us
            </a>

            <a href="{{ url('/contact') }}">
                Contact
            </a>

            <a href="{{ url('/contact') }}" class="mobile-navigation-cta">
                Plan Your Trip
                <span aria-hidden="true">→</span>
            </a>

        </nav>

    </div>
</header>