@extends('admin.layouts.app')

@section('title', 'SEO Tools')

@section('content')

<style>
    .seo-page {
        padding: 24px;
    }

    .seo-header {
        margin-bottom: 22px;
    }

    .seo-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        color: #1f2937;
    }

    .seo-header p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .seo-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .seo-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
    }

    .seo-card-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .seo-card-header p {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .seo-card-body {
        padding: 22px;
    }

    .seo-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .seo-field {
        margin-bottom: 2px;
    }

    .seo-field.full {
        grid-column: 1 / -1;
    }

    .seo-label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 14px;
        font-weight: 600;
    }

    .seo-input,
    .seo-select,
    .seo-textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        padding: 11px 13px;
        font-size: 14px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: .2s;
        box-sizing: border-box;
    }

    .seo-input:focus,
    .seo-select:focus,
    .seo-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.10);
    }

    .seo-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .seo-help {
        margin-top: 6px;
        font-size: 12px;
        color: #9ca3af;
    }

    .seo-counter {
        text-align: right;
        margin-top: 5px;
        font-size: 12px;
        color: #9ca3af;
    }

    .seo-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        margin-top: 4px;
    }

    .seo-radio {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #374151;
    }

    .seo-radio input {
        width: 16px;
        height: 16px;
        accent-color: #6366f1;
    }

    .seo-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-top: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .seo-action-left,
    .seo-action-right {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .seo-btn {
        border: 0;
        border-radius: 7px;
        padding: 10px 17px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: .2s;
    }

    .seo-btn-primary {
        background: #4f46e5;
        color: #fff;
    }

    .seo-btn-primary:hover {
        background: #4338ca;
    }

    .seo-btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .seo-btn-secondary:hover {
        background: #e5e7eb;
    }

    .seo-btn-success {
        background: #059669;
        color: #fff;
    }

    .seo-btn-success:hover {
        background: #047857;
    }

    .seo-alert {
        border-radius: 8px;
        padding: 13px 15px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .seo-alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .seo-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .seo-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
    }

    .sitemap-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .sitemap-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 17px;
    }

    .sitemap-box-title {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }

    .sitemap-url {
        width: 100%;
        box-sizing: border-box;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 9px 10px;
        font-size: 12px;
        color: #4b5563;
    }

    .seo-warning {
        margin-top: 18px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 8px;
        padding: 13px 15px;
        font-size: 13px;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .seo-page {
            padding: 15px;
        }

        .seo-grid,
        .sitemap-info {
            grid-template-columns: 1fr;
        }

        .seo-field.full {
            grid-column: auto;
        }

        .seo-card-body {
            padding: 16px;
        }

        .seo-actions {
            padding: 16px;
            align-items: stretch;
        }

        .seo-action-left,
        .seo-action-right {
            width: 100%;
        }

        .seo-btn {
            flex: 1;
        }
    }
</style>

