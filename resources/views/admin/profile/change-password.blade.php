@extends('admin.layouts.app')

@section('title', 'Change Password')

@section('content')

<style>
    .admin-password-page {
        max-width: 900px;
        margin: 0 auto;
    }

    .admin-password-header {
        margin-bottom: 24px;
    }

    .admin-password-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    .admin-password-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .password-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
    }

    .password-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .password-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #111827;
        font-size: 20px;
        flex-shrink: 0;
    }

    .password-card-header h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .password-card-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }

    .password-card-body {
        padding: 26px 24px;
    }

    .alert {
        padding: 13px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 18px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .form-input-wrap {
        position: relative;
    }

    .form-input {
        width: 100%;
        min-height: 46px;
        padding: 11px 46px 11px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 14px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        box-sizing: border-box;
    }

    .form-input:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.10);
    }

    .form-input.is-invalid {
        border-color: #dc2626;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        cursor: pointer;
        color: #6b7280;
        font-size: 13px;
        padding: 5px;
    }

    .password-toggle:hover {
        color: #111827;
    }

    .field-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
    }

    .password-help {
        margin-top: 7px;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
    }

    .security-note {
        margin-top: 8px;
        padding: 14px;
        border-radius: 10px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        font-size: 13px;
        line-height: 1.55;
    }

    .password-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 6px;
    }

    .back-btn,
    .save-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
        box-sizing: border-box;
    }

    .back-btn {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .back-btn:hover {
        background: #f9fafb;
    }

    .save-btn {
        border: 1px solid #0f766e;
        background: #0f766e;
        color: #ffffff;
    }

    .save-btn:hover {
        background: #115e59;
        border-color: #115e59;
    }

    @media (max-width: 640px) {
        .admin-password-header h1 {
            font-size: 24px;
        }

        .password-card-body,
        .password-card-header {
            padding: 20px 16px;
        }

        .password-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .back-btn,
        .save-btn {
            width: 100%;
        }
    }
</style>

<div class="admin-password-page">

    <div class="admin-password-header">
        <h1>Change Password</h1>
        <p>Update your administrator account password securely.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="password-card">

        <div class="password-card-header">
            <div class="password-icon">🔒</div>

            <div>
                <h2>Account Security</h2>
                <p>Change the password used to access the admin panel.</p>
            </div>
        </div>

        <div class="password-card-body">

            <form
                action="{{ route('admin.profile.password.update') }}"
                method="POST"
                autocomplete="off"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label
                        for="current_password"
                        class="form-label"
                    >
                        Current Password
                    </label>

                    <div class="form-input-wrap">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-input @error('current_password') is-invalid @enderror"
                            placeholder="Enter your current password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('current_password', this)"
                        >
                            Show
                        </button>
                    </div>

                    @error('current_password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        for="password"
                        class="form-label"
                    >
                        New Password
                    </label>

                    <div class="form-input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Enter a new password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password', this)"
                        >
                            Show
                        </button>
                    </div>

                    <div class="password-help">
                        Password must be at least 8 characters long.
                    </div>

                    @error('password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        for="password_confirmation"
                        class="form-label"
                    >
                        Confirm New Password
                    </label>

                    <div class="form-input-wrap">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="Re-enter your new password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password_confirmation', this)"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <div class="security-note">
                    For security, you must enter your current password before
                    changing it. After changing the password, you will need
                    to use the new password for future admin logins.
                </div>

                <div class="password-actions">

                    <a
                        href="{{ route('admin.profile') }}"
                        class="back-btn"
                    >
                        ← Back to Profile
                    </a>

                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Update Password
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = 'Hide';
        } else {
            input.type = 'password';
            button.textContent = 'Show';
        }
    }
</script>

@endsection