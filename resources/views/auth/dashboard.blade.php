<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Dashboard |
        {{ config('travels.brand.name', 'Travels') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="admin-dashboard-body">


    {{-- =========================================================
         MOBILE OVERLAY
    ========================================================== --}}

    <div
        class="admin-sidebar-overlay"
        id="adminSidebarOverlay"
    ></div>


    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

    <aside
        class="admin-sidebar"
        id="adminSidebar"
    >

        {{-- Brand --}}

        <div class="admin-sidebar-brand">

            <a
                href="{{ route('admin.dashboard') }}"
                class="admin-brand"
            >

                <span class="admin-brand-logo">

                    <img
                        src="{{ asset('images/travel_logo.png') }}"
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


        {{-- Navigation --}}

        <nav
            class="admin-sidebar-nav"
            aria-label="Admin navigation"
        >

            {{-- Main --}}

            <div class="admin-nav-section">

                <span class="admin-nav-label">
                    MAIN
                </span>


                <a
                    href="{{ route('admin.dashboard') }}"
                    class="admin-nav-link active"
                >

                    <span class="admin-nav-icon">
                        ⌂
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>

            </div>


            {{-- Content --}}

            <div class="admin-nav-section">

                <span class="admin-nav-label">
                    CONTENT
                </span>


                <a
                    href="#"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ✈
                    </span>

                    <span>
                        Tour Packages
                    </span>

                </a>


                <a
                    href="#"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ◈
                    </span>

                    <span>
                        Tours
                    </span>

                </a>


                <a
                    href="#"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ◎
                    </span>

                    <span>
                        Destinations
                    </span>

                </a>


                <a
                    href="#"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ◷
                    </span>

                    <span>
                        Monthly Tours
                    </span>

                </a>


                <a
                    href="#"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ▤
                    </span>

                    <span>
                        Blog
                    </span>

                </a>

            </div>


            {{-- Website --}}

            <div class="admin-nav-section">

                <span class="admin-nav-label">
                    WEBSITE
                </span>


                <a
                    href="{{ route('home') }}"
                    class="admin-nav-link"
                    target="_blank"
                >

                    <span class="admin-nav-icon">
                        ↗
                    </span>

                    <span>
                        View Website
                    </span>

                </a>


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


            {{-- Account --}}

            <div class="admin-nav-section">

                <span class="admin-nav-label">
                    ACCOUNT
                </span>


                <a
                    href="{{ route('admin.profile') }}"
                    class="admin-nav-link"
                >

                    <span class="admin-nav-icon">
                        ◯
                    </span>

                    <span>
                        My Profile
                    </span>

                </a>


                <a
                    href="{{ route('admin.password.change') }}"
                    class="admin-nav-link"
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


        {{-- Sidebar bottom --}}

        <div class="admin-sidebar-bottom">

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
         MAIN AREA
    ========================================================== --}}

    <div class="admin-main">


        {{-- =====================================================
             TOPBAR
        ====================================================== --}}

        <header class="admin-topbar">

            <div class="admin-topbar-left">

                <button
                    type="button"
                    class="admin-sidebar-toggle"
                    id="adminSidebarToggle"
                    aria-label="Open sidebar"
                >

                    <span></span>
                    <span></span>
                    <span></span>

                </button>


                <div>

                    <span class="admin-breadcrumb">
                        Admin
                    </span>

                    <strong>
                        Dashboard
                    </strong>

                </div>

            </div>


            <div class="admin-topbar-right">

                {{-- Website --}}

                <a
                    href="{{ route('home') }}"
                    class="admin-topbar-icon-button"
                    target="_blank"
                    title="View Website"
                >
                    ↗
                </a>


                {{-- Notification --}}

                <button
                    type="button"
                    class="admin-topbar-icon-button"
                    title="Notifications"
                >

                    ♢

                    <span class="admin-notification-dot"></span>

                </button>


                {{-- Profile --}}

                <a
                    href="{{ route('admin.profile') }}"
                    class="admin-topbar-profile"
                >

                    <span class="admin-topbar-avatar">

                        {{ strtoupper(
                            substr(
                                auth()->user()->username,
                                0,
                                1
                            )
                        ) }}

                    </span>


                    <span class="admin-topbar-profile-info">

                        <strong>
                            {{ auth()->user()->username }}
                        </strong>

                        <small>
                            Admin
                        </small>

                    </span>

                    <span class="admin-topbar-profile-arrow">
                       ⌄
                    </span>

                </a>

            </div>

        </header>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        <main class="admin-content">


            {{-- Page Heading --}}

            <section class="admin-page-heading">

                <div>

                    <span class="admin-page-eyebrow">
                        OVERVIEW
                    </span>

                    <h1>
                        Welcome back,
                        {{ auth()->user()->username }}
                    </h1>

                    <p>
                        Here's what's happening across your
                        travel website today.
                    </p>

                </div>


                <a
                    href="{{ route('home') }}"
                    class="admin-view-site-button"
                    target="_blank"
                >

                    <span>
                        View Website
                    </span>

                    <span>
                        ↗
                    </span>

                </a>

            </section>


            {{-- =================================================
                 STAT CARDS
            ================================================== --}}

            <section class="admin-stat-grid">


                {{-- Tour Packages --}}

                <div class="admin-stat-card">

                    <div class="admin-stat-top">

                        <span class="admin-stat-icon">
                            ✈
                        </span>

                        <span class="admin-stat-badge">
                            Active
                        </span>

                    </div>


                    <div class="admin-stat-value">
                        12
                    </div>

                    <div class="admin-stat-label">
                        Tour Packages
                    </div>

                    <div class="admin-stat-description">
                        Available packages
                    </div>

                </div>


                {{-- Tours --}}

                <div class="admin-stat-card">

                    <div class="admin-stat-top">

                        <span class="admin-stat-icon">
                            ◈
                        </span>

                        <span class="admin-stat-badge">
                            Active
                        </span>

                    </div>


                    <div class="admin-stat-value">
                        24
                    </div>

                    <div class="admin-stat-label">
                        Tours
                    </div>

                    <div class="admin-stat-description">
                        Published tours
                    </div>

                </div>


                {{-- Destinations --}}

                <div class="admin-stat-card">

                    <div class="admin-stat-top">

                        <span class="admin-stat-icon">
                            ◎
                        </span>

                        <span class="admin-stat-badge">
                            Active
                        </span>

                    </div>


                    <div class="admin-stat-value">
                        18
                    </div>

                    <div class="admin-stat-label">
                        Destinations
                    </div>

                    <div class="admin-stat-description">
                        Travel destinations
                    </div>

                </div>


                {{-- Blog --}}

                <div class="admin-stat-card">

                    <div class="admin-stat-top">

                        <span class="admin-stat-icon">
                            ▤
                        </span>

                        <span class="admin-stat-badge">
                            Published
                        </span>

                    </div>


                    <div class="admin-stat-value">
                        36
                    </div>

                    <div class="admin-stat-label">
                        Blog Posts
                    </div>

                    <div class="admin-stat-description">
                        Published articles
                    </div>

                </div>

            </section>


            {{-- =================================================
                 MAIN DASHBOARD GRID
            ================================================== --}}

            <section class="admin-dashboard-grid">


                {{-- =================================================
                     RECENT ACTIVITY
                ================================================== --}}

                <div class="admin-panel admin-activity-panel">

                    <div class="admin-panel-header">

                        <div>

                            <span class="admin-panel-eyebrow">
                                ACTIVITY
                            </span>

                            <h2>
                                Recent Activity
                            </h2>

                        </div>


                        <button
                            type="button"
                            class="admin-panel-more"
                        >
                            •••
                        </button>

                    </div>


                    <div class="admin-activity-list">


                        <div class="admin-activity-item">

                            <span class="admin-activity-icon">
                                +
                            </span>

                            <div>

                                <strong>
                                    New tour package added
                                </strong>

                                <p>
                                    Kedarnath Spiritual Package
                                </p>

                                <small>
                                    Today, 10:32 AM
                                </small>

                            </div>

                        </div>


                        <div class="admin-activity-item">

                            <span class="admin-activity-icon">
                                ↻
                            </span>

                            <div>

                                <strong>
                                    Tour package updated
                                </strong>

                                <p>
                                    Rajasthan Heritage Tour
                                </p>

                                <small>
                                    Today, 09:15 AM
                                </small>

                            </div>

                        </div>


                        <div class="admin-activity-item">

                            <span class="admin-activity-icon">
                                +
                            </span>

                            <div>

                                <strong>
                                    New destination added
                                </strong>

                                <p>
                                    Manali, Himachal Pradesh
                                </p>

                                <small>
                                    Yesterday, 04:48 PM
                                </small>

                            </div>

                        </div>


                        <div class="admin-activity-item">

                            <span class="admin-activity-icon">
                                ✓
                            </span>

                            <div>

                                <strong>
                                    Blog post published
                                </strong>

                                <p>
                                    Best Places to Visit in India
                                </p>

                                <small>
                                    Yesterday, 01:22 PM
                                </small>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     QUICK ACTIONS
                ================================================== --}}

                <div class="admin-panel admin-quick-panel">

                    <div class="admin-panel-header">

                        <div>

                            <span class="admin-panel-eyebrow">
                                SHORTCUTS
                            </span>

                            <h2>
                                Quick Actions
                            </h2>

                        </div>

                    </div>


                    <div class="admin-quick-grid">


                        <a
                            href="#"
                            class="admin-quick-action"
                        >

                            <span class="admin-quick-icon">
                                +
                            </span>

                            <span>

                                <strong>
                                    Add Tour
                                </strong>

                                <small>
                                    Create a new tour
                                </small>

                            </span>

                            <span class="admin-quick-arrow">
                                →
                            </span>

                        </a>


                        <a
                            href="#"
                            class="admin-quick-action"
                        >

                            <span class="admin-quick-icon">
                                +
                            </span>

                            <span>

                                <strong>
                                    Add Package
                                </strong>

                                <small>
                                    Create a package
                                </small>

                            </span>

                            <span class="admin-quick-arrow">
                                →
                            </span>

                        </a>


                        <a
                            href="#"
                            class="admin-quick-action"
                        >

                            <span class="admin-quick-icon">
                                +
                            </span>

                            <span>

                                <strong>
                                    Add Destination
                                </strong>

                                <small>
                                    Add destination
                                </small>

                            </span>

                            <span class="admin-quick-arrow">
                                →
                            </span>

                        </a>


                        <a
                            href="#"
                            class="admin-quick-action"
                        >

                            <span class="admin-quick-icon">
                                +
                            </span>

                            <span>

                                <strong>
                                    Write Blog
                                </strong>

                                <small>
                                    Publish an article
                                </small>

                            </span>

                            <span class="admin-quick-arrow">
                                →
                            </span>

                        </a>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 BOTTOM GRID
            ================================================== --}}

            <section class="admin-bottom-grid">


                {{-- Website Status --}}

                <div class="admin-panel">

                    <div class="admin-panel-header">

                        <div>

                            <span class="admin-panel-eyebrow">
                                WEBSITE
                            </span>

                            <h2>
                                Website Status
                            </h2>

                        </div>

                    </div>


                    <div class="admin-status-row">

                        <span class="admin-status-indicator"></span>

                        <div>

                            <strong>
                                Website is Live
                            </strong>

                            <p>
                                Your public website is currently
                                accessible.
                            </p>

                        </div>

                        <a
                            href="{{ route('home') }}"
                            target="_blank"
                        >
                            View
                        </a>

                    </div>

                </div>


                {{-- Admin Account --}}

                <div class="admin-panel">

                    <div class="admin-panel-header">

                        <div>

                            <span class="admin-panel-eyebrow">
                                ACCOUNT
                            </span>

                            <h2>
                                Admin Account
                            </h2>

                        </div>

                    </div>


                    <div class="admin-account-info">

                        <div class="admin-account-avatar">

                            {{ strtoupper(
                                substr(
                                    auth()->user()->username,
                                    0,
                                    1
                                )
                            ) }}

                        </div>


                        <div>

                            <strong>
                                {{ auth()->user()->username }}
                            </strong>

                            <p>
                                {{ auth()->user()->email }}
                            </p>

                        </div>


                        <a
                            href="{{ route('admin.profile') }}"
                            class="admin-account-edit"
                        >
                            Edit
                        </a>

                    </div>

                </div>

            </section>

        </main>

    </div>


    {{-- =========================================================
         SIDEBAR JAVASCRIPT
    ========================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const sidebar =
                    document.getElementById(
                        'adminSidebar'
                    );

                const overlay =
                    document.getElementById(
                        'adminSidebarOverlay'
                    );

                const toggle =
                    document.getElementById(
                        'adminSidebarToggle'
                    );

                const close =
                    document.getElementById(
                        'adminSidebarClose'
                    );


                function openSidebar() {

                    sidebar.classList.add(
                        'is-open'
                    );

                    overlay.classList.add(
                        'is-visible'
                    );

                    document.body.classList.add(
                        'sidebar-open'
                    );

                }


                function closeSidebar() {

                    sidebar.classList.remove(
                        'is-open'
                    );

                    overlay.classList.remove(
                        'is-visible'
                    );

                    document.body.classList.remove(
                        'sidebar-open'
                    );

                }


                if (toggle) {

                    toggle.addEventListener(
                        'click',
                        openSidebar
                    );

                }


                if (close) {

                    close.addEventListener(
                        'click',
                        closeSidebar
                    );

                }


                if (overlay) {

                    overlay.addEventListener(
                        'click',
                        closeSidebar
                    );

                }


                document
                    .querySelectorAll(
                        '.admin-nav-link'
                    )
                    .forEach(function (link) {

                        link.addEventListener(
                            'click',
                            function () {

                                if (
                                    window.innerWidth <= 1000
                                ) {

                                    closeSidebar();

                                }

                            }
                        );

                    });

            }
        );

    </script>

</body>

</html>