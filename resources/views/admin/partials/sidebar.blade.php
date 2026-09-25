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
                    src="{{ asset('images/travel_logo_white.png') }}"
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
                </span>

                <span>
                    Tour Packages
                </span>

            </a>


            {{-- BOOKINGS --}}

            <a
                href="#"
                id="adminBookingsToggle"
                class="admin-nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                aria-expanded="{{ request()->routeIs('admin.bookings.*') ? 'true' : 'false' }}"
                aria-controls="adminBookingsSubmenu"
                onclick="toggleAdminBookings(event)"
            >

                <span class="admin-nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M16 6V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v1"/><circle cx="17" cy="13" r="1.3" fill="currentColor" stroke="none"/></svg>
                </span>

                <span>
                    Bookings
                </span>

                <span
                    class="admin-nav-arrow"
                    id="adminBookingsArrow"
                >
                    {{ request()->routeIs('admin.bookings.*') ? '⌃' : '⌄' }}
                </span>

            </a>


            <div
                id="adminBookingsSubmenu"
                class="admin-nav-submenu"
                style="{{ request()->routeIs('admin.bookings.*') ? 'display: block;' : 'display: none;' }}"
            >

                {{-- ALL BOOKINGS --}}

                <a
                    href="{{ route('admin.bookings.index') }}"
                    class="admin-nav-sublink {{ request()->routeIs('admin.bookings.index', 'admin.bookings.show') ? 'active' : '' }}"
                >

                    <span class="admin-nav-subicon">
                        •
                    </span>

                    <span>
                        All Bookings
                    </span>

                </a>


                {{-- CREATE BOOKING --}}

                <a
                    href="{{ route('admin.bookings.create') }}"
                    class="admin-nav-sublink {{ request()->routeIs('admin.bookings.create', 'admin.bookings.checkout') ? 'active' : '' }}"
                >

                    <span class="admin-nav-subicon">
                        +
                    </span>

                    <span>
                        Create Booking
                    </span>

                </a>

            </div>


            {{-- USERS --}}

            <a
                href="{{ route('admin.users.index') }}"
                class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>

                <span>
                    Users
                </span>

            </a>

{{-- CONTACT INQUIRIES --}}
<a
    href="{{ route('admin.inquiries.index') }}"
    class="admin-nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}"
>
    <span class="admin-nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m3 6 9 7 9-7"/></svg>
    </span>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/></svg>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2" fill="currentColor" stroke="none"/></svg>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v2"/><circle cx="16" cy="14" r="1.3" fill="currentColor" stroke="none"/></svg>
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


            {{-- PAGES --}}

            <a
                href="{{ route('admin.pages.index') }}"
                class="admin-nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
            >

                <span class="admin-nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/></svg>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.04 1.56V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.56-1.04H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1.04-1.56V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.56 1.04H21a2 2 0 1 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z"/></svg>
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

{{-- PREFERENCES --}}

<a
    href="{{ route('admin.settings.preferences') }}"
    class="admin-nav-sublink {{ request()->routeIs('admin.settings.preferences') ? 'active' : '' }}"
>

    <span class="admin-nav-subicon">
        •
    </span>

    <span>
        Preferences
    </span>

</a>
                    {{-- FONT SETTINGS --}}

                    <!-- <a
                        href="{{ route('admin.settings.fonts') }}"
                        class="admin-nav-sublink {{ request()->routeIs('admin.settings.fonts') ? 'active' : '' }}"
                    >

                        <span class="admin-nav-subicon">
                            •
                        </span>

                        <span>
                            Font Settings
                        </span>

                    </a> -->


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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6"/></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
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


    function toggleAdminBookings(event) {

        event.preventDefault();

        const submenu = document.getElementById('adminBookingsSubmenu');
        const arrow = document.getElementById('adminBookingsArrow');
        const toggle = document.getElementById('adminBookingsToggle');

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