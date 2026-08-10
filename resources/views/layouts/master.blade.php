<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('title')
    @vite(['resources/styles/app.scss'])
    @yield('styles')
    @vite(['resources/js/app.js'])
    @yield('links_scripts')
</head>

<body>
    @auth('admin')
        @include('layouts.admin.panel')
    @endauth
    @include('layouts.header')
    <main id="main-content" class="main-content">
        @yield('content')
    </main>
    <div id="modal-window" class="modal-window">
        @yield('modal')
    </div>
    @include('layouts.footer')
    @yield('afterFooter')
</body>

</html>
