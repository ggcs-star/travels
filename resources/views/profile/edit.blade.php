@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="profile-edit-page">

    {{-- =========================================================
         EDIT PROFILE HERO
         ========================================================= --}}
    <section class="profile-edit-hero">
        <div class="profile-edit-hero__glow"></div>

        <div class="profile-edit-hero__inner">

            <a
                href="{{ route('profile') }}"
                class="profile-edit-back"
            >
                <span>←</span>
                Back to profile
            </a>

            <div class="profile-edit-heading">
                <span class="profile-edit-eyebrow">
                    ACCOUNT SETTINGS
                </span>

                <h1>Edit Profile</h1>

                <p>
                    Keep your personal information up to date for a smoother travel experience.
                </p>
            </div>

        </div>
    </section>

    <main class="profile-edit-content">

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="profile-edit-alert profile-edit-alert--success">
                <span class="profile-edit-alert__icon">✓</span>

                <div>
                    <strong>Profile updated</strong>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="profile-edit-alert profile-edit-alert--error">
                <span class="profile-edit-alert__icon">!</span>

                <div>
                    <strong>Something went wrong</strong>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="profile-edit-alert profile-edit-alert--error">
                <span class="profile-edit-alert__icon">!</span>

                <div>
                    <strong>Please check your details</strong>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <section class="profile-edit-card">

            {{-- CARD HEADER --}}
            <div class="profile-edit-card__head">

                <div class="profile-edit-avatar">
                    @if($profile->profile_photo)
                        <img
                            src="{{ asset('storage/' . $profile->profile_photo) }}"
                            alt="{{ $user->name }}"
                        >
                    @else
                        {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
                    @endif
                </div>

                <div class="profile-edit-card__identity">
                    <span>TRAVEL MEMBER</span>

                    <h2>
                        {{ $user->name ?: $user->username }}
                    </h2>

                    <p>
                        {{ '@' . $user->username }}
                    </p>
                </div>

            </div>

            {{-- FORM --}}
            <form
                method="POST"
                action="{{ route('profile.update') }}"
                class="profile-edit-form"
                novalidate
            >
                @csrf
                @method('PUT')

                <div class="profile-edit-section-heading">
                    <div>
                        <span>PERSONAL INFORMATION</span>
                        <h3>Update your details</h3>
                    </div>

                    <span class="profile-edit-required">
                        <b>*</b> Required
                    </span>
                </div>

                <div class="profile-edit-fields">

                    {{-- FULL NAME --}}
                    <div class="profile-edit-field">
                        <label for="profile_name">
                            <span class="profile-edit-field__icon">A</span>
                            Full name
                            <b>*</b>
                        </label>

                        <div class="profile-edit-input-wrap">
                            <input
                                id="profile_name"
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                autocomplete="name"
                                maxlength="255"
                                placeholder="Enter your full name"
                                required
                            >
                        </div>

                        <small>
                            Use the name you want displayed on your travel account.
                        </small>

                        @error('name')
                            <small class="profile-edit-field__error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- PHONE --}}
                    <div class="profile-edit-field">
                        <label for="profile_phone">
                            <span class="profile-edit-field__icon">☎</span>
                            Phone number
                        </label>

                        <div class="profile-edit-input-wrap">
                            <input
                                id="profile_phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $profile->phone) }}"
                                autocomplete="tel"
                                maxlength="40"
                                inputmode="tel"
                                placeholder="+91 98765 43210"
                            >
                        </div>

                        <small>
                            Keep your active number here so travel-related communication can reach you.
                        </small>

                        @error('phone')
                            <small class="profile-edit-field__error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                </div>

                {{-- ACCOUNT INFORMATION --}}
                <div class="profile-edit-readonly-section">

                    <div class="profile-edit-section-heading">
                        <div>
                            <span>ACCOUNT INFORMATION</span>
                            <h3>Account details</h3>
                        </div>

                        <span class="profile-edit-locked">
                            <span>🔒</span>
                            Protected
                        </span>
                    </div>

                    <div class="profile-edit-readonly-grid">

                        <div class="profile-edit-readonly-item">
                            <span>Username</span>
                            <strong>{{ '@' . $user->username }}</strong>
                            <small>Username cannot be changed here.</small>
                        </div>

                        <div class="profile-edit-readonly-item">
                            <span>Email address</span>
                            <strong>{{ $user->email }}</strong>
                            <small>Login email remains protected.</small>
                        </div>

                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="profile-edit-actions">

                    <a
                        href="{{ route('profile') }}"
                        class="profile-edit-cancel"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="profile-edit-save"
                    >
                        <span>✓</span>
                        Save changes
                    </button>

                </div>

                <p class="profile-edit-security-note">
                    <span>✓</span>
                    Your account information is securely stored and updated through your authenticated session.
                </p>

            </form>

        </section>

    </main>
</div>
@endsection
