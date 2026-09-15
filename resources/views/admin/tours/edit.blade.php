@extends('admin.layouts.app')

@section('title', 'Edit Tour Package')

@section('content')

<div class="admin-page">

    {{-- ================================================================
         PAGE HEADER
    ================================================================ --}}
    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                TOURS / EDIT
            </span>

            <h1 class="admin-page__title">
                Edit Tour Package
            </h1>

            <p class="admin-page__description">
                Update the information for {{ $tour->name }}.
            </p>

        </div>


        <div class="admin-page__actions">

            {{-- Manage Departures --}}
            <a
                href="{{ route('admin.tours.departures.index', $tour) }}"
                class="admin-button admin-button--dark"
            >
                Manage Departures
            </a>


            {{-- Preview --}}
            <a
                href="{{ route('admin.tours.show', $tour) }}"
                class="admin-button"
            >
                Preview
            </a>


            {{-- Back --}}
            <a
                href="{{ route('admin.tours.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

        </div>

    </div>


    {{-- ================================================================
         VALIDATION / ERROR SUMMARY
    ================================================================ --}}
    @if ($errors->any())

        <div
            class="admin-alert admin-alert--error"
            role="alert"
        >

            <strong>
                Please fix the following errors:
            </strong>

            <ul>
                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach
            </ul>

        </div>

    @endif


    {{-- ================================================================
         SUCCESS MESSAGE
    ================================================================ --}}
    @if (session('success'))

        <div
            class="admin-alert admin-alert--success"
            role="alert"
        >
            {{ session('success') }}
        </div>

    @endif


    {{-- ================================================================
         EDIT TOUR FORM
    ================================================================ --}}
    <form
        method="POST"
        action="{{ route('admin.tours.update', $tour) }}"
        enctype="multipart/form-data"
    >

        @csrf

        @method('PUT')


        {{-- ============================================================
             TOUR FORM PARTIAL

             Actual file path:
             resources/views/admin/tours/partials/form.blade.php
        ============================================================ --}}
        @include('admin.tours.partials.form', [
            'tour' => $tour,
        ])


    </form>

</div>

@endsection