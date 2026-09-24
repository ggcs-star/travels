@extends('admin.layouts.app')

@section('title', 'Welcome back, '.auth()->user()->username)

@section('description', 'Manage your travel website from one place.')

@section('content')

<div class="admin-page">

    <!-- <div class="admin-page__header">

        <a
            href="{{ route('home') }}"
            target="_blank"
            class="admin-button admin-button--dark"
        >
            View Website
            <span>↗</span>
        </a>

    </div> -->


    {{-- Stats --}}

    <div class="admin-stats">
@include('admin.partials.stat-card', [
    'icon' => '✈',
    'value' => $tourPackagesCount,
    'label' => 'Tour Packages',
    'caption' => 'Total packages',
])

    @include('admin.partials.stat-card', [
    'icon' => '◈',
    'value' => $publishedToursCount,
    'label' => 'Published Tours',
    'caption' => 'Currently published',
])

        @include('admin.partials.stat-card', [
            'icon' => '◎',
            'value' => 18,
            'label' => 'Destinations',
            'caption' => 'Travel destinations',
        ])

        @include('admin.partials.stat-card', [
            'icon' => '▤',
            'value' => 36,
            'label' => 'Blog Posts',
            'caption' => 'Published articles',
        ])

    </div>


    {{-- Main panels --}}

    <div class="admin-grid admin-grid--main">

        <section class="admin-card">

            <div class="admin-card__header">

                <div>
                    <span class="admin-eyebrow">
                        ACTIVITY
                    </span>

                    <h2>
                        Recent Activity
                    </h2>
                </div>

                <button
                    type="button"
                    class="admin-icon-button"
                >
                    •••
                </button>

            </div>


            <div class="admin-activity">

                @include('admin.partials.activity-item', [
                    'icon' => '+',
                    'title' => 'New tour package added',
                    'description' => 'Kedarnath Spiritual Package',
                    'time' => 'Today, 10:32 AM',
                ])

                @include('admin.partials.activity-item', [
                    'icon' => '↻',
                    'title' => 'Tour package updated',
                    'description' => 'Rajasthan Heritage Tour',
                    'time' => 'Today, 09:15 AM',
                ])

                @include('admin.partials.activity-item', [
                    'icon' => '+',
                    'title' => 'New destination added',
                    'description' => 'Manali, Himachal Pradesh',
                    'time' => 'Yesterday, 04:48 PM',
                ])

                @include('admin.partials.activity-item', [
                    'icon' => '✓',
                    'title' => 'Blog post published',
                    'description' => 'Best Places to Visit in India',
                    'time' => 'Yesterday, 01:22 PM',
                ])

            </div>

        </section>


        <section class="admin-card">

            <div class="admin-card__header">

                <div>
                    <span class="admin-eyebrow">
                        SHORTCUTS
                    </span>

                    <h2>
                        Quick Actions
                    </h2>
                </div>

            </div>


            <div class="admin-actions">

         <a
    href="{{ route('admin.tours.create') }}"
    class="admin-action"
>
    <span class="admin-action__icon">+</span>

    <span class="admin-action__content">
        <strong>Add Tour Package</strong>
        <small>Create a new tour package</small>
    </span>

    <span class="admin-action__arrow">→</span>
</a>


                <a href="#" class="admin-action">
                    <span class="admin-action__icon">+</span>

                    <span class="admin-action__content">
                        <strong>Add Package</strong>
                        <small>Create a tour package</small>
                    </span>

                    <span class="admin-action__arrow">→</span>
                </a>


                <a href="#" class="admin-action">
                    <span class="admin-action__icon">+</span>

                    <span class="admin-action__content">
                        <strong>Add Destination</strong>
                        <small>Add travel destination</small>
                    </span>

                    <span class="admin-action__arrow">→</span>
                </a>


                <a href="#" class="admin-action">
                    <span class="admin-action__icon">+</span>

                    <span class="admin-action__content">
                        <strong>Write Blog</strong>
                        <small>Publish a new article</small>
                    </span>

                    <span class="admin-action__arrow">→</span>
                </a>

            </div>

        </section>

    </div>


    {{-- Bottom --}}

    <div class="admin-grid admin-grid--bottom">

        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">
                        WEBSITE
                    </span>

                    <h2>
                        Website Status
                    </h2>
                </div>
            </div>


            <div class="admin-status">

                <span class="admin-status__dot"></span>

                <div>
                    <strong>Website is Live</strong>

                    <p>
                        Your public website is currently accessible.
                    </p>
                </div>

                <a
                    href="{{ route('home') }}"
                    target="_blank"
                >
                    View
                </a>

            </div>

        </section>


        <section class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">
                        ACCOUNT
                    </span>

                    <h2>
                        Admin Account
                    </h2>
                </div>
            </div>


            <div class="admin-account">

                <div class="admin-account__avatar">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>

                <div class="admin-account__content">
                    <strong>
                        {{ auth()->user()->username }}
                    </strong>

                    <p>
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <a href="{{ route('admin.profile') }}">
                    Edit
                </a>

            </div>

        </section>

    </div>

</div>

@endsection