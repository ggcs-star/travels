@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<section class="auth-page-section">
    <div class="auth-card">
        <h1>Change password</h1>
        <p>Update your account password securely.</p>

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

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf

            <label>Current password</label>
            <input type="password" name="current_password" required autocomplete="current-password">

            <label>New password</label>
            <input type="password" name="password" required autocomplete="new-password">

            <label>Confirm new password</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit" class="storefront-button">Update password</button>
        </form>
    </div>
</section>
@endsection