<div class="seo-page">

    <div class="seo-header">
        <h1>SEO Tools</h1>
        <p>Manage your website SEO settings and sitemap.</p>
    </div>

    @if(session('success'))
        <div class="seo-alert seo-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="seo-alert seo-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="seo-alert seo-alert-error">
            <strong>Please fix the following errors:</strong>

            <ul style="margin:8px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.seo.update') }}">
        @csrf
        @method('PUT')

        {{-- SEO SETTINGS --}}
        <div class="seo-card">

            <div class="seo-card-header">
                <h2>SEO Settings</h2>
                <p>Configure the basic search engine optimization settings for your website.</p>
            </div>

            <div class="seo-card-body">

                <div class="seo-grid">

                    {{-- LANGUAGE --}}
                    <div class="seo-field">
                        <label class="seo-label" for="settings_language">
                            Settings Language
                        </label>

                        <select
                            name="settings_language"
                            id="settings_language"
                            class="seo-select"
                        >
                            <option value="English"
                                {{ old('settings_language', $settings['seo.settings_language'] ?? 'English') === 'English' ? 'selected' : '' }}>
                                English
                            </option>

                            <option value="Hindi"
                                {{ old('settings_language', $settings['seo.settings_language'] ?? '') === 'Hindi' ? 'selected' : '' }}>
                                Hindi
                            </option>
                        </select>
                    </div>

                    {{-- SITE TITLE --}}
                    <div class="seo-field">
                        <label class="seo-label" for="site_title">
                            Site Title
                        </label>

                        <input
                            type="text"
                            name="site_title"
                            id="site_title"
                            class="seo-input"
                            value="{{ old('site_title', $settings['seo.title'] ?? '') }}"
                            placeholder="Enter your website title"
                        >

                        <div class="seo-help">
                            Main title used throughout your website.
                        </div>
                    </div>

                    {{-- HOME TITLE --}}
                    <div class="seo-field full">
                        <label class="seo-label" for="home_title">
                            Home Title
                        </label>

                        <input
                            type="text"
                            name="home_title"
                            id="home_title"
                            class="seo-input"
                            value="{{ old('home_title', $settings['seo.home_title'] ?? '') }}"
                            placeholder="Enter homepage SEO title"
                        >
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="seo-field full">
                        <label class="seo-label" for="site_description">
                            Site Description
                        </label>

                        <textarea
                            name="site_description"
                            id="site_description"
                            class="seo-textarea"
                            maxlength="160"
                            placeholder="Enter website description"
                        >{{ old('site_description', $settings['seo.description'] ?? '') }}</textarea>

                        <div class="seo-counter">
                            <span id="descriptionCount">
                                {{ strlen(old('site_description', $settings['seo.description'] ?? '')) }}
                            </span>/160
                        </div>
                    </div>

                    {{-- KEYWORDS --}}
                    <div class="seo-field full">
                        <label class="seo-label" for="keywords">
                            Keywords
                        </label>

                        <input
                            type="text"
                            name="keywords"
                            id="keywords"
                            class="seo-input"
                            value="{{ old('keywords', $settings['seo.keywords'] ?? '') }}"
                            placeholder="travel, tours, holidays, packages"
                        >

                        <div class="seo-help">
                            Separate keywords with commas.
                        </div>
                    </div>

                    {{-- GOOGLE ANALYTICS --}}
                    <div class="seo-field full">
                        <label class="seo-label" for="google_analytics">
                            Google Analytics
                        </label>

                        <textarea
                            name="google_analytics"
                            id="google_analytics"
                            class="seo-textarea"
                            placeholder="Paste Google Analytics code here"
                        >{{ old('google_analytics', $settings['seo.analytics_head'] ?? '') }}</textarea>

                        <div class="seo-help">
                            Google Analytics / tracking code inserted inside the website head.
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- SITEMAP SETTINGS --}}
        <div class="seo-card">

            <div class="seo-card-header">
                <h2>Sitemap Settings</h2>
                <p>Configure how your sitemap should be generated and updated.</p>
            </div>

            <div class="seo-card-body">

                <div class="seo-grid">

                    {{-- FREQUENCY --}}
                    <div class="seo-field">
                        <label class="seo-label" for="sitemap_frequency">
                            Sitemap Frequency
                        </label>

                        <select
                            name="sitemap_frequency"
                            id="sitemap_frequency"
                            class="seo-select"
                        >
                            @php
                                $frequency = old(
                                    'sitemap_frequency',
                                    $settings['sitemap.frequency'] ?? 'daily'
                                );
                            @endphp

                            <option value="always" {{ $frequency === 'always' ? 'selected' : '' }}>
                                Always
                            </option>

                            <option value="hourly" {{ $frequency === 'hourly' ? 'selected' : '' }}>
                                Hourly
                            </option>

                            <option value="daily" {{ $frequency === 'daily' ? 'selected' : '' }}>
                                Daily
                            </option>

                            <option value="weekly" {{ $frequency === 'weekly' ? 'selected' : '' }}>
                                Weekly
                            </option>

                            <option value="monthly" {{ $frequency === 'monthly' ? 'selected' : '' }}>
                                Monthly
                            </option>

                            <option value="yearly" {{ $frequency === 'yearly' ? 'selected' : '' }}>
                                Yearly
                            </option>

                            <option value="never" {{ $frequency === 'never' ? 'selected' : '' }}>
                                Never
                            </option>
                        </select>
                    </div>

                    {{-- LAST MODIFICATION --}}
                    <div class="seo-field">
                        <label class="seo-label">
                            Last Modification
                        </label>

                        @php
                            $lastmod = old(
                                'sitemap_lastmod',
                                $settings['sitemap.lastmod'] ?? 'server'
                            );
                        @endphp

                        <div class="seo-radio-group">

                            <label class="seo-radio">
                                <input
                                    type="radio"
                                    name="sitemap_lastmod"
                                    value="none"
                                    {{ $lastmod === 'none' ? 'checked' : '' }}
                                >
                                <span>None</span>
                            </label>

                            <label class="seo-radio">
                                <input
                                    type="radio"
                                    name="sitemap_lastmod"
                                    value="server"
                                    {{ $lastmod === 'server' ? 'checked' : '' }}
                                >
                                <span>Server's Response</span>
                            </label>

                        </div>
                    </div>

                    {{-- PRIORITY --}}
                    <div class="seo-field full">
                        <label class="seo-label">
                            Priority
                        </label>

                        @php
                            $priority = old(
                                'sitemap_priority',
                                $settings['sitemap.priority'] ?? 'automatic'
                            );
                        @endphp

                        <div class="seo-radio-group">

                            <label class="seo-radio">
                                <input
                                    type="radio"
                                    name="sitemap_priority"
                                    value="none"
                                    {{ $priority === 'none' ? 'checked' : '' }}
                                >
                                <span>None</span>
                            </label>

                            <label class="seo-radio">
                                <input
                                    type="radio"
                                    name="sitemap_priority"
                                    value="automatic"
                                    {{ $priority === 'automatic' ? 'checked' : '' }}
                                >
                                <span>Automatically Calculated Priority</span>
                            </label>

                        </div>
                    </div>

                </div>

                <div class="seo-warning">
                    <strong>Important:</strong>
                    If your website contains more than 50,000 links,
                    the sitemap will automatically be split into multiple sitemap files.
                </div>

            </div>

            <div class="seo-actions">

                <div class="seo-action-left">
                    <button type="submit" class="seo-btn seo-btn-primary">
                        Save Changes
                    </button>
                </div>

            </div>

        </div>

    </form>

    {{-- SITEMAP MANAGEMENT --}}
    <div class="seo-card">

        <div class="seo-card-header">
            <h2>Sitemap Management</h2>
            <p>Generate or manually update your website sitemap.</p>
        </div>

        <div class="seo-card-body">

            <div class="sitemap-info">

                <div class="sitemap-box">

                    <div class="sitemap-box-title">
                        Sitemap URL
                    </div>

                    <input
                        type="text"
                        class="sitemap-url"
                        readonly
                        value="{{ url('/sitemap.xml') }}"
                        onclick="this.select()"
                    >

                </div>

                <div class="sitemap-box">

                    <div class="sitemap-box-title">
                        Sitemap Update URL
                    </div>

                    <input
                        type="text"
                        class="sitemap-url"
                        readonly
                        value="{{ url('/cron/update-sitemap') }}"
                        onclick="this.select()"
                    >

                </div>

            </div>

        </div>

        <div class="seo-actions">

            <div class="seo-action-left">

                <form
                    method="POST"
                    action="{{ route('admin.settings.seo.generate-sitemap') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="seo-btn seo-btn-secondary"
                    >
                        Generate Sitemap
                    </button>
                </form>

            </div>

            <div class="seo-action-right">

                <a
                    href="{{ url('/cron/update-sitemap') }}"
                    target="_blank"
                    class="seo-btn seo-btn-success"
                >
                    Update Now
                </a>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const description = document.getElementById('site_description');
    const counter = document.getElementById('descriptionCount');

    if (description && counter) {

        function updateCounter() {
            counter.textContent = description.value.length;
        }

        description.addEventListener('input', updateCounter);

        updateCounter();
    }

});
</script>

@endsection