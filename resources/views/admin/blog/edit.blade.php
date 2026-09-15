@extends('admin.layouts.app')

@section('title', 'Edit Blog Post')

@section('content')

<div class="admin-page">

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                BLOG / EDIT
            </span>

            <h1 class="admin-page__title">
                Edit Blog Post
            </h1>

            <p class="admin-page__description">
                Update your travel article, SEO information and media.
            </p>

        </div>


        <div class="admin-page__header-actions">

            <a
                href="{{ route('admin.blog.show', $blog) }}"
                class="admin-button"
            >
                View Post
            </a>

            <a
                href="{{ route('admin.blog.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

        </div>

    </div>


    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div class="admin-alert admin-alert--error">

            <strong>
                Please fix the following:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.blog.update', $blog) }}"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        @include(
            'admin.blog.partials.form',
            [
                'mode' => 'edit',
                'blog' => $blog,
            ]
        )

    </form>

</div>

@endsection