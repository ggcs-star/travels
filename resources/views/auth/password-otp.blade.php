@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')
<section class="auth-page-section">
    <div class="auth-card">

        <h1>Verify OTP</h1>

        <p>
            Enter the 6-digit verification code sent to
            <strong>{{ $email }}</strong>.
        </p>

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

        <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf

            <label for="password_reset_otp">Verification code</label>
            <input
                id="password_reset_otp"
                type="text"
                name="otp"
                inputmode="numeric"
                autocomplete="one-time-code"
                pattern="[0-9]{6}"
                maxlength="6"
                minlength="6"
                required
                autofocus
                placeholder="Enter 6-digit OTP"
            >

            <button type="submit" class="storefront-button">
                Verify OTP
            </button>
        </form>

        <form method="POST" action="{{ route('password.otp.resend') }}" style="margin-top:10px">
            @csrf
            <button type="submit" class="storefront-button storefront-button--secondary">
                Resend OTP
            </button>
        </form>

        <p style="margin-top:14px;font-size:13px;">
            OTP is valid for 10 minutes. For security, only limited verification attempts are allowed.
        </p>

        <a href="{{ route('login') }}" class="auth-inline-link">
            ← Back to Login
        </a>

    </div>
</section>
@endsection
