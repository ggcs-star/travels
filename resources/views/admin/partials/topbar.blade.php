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
            <span class="admin-breadcrumb">
                Admin
            </span>

            <strong>
                @yield('title', 'Dashboard')
            </strong>
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
            ↗
        </a>


        {{-- Notifications --}}
        <button
            type="button"
            class="admin-topbar-icon-button"
            title="Notifications"
            aria-label="Notifications"
        >
            ♢

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