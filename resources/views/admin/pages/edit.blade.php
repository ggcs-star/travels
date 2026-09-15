@extends('admin.layouts.app')

@section('title', 'Edit Page')

@section('content')

<div class="page-admin-wrapper">

    <div class="page-admin-header">
        <div>
            <div class="page-admin-breadcrumb">
                Admin / Pages / Edit
            </div>

            <h1>
                Edit Page
            </h1>

            <p>
                Update page content, menu placement and SEO.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('admin.pages.index') }}"
                class="page-admin-back"
            >
                ← Back to Pages
            </a>

            @if($page->isPublished())
                <a
                    href="{{ $page->url }}"
                    target="_blank"
                    class="page-button page-button-light"
                >
                    View Page ↗
                </a>
            @endif

        </div>
    </div>

    @if(session('success'))
        <div class="page-alert page-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="page-alert page-alert-error">
            <strong>Please fix the following:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.pages.update', $page) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        @include(
            'admin.pages._form',
            [
                'page' => $page,
                'parentPages' => $parentPages,
            ]
        )

        <div class="page-save-bar">

            <a
                href="{{ route('admin.pages.index') }}"
                class="page-button page-button-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="page-button page-button-primary"
            >
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection