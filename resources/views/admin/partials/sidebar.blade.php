<aside class="admin-sidebar" id="adminSidebar">

    {{-- =====================================================
         BRAND
    ====================================================== --}}

    <div class="admin-sidebar-brand">

        <a
            href="{{ route('admin.dashboard') }}"
            class="admin-brand"
        >

            <span class="admin-brand-logo">

                <img
                    src="{{ asset('images/logo.jpeg') }}"
                    alt="{{ config('travels.brand.name', 'Travels') }}"
                >

            </span>

            <span class="admin-brand-content">

                <!--
                <strong>
                    {{ config('travels.brand.name', 'Travels') }}
                </strong>

                <small>
                    ADMIN PANEL
                </small>
                -->

            </span>

        </a>

        <button
            type="button"
            class="admin-sidebar-close"
            id="adminSidebarClose"
            aria-label="Close sidebar"
        >
            ×
        </button>

    </div>


    {{-- =====================================================
         NAVIGATION
    ====================================================== --}}

    <nav class="admin-sidebar-nav">


        {{-- =================================================
             MAIN
        ================================================== --}}

        <div class="admin-nav-section">

            <span class="admin-nav-label">
                MAIN
            </span>

            {{-- DASHBOARD --}}

            <a
                href="{{ route('admin.dashboard') }}"
                class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>

            </a>

        </div>



        {{-- =================================================
             CONTENT
        ================================================== --}}

        <div class="admin-nav-section">

            <span class="admin-nav-label">
                CONTENT
            </span>


            {{-- TOUR CATEGORIES --}}

            <a
                href="{{ route('admin.tour-categories.index') }}"
                class="admin-nav-link {{ request()->routeIs('admin.tour-categories.*') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    ◈
                </span>

                <span>
                    Tour Categories
                </span>

            </a>


            {{-- TOUR PACKAGES --}}

            <a
                href="{{ route('admin.tours.index') }}"
                class="admin-nav-link {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    ✈
                </span>

                <span>
                    Tour Packages
                </span>

            </a>


            {{-- BOOKINGS --}}

            <a
                href="{{ route('admin.bookings.index') }}"
                class="admin-nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    $
                </span>

                <span>
                    Bookings
                </span>

            </a>

{{-- CONTACT INQUIRIES --}}
<a
    href="{{ route('admin.inquiries.index') }}"
    class="admin-nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}"
>
    <span class="admin-nav-icon">✉</span>
    <span>Contact Inquiries</span>
</a>

            {{-- =================================================
                 BLOG
            ================================================== --}}

            <div class="admin-nav-group">

                <a
                    href="{{ route('admin.blog.index') }}"
                    class="admin-nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"
                >

                    <span class="admin-nav-icon">
                        ▤
                    </span>

                    <span>
                        Blog
                    </span>

                </a>


                {{-- POINTS MANAGEMENT --}}

                <a
                    href="{{ route('admin.point-settings.index') }}"
                    class="admin-nav-link {{ request()->routeIs('admin.point-settings.*') ? 'active' : '' }}"
                >

                    <span class="admin-nav-icon">
                        ◎
                    </span>

                    <span>
                        Points Management
                    </span>

                </a>


                {{-- POINTS WALLETS --}}

                <a
                    href="{{ route('admin.point-wallets.index') }}"
                    class="admin-nav-link {{ request()->routeIs('admin.point-wallets.*') ? 'active' : '' }}"
                >

                    <span class="admin-nav-icon">
                        ◎
                    </span>

                    <span>
                        Points Wallets
                    </span>

                </a>


                {{-- BLOG SUB NAVIGATION --}}

                @if(
                    request()->routeIs('admin.blog.*') ||
                    request()->routeIs('admin.blog-categories.*')
                )

                    <div class="admin-nav-submenu">

                        {{-- ALL POSTS --}}

                        <a
                            href="{{ route('admin.blog.index') }}"
                            class="admin-nav-sublink {{ request()->routeIs('admin.blog.index', 'admin.blog.create', 'admin.blog.edit', 'admin.blog.show') ? 'active' : '' }}"
                        >

                            <span class="admin-nav-subicon">
                                •
                            </span>

                            <span>
                                All Posts
                            </span>

                        </a>


                        {{-- CREATE POST --}}

                        <a
                            href="{{ route('admin.blog.create') }}"
                            class="admin-nav-sublink {{ request()->routeIs('admin.blog.create') ? 'active' : '' }}"
                        >

                            <span class="admin-nav-subicon">
                                +
                            </span>

                            <span>
                                Create Post
                            </span>

                        </a>


                        {{-- CATEGORIES --}}

                        <a
                            href="{{ route('admin.blog-categories.index') }}"
                            class="admin-nav-sublink {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}"
                        >

                            <span class="admin-nav-subicon">
                                ◦
                            </span>

                            <span>
                                Categories
                            </span>

                        </a>

                    </div>

                @endif

            </div>

        </div>



        <!-- {{-- =================================================
             WEBSITE
        ================================================== --}}

        <div class="admin-nav-section">

            <span class="admin-nav-label">
                WEBSITE
            </span>


            {{-- VIEW WEBSITE --}}

            <a
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener"
                class="admin-nav-link"
            >

                <span class="admin-nav-icon">
                    ↗
                </span>

                <span>
                    View Website
                </span>

            </a>


            {{-- WEBSITE CONTENT --}}

            <a
                href="#"
                class="admin-nav-link"
            >

                <span class="admin-nav-icon">
                    ◫
                </span>

                <span>
                    Website Content
                </span>

            </a> -->
