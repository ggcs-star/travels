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
        @yield('title', 'Admin')
        |
        {{ config('travels.brand.name', 'Travels') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="admin-body">

    @include('admin.partials.sidebar')


    <div class="admin-main">

        @include('admin.partials.topbar')


        <main class="admin-content">

            @yield('content')

        </main>

    </div>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const sidebar =
                    document.getElementById('adminSidebar');

                const overlay =
                    document.getElementById('adminSidebarOverlay');

                const toggle =
                    document.getElementById('adminSidebarToggle');

                const close =
                    document.getElementById('adminSidebarClose');


                function openSidebar() {

                    sidebar?.classList.add('is-open');

                    overlay?.classList.add('is-visible');

                }


                function closeSidebar() {

                    sidebar?.classList.remove('is-open');

                    overlay?.classList.remove('is-visible');

                }


                toggle?.addEventListener(
                    'click',
                    openSidebar
                );


                close?.addEventListener(
                    'click',
                    closeSidebar
                );


                overlay?.addEventListener(
                    'click',
                    closeSidebar
                );

            }
        );

    </script>

</body>

</html>