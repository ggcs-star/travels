<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', config('travels.brand.name', 'Travels'))
    </title>

    <meta
        name="description"
        content="@yield(
            'meta_description',
            'Discover unforgettable travel experiences with Travels.'
        )"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body>

    @include('components.layout.topbar')

    @include('components.layout.header')

    <main>
        @yield('content')
    </main>

    @include('components.layout.footer')

    @stack('scripts')

</body>

</html>