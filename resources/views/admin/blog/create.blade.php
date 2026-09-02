@extends('admin.layouts.app')

@section('title', 'Create Blog Post')

@section('content')

<div class="admin-page">

    {{-- Page Header --}}
    <div class="admin-page__header">

        <div>
            <span class="admin-eyebrow">
                BLOG / CREATE
            </span>

            <h1 class="admin-page__title">
                Create Blog Post
            </h1>

            <p class="admin-page__description">
                Create a travel article, destination guide or travel story.
            </p>
        </div>

        <a
            href="{{ route('admin.blog.index') }}"
            class="admin-button"
        >
            ← Back to Blog
        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="admin-alert admin-alert--error">

            <strong>
                Please fix the following:
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- Success --}}
    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Blog Form --}}
    <form
        method="POST"
        action="{{ route('admin.blog.store') }}"
        enctype="multipart/form-data"
        id="blogCreateForm"
    >

        @csrf

        @include(
            'admin.blog.partials.form',
            [
                'mode' => 'create',
                'blog' => null,
                'categories' => $categories ?? collect(),
            ]
        )

    </form>

</div>

@endsection