@extends('layouts.app')

@section('title', 'Create New Password')

@section('content')
<section class="auth-page-section">
    <div class="auth-card">

        <h1>Create new password</h1>

        <p>Your email OTP has been verified. Choose a new password for your account.</p>

        @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="auth-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset.update') }}">
            @csrf

            <label for="reset_password">New password</label>
            <input
                id="reset_password"
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="Minimum 8 characters"
            >

            <label for="reset_password_confirmation">Confirm new password</label>
            <input
                id="reset_password_confirmation"
                type="password"
                name="password_confirmation"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="Confirm your new password"
            >

            <button type="submit" class="storefront-button">
                Reset password
            </button>
        </form>

    </div>
</section>
@endsection
