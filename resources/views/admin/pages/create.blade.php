@extends('admin.layouts.app')

@section('title', 'Create Page')

@section('content')

<div class="page-cms-create">

    <form
        action="{{ route('admin.pages.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="createPageForm"
    >
        @csrf

        @include(
            'admin.pages._form',
            [
                'page' => null,
                'parentPages' => $parentPages,
            ]
        )

    </form>

</div>

@endsection
