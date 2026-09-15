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

                $loginLogo = $settingsService->get('visual.login_logo');
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

                        <input
                            id="register_name"
                            type="text"
                            name="name"
                            value="{{ old('_form') === 'register' ? old('name') : '' }}"
                            placeholder="Enter your full name"
                            autocomplete="name"
                            required
                        >

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

                        <input
                            id="register_username"
                            type="text"
                            name="username"
                            value="{{ old('_form') === 'register' ? old('username') : '' }}"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                        >

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

                        <input
                            id="register_email"
                            type="email"
                            name="email"
                            value="{{ old('_form') === 'register' ? old('email') : '' }}"
                            placeholder="Enter your email"
                            autocomplete="email"
                            required
                        >

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

                        <input
                            id="forgot_email"
                            type="email"
                            name="email"
                            value="{{ old('_form') === 'forgot' ? old('email') : '' }}"
                            placeholder="Enter your email"
                            autocomplete="email"
                            required
                        >

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



            {{-- =====================================================
                 FOOTER
            ====================================================== --}}

            <div class="auth-footer">

                <a href="{{ route('home') }}">
                    ← Back to website
                </a>

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