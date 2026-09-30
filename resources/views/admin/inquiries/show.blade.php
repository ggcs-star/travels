@extends('admin.layouts.app')

@section('title', 'Inquiry Details')

@section('content')

@php
    $statusVariant = match($inquiry->status) {
        'new' => 'danger',
        'read' => 'warning',
        default => 'success',
    };
@endphp

<div class="admin-page">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="admin-page__header">

        <div>
            <div class="admin-breadcrumb">
                <a href="{{ route('admin.inquiries.index') }}">Inquiries</a>
                <span>/</span>
                <span>{{ $inquiry->name }}</span>
            </div>

            <span class="admin-eyebrow">CUSTOMER ENQUIRY</span>

            <h1 class="admin-page__title">
                {{ $inquiry->name }}
            </h1>

            <p class="admin-page__description">
                {{ ucwords(str_replace('-', ' ', $inquiry->subject)) }}
                ·
                Submitted {{ $inquiry->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.inquiries.index') }}" class="admin-button">
                ← Back to inquiries
            </a>
        </div>

    </div>


    @if(session('success'))
        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>
    @endif


    <div class="admin-grid admin-grid--main">

        {{-- =================================================
             INQUIRY
        ================================================== --}}

        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">SUBJECT</span>
                    <h2>{{ ucwords(str_replace('-', ' ', $inquiry->subject)) }}</h2>
                </div>

                <span class="admin-badge admin-badge--{{ $statusVariant }}">
                    {{ Str::headline($inquiry->status) }}
                </span>
            </div>

            <div class="admin-inquiry-message">
                {!! nl2br(e($inquiry->message)) !!}
            </div>

        </section>


        {{-- =================================================
             CUSTOMER
        ================================================== --}}

        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">CONTACT</span>
                    <h2>Customer details</h2>
                </div>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Name</span>
                    <strong>{{ $inquiry->name }}</strong>
                </div>

                <div>
                    <span>Email</span>
                    <strong><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></strong>
                </div>

                @if($inquiry->phone)
                    <div>
                        <span>Phone</span>
                        <strong><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></strong>
                    </div>
                @endif

                <div>
                    <span>Submitted</span>
                    <strong>{{ $inquiry->created_at->format('d M Y, h:i A') }}</strong>
                </div>

                @if($inquiry->read_at)
                    <div>
                        <span>Read at</span>
                        <strong>{{ $inquiry->read_at->format('d M Y, h:i A') }}</strong>
                    </div>
                @endif

                @if($inquiry->replied_at)
                    <div>
                        <span>Replied at</span>
                        <strong>{{ $inquiry->replied_at->format('d M Y, h:i A') }}</strong>
                    </div>
                @endif

            </div>

            <p class="admin-muted" style="margin-top:16px;">
                Status and delete actions are available from the
                <a href="{{ route('admin.inquiries.index') }}">inquiries table</a>.
            </p>

        </section>

    </div>

</div>

<style>
    .admin-inquiry-message {
        margin-top: 4px;
        padding: 16px 18px;
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-md);
        background: var(--admin-background);
        color: var(--admin-text);
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }
</style>

@endsection
