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
        Login |
        {{ config('travels.brand.name', 'Travels') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="auth-body">

    <main class="auth-page">

        <svg
            class="auth-page-decor auth-page-decor--plane"
            viewBox="0 0 100 70"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path d="M4 60C22 56 34 46 42 34C50 22 62 12 92 8" stroke="currentColor" stroke-width="2" stroke-dasharray="1 7" stroke-linecap="round"/>
            <g transform="translate(72,2) rotate(28)">
                <path d="M0 9L24 0L21 5L10 7.5L7 14L3 12Z" fill="currentColor"/>
            </g>
        </svg>

        <svg
            class="auth-page-decor auth-page-decor--leaf"
            viewBox="0 0 160 160"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <g stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M100 160C92 120 74 98 40 86"/>
                <path d="M110 160C100 115 86 88 56 62"/>
                <path d="M124 160C122 112 132 82 160 66"/>
                <path d="M134 160C138 116 156 96 160 94"/>
                <path d="M114 160C112 105 112 70 112 20"/>
            </g>
        </svg>

        <div class="auth-shell">


            {{-- =====================================================
                 HERO PANEL
            ====================================================== --}}

            <aside
                class="auth-hero"
                style="background-image: url('{{ asset('images/login.png') }}'), linear-gradient(180deg, #bfe3f7 0%, #eaf4fb 30%, #fbe7c8 65%, #f3c98a 100%)"
            >

                <div class="auth-hero-content">

                    <h2>
                        Explore the World
                        <span>With {{ config('travels.brand.name', 'Travels') }}</span>
                    </h2>

                    <p>
                        Discover amazing destinations, create unforgettable
                        memories and experience the world like never before.
                    </p>


                    <div class="auth-hero-features">

                        <div class="auth-hero-feature">

                            <span class="auth-hero-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 16.5L22 8L20.5 5.5L2 11V16.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 12.5L9 20L11.5 18.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.5 21L11 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </span>

                            <strong>Best Deals</strong>
                            <small>on Flights &amp; Hotels</small>

                        </div>


                        <div class="auth-hero-feature">

                            <span class="auth-hero-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21C15.5 17.4 19 13.9 19 10a7 7 0 1 0-14 0c0 3.9 3.5 7.4 7 11Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.6"/></svg>
                            </span>

                            <strong>Worldwide</strong>
                            <small>Destinations</small>

                        </div>


                        <div class="auth-hero-feature">

                            <span class="auth-hero-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L4.5 6v5.2c0 4.8 3.2 8.7 7.5 9.8 4.3-1.1 7.5-5 7.5-9.8V6L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 12l2 2 4-4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <strong>Safe &amp; Secure</strong>
                            <small>Booking</small>

                        </div>

                    </div>

                </div>


                <div class="auth-hero-tagline">
                    Your Journey, Our Priority
                </div>

            </aside>


            {{-- =====================================================
                 FORM PANEL
            ====================================================== --}}

            <div class="auth-card">


            {{-- =====================================================
                 LOGO
            ====================================================== --}}

            @php
                /*
                |--------------------------------------------------------------------------
                | LOGIN PAGE LOGO
                |--------------------------------------------------------------------------
                | ONLY Visual Settings -> Login Page Logo
                | Setting key: visual.login_logo
                |
                | This is intentionally separate from:
                | - visual.logo          (Website Logo)
                | - footer.logo          (Footer Logo)
                | - Admin sidebar logo
                |--------------------------------------------------------------------------
                */

                $settingsService = app(\App\Services\SettingsService::class);

                $loginLogo = $settingsService->get('visual.login_logo')
                    ?: $settingsService->get('visual.logo')
                    ?: $settingsService->get('header.logo');

                $loginLogoUrl = null;

                if (!empty($loginLogo)) {
                    $loginLogo = ltrim(trim((string) $loginLogo), '/');

                    if (filter_var($loginLogo, FILTER_VALIDATE_URL)) {
                        $loginLogoUrl = $loginLogo;
                    } else {
                        $loginLogoUrl = asset('storage/' . $loginLogo);
                    }

                    // Prevent browser cache from showing an older login logo.
                    $loginLogoUrl .= (str_contains($loginLogoUrl, '?') ? '&' : '?')
                        . 'v=' . rawurlencode($loginLogo);
                } else {
                    $loginLogoUrl = asset('images/logo.jpeg');
                }
            @endphp

            <div class="auth-logo">

                @if (!empty($loginLogoUrl))
                    <img
                        src="{{ $loginLogoUrl }}"
                        alt="{{ config('travels.brand.name', 'Travels') }}"
                        width="100"
                        height="100"
                    >
                @endif

            </div>



            {{-- =====================================================
                 GLOBAL SUCCESS MESSAGE
            ====================================================== --}}

            @if (session('success'))

                <div class="auth-success">

                    {{ session('success') }}

                </div>

            @endif



            {{-- =====================================================
                 LOGIN
            ====================================================== --}}

            <div
                class="auth-panel"
                id="loginPanel"
            >

                <div class="auth-header">

                    <span class="auth-eyebrow">
                        WELCOME BACK
                    </span>

                    <h1>
                        Login
                    </h1>

                    <p>
                        Login to continue to
                        {{ config('travels.brand.name', 'Travels') }}.
                    </p>

                </div>


                {{-- Login errors --}}

                @if ($errors->any() && old('_form') !== 'register')

                    <div class="auth-error">

                        <strong>
                            Login failed
                        </strong>

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('login.submit') }}"
                    class="auth-form"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="_form"
                        value="login"
                    >


                    {{-- Email --}}

                    <div class="auth-field">

                        <label for="login_email">
                            Email Address
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <input
                                id="login_email"
                                type="email"
                                name="email"
                                value="{{ old('_form') === 'login' ? old('email') : '' }}"
                                placeholder="Enter your email"
                                autocomplete="email"
                                required
                                autofocus
                            >

                        </div>

                        @if (old('_form') === 'login')

                            @error('email')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Password --}}

                    <div class="auth-field">

                        <div class="auth-label-row">

                            <label for="login_password">
                                Password
                            </label>

                            <button
                                type="button"
                                class="auth-inline-link"
                                data-auth-switch="forgot"
                            >
                                Forgot Password?
                            </button>

                        </div>


                        <div class="auth-password-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </span>

                            <input
                                id="login_password"
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="auth-password-toggle"
                                data-password-target="login_password"
                            >
                                Show
                            </button>

                        </div>


                        @if (old('_form') === 'login')

                            @error('password')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Remember --}}

                    <div class="auth-options">

                        <label class="auth-remember">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                            >

                            <span>
                                Remember me
                            </span>

                        </label>

                    </div>


                    {{-- Login --}}

                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Login
                        </span>

                        <span aria-hidden="true">
                            →
                        </span>

                    </button>

                </form>


                {{-- Register switch --}}

                <div class="auth-register">

                    <span>
                        Don't have an account?
                    </span>

                    <button
                        type="button"
                        class="auth-switch-button"
                        data-auth-switch="register"
                    >
                        Create Account
                    </button>

                </div>

            </div>



            {{-- =====================================================
                 REGISTER
            ====================================================== --}}

            <div
                class="auth-panel"
                id="registerPanel"
                hidden
            >

                <div class="auth-header">

                    <span class="auth-eyebrow">
                        GET STARTED
                    </span>

                    <h1>
                        Create Account
                    </h1>

                    <p>
                        Create your
                        {{ config('travels.brand.name', 'Travels') }}
                        account.
                    </p>

                </div>


                {{-- Register errors --}}

                @if ($errors->any() && old('_form') === 'register')

                    <div class="auth-error">

                        <strong>
                            Please check your details
                        </strong>

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('register.submit') }}"
                    class="auth-form"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="_form"
                        value="register"
                    >


                    {{-- Full Name --}}

                    <div class="auth-field">

                        <label for="register_name">
                            Full Name
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.6"/><path d="M5 19c1.2-3.4 4-5 7-5s5.8 1.6 7 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <input
                                id="register_name"
                                type="text"
                                name="name"
                                value="{{ old('_form') === 'register' ? old('name') : '' }}"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                required
                            >

                        </div>

                        @if (old('_form') === 'register')

                            @error('name')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Username --}}

                    <div class="auth-field">

                        <label for="register_username">
                            Username
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.6"/><path d="M5 19c1.2-3.4 4-5 7-5s5.8 1.6 7 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <input
                                id="register_username"
                                type="text"
                                name="username"
                                value="{{ old('_form') === 'register' ? old('username') : '' }}"
                                placeholder="Enter your username"
                                autocomplete="username"
                                required
                            >

                        </div>

                        @if (old('_form') === 'register')

                            @error('username')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Email --}}

                    <div class="auth-field">

                        <label for="register_email">
                            Email Address
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <input
                                id="register_email"
                                type="email"
                                name="email"
                                value="{{ old('_form') === 'register' ? old('email') : '' }}"
                                placeholder="Enter your email"
                                autocomplete="email"
                                required
                            >

                        </div>

                        @if (old('_form') === 'register')

                            @error('email')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Password --}}

                    <div class="auth-field">

                        <label for="register_password">
                            Password
                        </label>

                        <div class="auth-password-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </span>

                            <input
                                id="register_password"
                                type="password"
                                name="password"
                                placeholder="Create a password"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="auth-password-toggle"
                                data-password-target="register_password"
                            >
                                Show
                            </button>

                        </div>

                        @if (old('_form') === 'register')

                            @error('password')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    {{-- Confirm Password --}}

                    <div class="auth-field">

                        <label for="password_confirmation">
                            Confirm Password
                        </label>

                        <div class="auth-password-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </span>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="Confirm your password"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="auth-password-toggle"
                                data-password-target="password_confirmation"
                            >
                                Show
                            </button>

                        </div>

                    </div>


                    {{-- Register --}}

                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Create Account
                        </span>

                        <span aria-hidden="true">
                            →
                        </span>

                    </button>

                </form>


                {{-- Login switch --}}

                <div class="auth-register">

                    <span>
                        Already have an account?
                    </span>

                    <button
                        type="button"
                        class="auth-switch-button"
                        data-auth-switch="login"
                    >
                        Login
                    </button>

                </div>

            </div>



            {{-- =====================================================
                 FORGOT PASSWORD
            ====================================================== --}}

            <div
                class="auth-panel"
                id="forgotPanel"
                hidden
            >

                <div class="auth-header">

                    <span class="auth-eyebrow">
                        PASSWORD RESET
                    </span>

                    <h1>
                        Forgot Password?
                    </h1>

                    <p>
                        Enter your email address and we'll send you
                        a password reset link.
                    </p>

                </div>


                @if ($errors->any() && old('_form') === 'forgot')

                    <div class="auth-error">

                        <strong>
                            Unable to send reset link
                        </strong>

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                    class="auth-form"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="_form"
                        value="forgot"
                    >


                    <div class="auth-field">

                        <label for="forgot_email">
                            Email Address
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>

                            <input
                                id="forgot_email"
                                type="email"
                                name="email"
                                value="{{ old('_form') === 'forgot' ? old('email') : '' }}"
                                placeholder="Enter your email"
                                autocomplete="email"
                                required
                            >

                        </div>

                        @if (old('_form') === 'forgot')

                            @error('email')

                                <small class="auth-field-error">
                                    {{ $message }}
                                </small>

                            @enderror

                        @endif

                    </div>


                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Send Reset Link
                        </span>

                        <span aria-hidden="true">
                            →
                        </span>

                    </button>

                </form>


                <div class="auth-register">

                    <button
                        type="button"
                        class="auth-switch-button"
                        data-auth-switch="login"
                    >
                        ← Back to Login
                    </button>

                </div>

            </div>



            {{-- =====================================================
                 CHANGE PASSWORD
            ====================================================== --}}

            @auth

                <div
                    class="auth-panel"
                    id="changePasswordPanel"
                    hidden
                >

                    <div class="auth-header">

                        <span class="auth-eyebrow">
                            ACCOUNT SECURITY
                        </span>

                        <h1>
                            Change Password
                        </h1>

                        <p>
                            Update your account password securely.
                        </p>

                    </div>


                    @if ($errors->any() && old('_form') === 'change-password')

                        <div class="auth-error">

                            <strong>
                                Password update failed
                            </strong>

                            <span>
                                {{ $errors->first() }}
                            </span>

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('password.update') }}"
                        class="auth-form"
                    >

                        @csrf


                        <input
                            type="hidden"
                            name="_form"
                            value="change-password"
                        >


                        {{-- Current Password --}}

                        <div class="auth-field">

                            <label for="current_password">
                                Current Password
                            </label>

                            <div class="auth-password-wrapper">

                                <span class="auth-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                </span>

                                <input
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    placeholder="Enter your current password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="auth-password-toggle"
                                    data-password-target="current_password"
                                >
                                    Show
                                </button>

                            </div>


                            @if (old('_form') === 'change-password')

                                @error('current_password')

                                    <small class="auth-field-error">
                                        {{ $message }}
                                    </small>

                                @enderror

                            @endif

                        </div>


                        {{-- New Password --}}

                        <div class="auth-field">

                            <label for="change_password">
                                New Password
                            </label>

                            <div class="auth-password-wrapper">

                                <span class="auth-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                </span>

                                <input
                                    id="change_password"
                                    type="password"
                                    name="password"
                                    placeholder="Enter your new password"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="auth-password-toggle"
                                    data-password-target="change_password"
                                >
                                    Show
                                </button>

                            </div>


                            @if (old('_form') === 'change-password')

                                @error('password')

                                    <small class="auth-field-error">
                                        {{ $message }}
                                    </small>

                                @enderror

                            @endif

                        </div>


                        {{-- Confirm New Password --}}

                        <div class="auth-field">

                            <label for="change_password_confirmation">
                                Confirm New Password
                            </label>

                            <div class="auth-password-wrapper">

                                <span class="auth-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                </span>

                                <input
                                    id="change_password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Confirm your new password"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="auth-password-toggle"
                                    data-password-target="change_password_confirmation"
                                >
                                    Show
                                </button>

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="auth-submit"
                        >

                            <span>
                                Update Password
                            </span>

                            <span aria-hidden="true">
                                →
                            </span>

                        </button>

                    </form>


                    <div class="auth-register">

                        <button
                            type="button"
                            class="auth-switch-button"
                            data-auth-switch="login"
                        >
                            ← Back
                        </button>

                    </div>

                </div>

            @endauth



            </div>

        </div>

    </main>



    {{-- =============================================================
         AUTH JAVASCRIPT
    ============================================================= --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const panels = {
                    login:
                        document.getElementById('loginPanel'),

                    register:
                        document.getElementById('registerPanel'),

                    forgot:
                        document.getElementById('forgotPanel'),

                    changePassword:
                        document.getElementById('changePasswordPanel')
                };


                /*
                |--------------------------------------------------------------------------
                | Show Panel
                |--------------------------------------------------------------------------
                */

                function showPanel(target) {

                    Object.keys(panels).forEach(function (key) {

                        const panel = panels[key];

                        if (!panel) {
                            return;
                        }

                        panel.hidden = key !== target;

                    });

                }


                /*
                |--------------------------------------------------------------------------
                | Login / Register / Forgot / Change Password
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('[data-auth-switch]')
                    .forEach(function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                const target =
                                    this.dataset.authSwitch;

                                if (target === 'login') {

                                    showPanel('login');

                                } else if (target === 'register') {

                                    showPanel('register');

                                } else if (target === 'forgot') {

                                    showPanel('forgot');

                                } else if (
                                    target === 'change-password'
                                ) {

                                    showPanel('changePassword');

                                }

                            }
                        );

                    });


                /*
                |--------------------------------------------------------------------------
                | Password Show / Hide
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('[data-password-target]')
                    .forEach(function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                const targetId =
                                    this.dataset.passwordTarget;

                                const input =
                                    document.getElementById(targetId);


                                if (!input) {
                                    return;
                                }


                                const isPassword =
                                    input.type === 'password';


                                input.type =
                                    isPassword
                                        ? 'text'
                                        : 'password';


                                this.textContent =
                                    isPassword
                                        ? 'Hide'
                                        : 'Show';

                            }
                        );

                    });


                /*
                |--------------------------------------------------------------------------
                | Keep Correct Panel Open After Validation Error
                |--------------------------------------------------------------------------
                */

                @if (old('_form') === 'register')

                    showPanel('register');

                @elseif (old('_form') === 'forgot')

                    showPanel('forgot');

                @elseif (old('_form') === 'change-password')

                    showPanel('changePassword');

                @else

                    showPanel('login');

                @endif

            }
        );

    </script>

</body>

</html>