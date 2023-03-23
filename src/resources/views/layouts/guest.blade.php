@php
    use App\Services\CordovaService;
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- SEO Meta Tags-->
    <meta name="description" content="Around - Multipurpose Bootstrap Template">
    <meta name="keywords"
        content="bootstrap, business, consulting, coworking space, services, creative agency, dashboard, e-commerce, mobile app showcase, multipurpose, product landing, shop, software, ui kit, web studio, landing, html5, css3, javascript, gallery, slider, touch, creative">
    <meta name="author" content="Createx Studio">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Favicon and Touch Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('around/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('around/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('around/img/favicon/favicon-16x16.png') }}">

    <link rel="manifest" href="{{ asset('site.webmanifest') }}" crossorigin="use-credentials">
    <meta name="msapplication-TileColor" content="#766df4">
    <meta name="theme-color" content="#ffffff">
    <!-- Page loading styles-->
    <style>
        .page-loading {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            -webkit-transition: all .4s .2s ease-in-out;
            transition: all .4s .2s ease-in-out;
            background-color: #fff;
            opacity: 0;
            visibility: hidden;
            z-index: 9999;
        }

        .page-loading.active {
            opacity: 1;
            visibility: visible;
        }

        .page-loading-inner {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            -webkit-transition: opacity .2s ease-in-out;
            transition: opacity .2s ease-in-out;
            opacity: 0;
        }

        .page-loading.active>.page-loading-inner {
            opacity: 1;
        }

        .page-loading-inner>span {
            display: block;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: normal;
            color: #737491;
        }

        .page-spinner {
            display: inline-block;
            width: 2.75rem;
            height: 2.75rem;
            margin-bottom: .75rem;
            vertical-align: text-bottom;
            border: .15em solid #766df4;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner .75s linear infinite;
            animation: spinner .75s linear infinite;
        }

        @-webkit-keyframes spinner {
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes spinner {
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

    </style>

    <!-- Vendor Styles-->
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/simplebar/dist/simplebar.min.css') }}" />
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/flatpickr/dist/flatpickr.min.css') }}" />
    @stack('pre-css')

    <!-- Main Theme Styles + Bootstrap-->
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/theme.min.css') }}">
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/custom.css') }}">

    @stack('css')
</head>

<body>
    <div id="app">
        <div class="page-loading active">
            <div class="page-loading-inner">
                <div class="page-spinner"></div>
                <span>Loading...</span>
            </div>
        </div>

        <main class="page-wrapper d-flex flex-column">
            @include('layouts.components.plain_header')
            @include('components.alert-internet-explorer')
            @yield('content')
        </main>

        @include('layouts.components.plain_footer')
    </div>

    <!-- Page loading scripts-->
    <script src="{{ asset('js/page-load.js') }}" defer></script>

    <!-- Vendor scripts: js libraries and plugins-->
    <script src="{{ asset('js/script.js') }}" defer></script>
    <script src="{{ asset('around/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('around/vendor/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('around/vendor/flatpickr/dist/flatpickr.min.js') }}"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ja.js"></script>
    <script src="{{ asset('around/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>
    <!-- Main theme script-->
    <script src="{{ asset('around/js/theme.min.js') }}"></script>

    {{-- Cordova Assets --}}
    @if(CordovaService::hasCookie())
        {!! CordovaService::injectCoreAssets() !!}
        <script src="{{ mix('js/dist/cordova/app.js') }}" defer></script>
    @endif

    @stack('js')
</body>

</html>
