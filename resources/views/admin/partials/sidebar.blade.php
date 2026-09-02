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

                <strong>
                    {{ config('travels.brand.name', 'Travels') }}
                </strong>

                <small>
                    ADMIN PANEL
                </small>

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


                {{-- BLOG SUB NAVIGATION --}}

                @if(request()->routeIs('admin.blog.*') || request()->routeIs('admin.blog-categories.*'))

                    <div class="admin-nav-submenu">

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


        {{-- =================================================
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

            </a>

        </div>


        {{-- =================================================
             ACCOUNT
        ================================================== --}}

        <div class="admin-nav-section">

            <span class="admin-nav-label">
                ACCOUNT
            </span>


            {{-- PROFILE --}}

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


            {{-- PASSWORD --}}

            <a
                href="{{ route('admin.password.change') }}"
                class="admin-nav-link {{ request()->routeIs('admin.password.change') ? 'active' : '' }}"
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