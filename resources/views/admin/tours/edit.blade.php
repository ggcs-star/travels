@extends('admin.layouts.app')

@section('title', 'Edit Tour Package')

@section('content')

<div class="admin-page">

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

            <a
                href="{{ route('admin.tours.departures.index', $tour) }}"
                class="admin-button admin-button--dark"
            >
                Manage Departures
            </a>

            <a
                href="{{ route('admin.tours.show', $tour) }}"
                class="admin-button"
            >
                Preview
            </a>

            <a
                href="{{ route('admin.tours.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('admin.tours.update', $tour) }}"
        enctype="multipart/form-data"
    >

        @include('admin.tours.partials.form', [
            'tour' => $tour,
        ])

    </form>

</div>

@endsection
