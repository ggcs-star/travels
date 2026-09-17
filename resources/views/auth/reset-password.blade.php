@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<section class="auth-page-section">
    <div class="auth-card">
        <h1>Reset your password</h1>
        <p>Choose a new password for your account.</p>

        @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email">

            <label>New password</label>
            <input type="password" name="password" required autocomplete="new-password">

            <label>Confirm new password</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit" class="storefront-button">Reset password</button>
        </form>
    </div>
</section>
@endsection
