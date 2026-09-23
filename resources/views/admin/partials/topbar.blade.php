<header class="admin-topbar">

    {{-- LEFT --}}
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

            <strong>
                @yield('title', 'Dashboard')
            </strong>

            <p>@yield('description', '')</p>

        </div>

    </div>


    {{-- RIGHT --}}
    <div class="admin-topbar-right">

        {{-- View Website --}}
        <a
            href="{{ route('home') }}"
            target="_blank"
            rel="noopener"
            class="admin-topbar-icon-button"
            title="View Website"
            aria-label="View Website"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
        </a>


        {{-- Notifications --}}
        <button
            type="button"
            class="admin-topbar-icon-button"
            title="Notifications"
            aria-label="Notifications"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>

            <span class="admin-notification-dot"></span>
        </button>


        {{-- Admin Profile --}}
        @php
            $admin = auth()->user();

            $adminName = $admin->username
                ?: $admin->name
                ?: 'Administrator';

            $adminInitial = strtoupper(
                substr(trim($adminName), 0, 1)
            );
        @endphp


        <a
            href="{{ route('admin.profile') }}"
            class="admin-topbar-profile"
            title="My Profile"
            aria-label="Open My Profile"
        >

            <span class="admin-topbar-avatar">
                {{ $adminInitial }}
            </span>

            <span class="admin-topbar-profile-info">

                <strong>
                    {{ $adminName }}
                </strong>

                <small>
                    Administrator
                </small>

            </span>

            <span
                class="admin-topbar-profile-arrow"
                aria-hidden="true"
            >
                ⌄
            </span>

        </a>

    </div>

</header>