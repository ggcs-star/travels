@extends('admin.layouts.app')

@section('title', 'Create Tour Package')

@section('content')

<div class="admin-page">

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                TOURS / CREATE
            </span>

            <h1 class="admin-page__title">
                Create Tour Package
            </h1>

            <p class="admin-page__description">
                Create the core information for a new travel package.
            </p>

        </div>

        <a
            href="{{ route('admin.tours.index') }}"
            class="admin-button"
        >
            ← Back to Tours
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('admin.tours.store') }}"
        enctype="multipart/form-data"
    >

        @include('admin.tours.partials.form')

    </form>

</div>

@endsection
