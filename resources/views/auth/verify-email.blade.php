@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<section class="auth-page-section">
    <div class="auth-card">
        <h1>Verify your email</h1>
        <p>
            Please verify your email address before continuing. We sent a
            verification link to <strong>{{ auth()->user()->email }}</strong>.
        </p>

        @if(session('success'))
            <div class="auth-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="storefront-button">Send verification email again</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
            @csrf
            <button type="submit" class="storefront-button storefront-button--secondary">Log out</button>
        </form>
    </div>
</section>
@endsection