<a
    href="{{ route('admin.pages.index') }}"
    class="admin-nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
>
    <span class="admin-nav-icon">
        ▤
    </span>

    <span>
        Pages
    </span>
</a>


            {{-- =================================================
                 SETTINGS
            ================================================== --}}

            <div class="admin-nav-group">

                {{-- SETTINGS TOGGLE --}}

                <a
                    href="#"
                    id="adminSettingsToggle"
                    class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                    aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}"
                    aria-controls="adminSettingsSubmenu"
                    onclick="toggleAdminSettings(event)"
                >

                    <span class="admin-nav-icon">
                        ⚙
                    </span>

                    <span>
                        Settings
                    </span>

                    <span
                        class="admin-nav-arrow"
                        id="adminSettingsArrow"
                    >
                        {{ request()->routeIs('admin.settings.*') ? '⌃' : '⌄' }}
                    </span>

                </a>


                {{-- SETTINGS SUB NAVIGATION --}}

                <div
                    id="adminSettingsSubmenu"
                    class="admin-nav-submenu"
                    style="{{ request()->routeIs('admin.settings.*') ? 'display: block;' : 'display: none;' }}"
                >

                    {{-- GENERAL SETTINGS --}}

                    <a
                        href="{{ route('admin.settings.general') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            General Settings
                        </span>

                    </a>


                    {{-- VISUAL SETTINGS --}}

                    <a
                        href="{{ route('admin.settings.visual') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.visual') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            Visual Settings
                        </span>

                    </a>


                    {{-- FONT SETTINGS --}}

                    <a
                        href="{{ route('admin.settings.fonts') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.fonts') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            Font Settings
                        </span>

                    </a>


                    {{-- HOME PAGE SETTINGS --}}

                    <a
                        href="{{ route('admin.settings.home') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.home') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            Home Page Settings
                        </span>

                    </a>


                    {{-- SEO SETTINGS --}}

                    <a
                        href="{{ route('admin.settings.seo') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            SEO Settings
                        </span>

                    </a>

                </div>

            </div>

        </div>



        {{-- =================================================
             ACCOUNT
        ================================================== --}}

        <div class="admin-nav-section">

            <span class="admin-nav-label">
                ACCOUNT
            </span>


            {{-- MY PROFILE --}}

            <a
                href="{{ route('admin.profile') }}"
                class="admin-nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    ◯
                </span>

                <span>
                    My Profile
                </span>

            </a>


            {{-- CHANGE PASSWORD --}}

            <a
                href="{{ route('admin.profile.password') }}"
                class="admin-nav-link {{ request()->routeIs('admin.profile.password') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    ◉
                </span>

                <span>
                    Change Password
                </span>

            </a>

        </div>


    </nav>



    {{-- =====================================================
         SIDEBAR BOTTOM
    ====================================================== --}}

    <div class="admin-sidebar-bottom">


        {{-- USER --}}

        <div class="admin-sidebar-user">

            <div class="admin-sidebar-user-avatar">

                {{ strtoupper(
                    substr(
                        auth()->user()->username,
                        0,
                        1
                    )
                ) }}

            </div>


            <div class="admin-sidebar-user-info">

                <strong>
                    {{ auth()->user()->username }}
                </strong>

                <small>
                    Administrator
                </small>

            </div>

        </div>


        {{-- LOGOUT --}}

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="admin-logout-button"
            >

                <span>
                    ↪
                </span>

                Logout

            </button>

        </form>

    </div>

</aside>



{{-- =========================================================
     MOBILE OVERLAY
========================================================= --}}

<div
    class="admin-sidebar-overlay"
    id="adminSidebarOverlay"
></div>



{{-- =========================================================
     SETTINGS TOGGLE
========================================================= --}}

<script>
    function toggleAdminSettings(event) {

        event.preventDefault();

        const submenu = document.getElementById('adminSettingsSubmenu');
        const arrow = document.getElementById('adminSettingsArrow');
        const toggle = document.getElementById('adminSettingsToggle');

        if (!submenu || !arrow || !toggle) {
            return;
        }

        const isOpen = submenu.style.display === 'block';

        if (isOpen) {

            submenu.style.display = 'none';
            arrow.textContent = '⌄';
            toggle.setAttribute('aria-expanded', 'false');

        } else {

            submenu.style.display = 'block';
            arrow.textContent = '⌃';
            toggle.setAttribute('aria-expanded', 'true');

        }

    }
</script>