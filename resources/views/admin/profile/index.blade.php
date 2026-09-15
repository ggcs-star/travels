@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')

    @php
        $adminName = $admin->username ?? $admin->name ?? 'Administrator';
        $adminEmail = $admin->email ?? '—';

        $initials = collect(
            preg_split('/[\s._-]+/', trim($adminName))
        )
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        if ($initials === '') {
            $initials = 'A';
        }

        $createdAt = $admin->created_at;
        $updatedAt = $admin->updated_at;
    @endphp


    <div class="admin-profile-page">

        {{-- =====================================================
             PAGE HEADER
        ====================================================== --}}

        <div class="admin-profile-heading">

            <div>

                <span class="admin-profile-eyebrow">
                    ACCOUNT
                </span>

                <h1>
                    My Profile
                </h1>

                <p>
                    Manage your administrator account information
                    and security settings.
                </p>

            </div>

        </div>


        {{-- =====================================================
             FLASH MESSAGES
        ====================================================== --}}

        @if (session('success'))

            <div class="admin-profile-alert admin-profile-alert--success">

                <span class="admin-profile-alert-icon">
                    ✓
                </span>

                <div>

                    <strong>
                        Success
                    </strong>

                    <p>
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif


        @if (session('error'))

            <div class="admin-profile-alert admin-profile-alert--error">

                <span class="admin-profile-alert-icon">
                    !
                </span>

                <div>

                    <strong>
                        Something went wrong
                    </strong>

                    <p>
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif


        {{-- =====================================================
             VALIDATION ERRORS
        ====================================================== --}}

        @if ($errors->any())

            <div class="admin-profile-alert admin-profile-alert--error">

                <span class="admin-profile-alert-icon">
                    !
                </span>

                <div>

                    <strong>
                        Please check the following:
                    </strong>

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        {{-- =====================================================
             PROFILE OVERVIEW
        ====================================================== --}}

        <div class="admin-profile-grid">


            {{-- =================================================
                 LEFT PROFILE CARD
            ================================================== --}}

            <section class="admin-profile-card admin-profile-card--identity">

                <div class="admin-profile-cover"></div>


                <div class="admin-profile-identity">

                    <div class="admin-profile-avatar">
                        {{ $initials }}
                    </div>


                    <div class="admin-profile-identity-content">

                        <h2>
                            {{ $adminName }}
                        </h2>

                        <p>
                            {{ $adminEmail }}
                        </p>


                        <span class="admin-profile-role">

                            <span class="admin-profile-role-dot"></span>

                            Administrator

                        </span>

                    </div>

                </div>


                {{-- ACCOUNT STATUS --}}

                <div class="admin-profile-account-status">

                    <div class="admin-profile-status-icon">
                        ✓
                    </div>

                    <div>

                        <span>
                            Account status
                        </span>

                        <strong>
                            {{
                                method_exists($admin, 'isActive') && ! $admin->isActive()
                                    ? 'Inactive'
                                    : 'Active'
                            }}
                        </strong>

                    </div>

                </div>


                {{-- ACCOUNT META --}}

                <div class="admin-profile-meta-list">


                    {{-- Account ID --}}

                    <div class="admin-profile-meta-item">

                        <span class="admin-profile-meta-icon">
                            ID
                        </span>

                        <div>

                            <small>
                                Account ID
                            </small>

                            <strong>
                                #{{ $admin->id }}
                            </strong>

                        </div>

                    </div>


                    {{-- Email --}}

                    <div class="admin-profile-meta-item">

                        <span class="admin-profile-meta-icon">
                            @
                        </span>

                        <div>

                            <small>
                                Email
                            </small>

                            <strong>
                                {{ $adminEmail }}
                            </strong>

                        </div>

                    </div>


                    {{-- Member Since --}}

                    <div class="admin-profile-meta-item">

                        <span class="admin-profile-meta-icon">
                            +
                        </span>

                        <div>

                            <small>
                                Member since
                            </small>

                            <strong>
                                {{ $createdAt ? $createdAt->format('d M Y') : '—' }}
                            </strong>

                        </div>

                    </div>


                    {{-- Last Update --}}

                    <div class="admin-profile-meta-item">

                        <span class="admin-profile-meta-icon">
                            ↻
                        </span>

                        <div>

                            <small>
                                Last profile update
                            </small>

                            <strong>
                                {{ $updatedAt ? $updatedAt->format('d M Y, h:i A') : '—' }}
                            </strong>

                        </div>

                    </div>


                </div>

            </section>


            {{-- =================================================
                 RIGHT CONTENT
            ================================================== --}}

            <div class="admin-profile-main">


                {{-- =============================================
                     PROFILE INFORMATION
                ============================================== --}}

                <section class="admin-profile-card">


                    <div class="admin-profile-card-header">

                        <div>

                            <span class="admin-profile-section-label">
                                PROFILE INFORMATION
                            </span>

                            <h2>
                                Personal details
                            </h2>

                            <p>
                                Update the basic information associated
                                with your administrator account.
                            </p>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('admin.profile.update') }}"
                        class="admin-profile-form"
                    >

                        @csrf

                        @method('PUT')


                        {{-- USERNAME --}}

                        <div class="admin-profile-field">

                            <label for="username">
                                Username
                            </label>


                            <div class="admin-profile-input-wrap">

                                <span class="admin-profile-input-icon">
                                    @
                                </span>


                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    value="{{ old('username', $admin->username) }}"
                                    autocomplete="username"
                                    maxlength="100"
                                    required
                                >

                            </div>


                            <small>
                                Use letters, numbers, dots, underscores
                                or hyphens.
                            </small>


                            @error('username')

                                <span class="admin-profile-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- EMAIL --}}

                        <div class="admin-profile-field">

                            <label for="email">
                                Email address
                            </label>


                            <div class="admin-profile-input-wrap">

                                <span class="admin-profile-input-icon">
                                    ✉
                                </span>


                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    autocomplete="email"
                                    maxlength="200"
                                    required
                                >

                            </div>


                            <small>
                                This email is associated with your
                                administrator account.
                            </small>


                            @error('email')

                                <span class="admin-profile-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- ROLE --}}

                        <div class="admin-profile-field">

                            <label>
                                Account role
                            </label>


                            <div class="admin-profile-readonly">

                                <span class="admin-profile-readonly-icon">
                                    ◈
                                </span>


                                <div>

                                    <strong>
                                        Administrator
                                    </strong>

                                    <small>
                                        Full access to the admin panel
                                    </small>

                                </div>


                                <span class="admin-profile-readonly-badge">
                                    SYSTEM
                                </span>

                            </div>

                        </div>


                        {{-- ACTIONS --}}

                        <div class="admin-profile-form-actions">

                            <button
                                type="submit"
                                class="admin-profile-primary-button"
                            >

                                <span>
                                    Save changes
                                </span>

                                <span>
                                    →
                                </span>

                            </button>

                        </div>

                    </form>

                </section>


                {{-- =============================================
                     SECURITY
                ============================================== --}}

                <section class="admin-profile-card">


                    <div class="admin-profile-card-header">

                        <div>

                            <span class="admin-profile-section-label">
                                SECURITY
                            </span>

                            <h2>
                                Password & security
                            </h2>

                            <p>
                                Keep your administrator account secure
                                by regularly updating your password.
                            </p>

                        </div>

                    </div>


                    <div class="admin-profile-security-row">


                        <div class="admin-profile-security-icon">
                            🔒
                        </div>


                        <div class="admin-profile-security-content">

                            <strong>
                                Administrator password
                            </strong>

                            <p>
                                Change your password if you think your
                                account may have been compromised or
                                simply want to refresh your credentials.
                            </p>

                        </div>


                        <a
                            href="{{ route('admin.profile.password') }}"
                            class="admin-profile-secondary-button"
                        >
                            Change password
                        </a>


                    </div>

                </section>


                {{-- =============================================
                     ACCOUNT SECURITY INFORMATION
                ============================================== --}}

                <section class="admin-profile-card admin-profile-card--security-info">


                    <div class="admin-profile-security-info">

                        <div class="admin-profile-security-info-icon">
                            ✓
                        </div>


                        <div>

                            <strong>
                                Your administrator account is protected
                            </strong>

                            <p>
                                Never share your administrator password
                                with anyone. Use a strong, unique password
                                and sign out when using a shared device.
                            </p>

                        </div>

                    </div>

                </section>


            </div>

        </div>

    </div>

@endsection