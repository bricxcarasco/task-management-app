@php
    use App\Services\CordovaService;
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- SEO Meta Tags-->
    <meta name="description" content="Around - Multipurpose Bootstrap Template">
    <meta name="keywords"
        content="bootstrap, business, consulting, coworking space, services, creative agency, dashboard, e-commerce, mobile app showcase, multipurpose, product landing, shop, software, ui kit, web studio, landing, html5, css3, javascript, gallery, slider, touch, creative">
    <meta name="author" content="Createx Studio">
    <!-- Viewport-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        .hidden-element {
            display:none;
        }
        .label {
            display: inline-block;
            font-weight: 500;
            line-height: 1.5;
            color: white;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.5625rem 1.25rem;
            font-size: 1rem;
            border-radius: 0.75rem;
            transition: color 0.25s ease-in-out, background-color 0.25s ease-in-out,
                border-color 0.25s ease-in-out;
        }
        .label-success {
            background-color: #A8D08C;
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

    <script>
        /**
         * Go back to previous page
         */
        function back() {
            window.history.back();
        }
    </script>
    <!-- Vendor Styles-->
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/simplebar/dist/simplebar.min.css') }}" />
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/flatpickr/dist/flatpickr.min.css') }}"/>
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/flatpickr/dist/month-select.css') }}" />

    @stack('pre-css')

    <!-- Calendar CSS -->
    <link rel="stylesheet" href="{{ asset('around/vendor/fullcalendar/main.min.css') }}">

    <!-- Slider CSS -->
    <link rel="stylesheet" media="screen" href="{{ asset('around/vendor/tiny-slider/dist/tiny-slider.css') }}">

    <!-- Main Theme Styles + Bootstrap-->
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/theme.min.css') }}">
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/custom.css') }}">

    @stack('css')
</head>

<body class="is-sidebar">
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="page-spinner"></div><span>Loading...</span>
        </div>
    </div>

    @include('layouts.components.main_header')
    @php
        $urls = [
            'rio/profile/introduction',
            'rio/profile/information',
            'neo/profile/introduction',
            'neo/profile/information',
            'neo/profile/participants',
            'neo/profile/groups',
            'document/files/',
            'document/shared/files/',
        ];
    @endphp
    @if (strlen(str_replace($urls, '', Request::path())) == strlen(Request::path()))
        <div class="position-relative container sidebar">
            <div class="w-25 d-none d-md-block sidebar__wrapper">
                @include('layouts.components.main_sidebar')
            </div>
        </div>
    @endif
    <div id="app" class="main">
        <main class="page-wrapper d-flex flex-column">
            @include('components.toast')

            {{-- Modals --}}
            @stack('modals')
            @include('components.function-info-modal')
            @include('components.alert-internet-explorer')

            {{-- Main Content --}}
            @yield('content')
        </main>
    </div>
    @include('layouts.components.main_footer')

    <!-- Page loading scripts-->
    <script src="{{ asset('js/page-load.js') }}"></script>

    <!-- Vendor scripts: js libraries and plugins-->
    <script src="{{ asset('around/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('around/vendor/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('around/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ asset('around/vendor/flatpickr/dist/flatpickr.min.js') }}"></script>
    <script src="{{ asset('around/vendor/flatpickr/dist/l10n/ja.js') }}"></script>
    <script src="{{ asset('around/vendor/flatpickr/dist/month-select.js') }}"></script>
    <script src="{{ asset('around/vendor/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('around/vendor/fullcalendar/locales/ja.js') }}"></script>
    <script src="{{ asset('around/vendor/tiny-slider/dist/min/tiny-slider.js') }}"></script>

    <!-- Main theme script-->
    <script src="{{ asset('around/js/theme.min.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}" defer></script>

    {{-- Cordova Assets --}}
    @if(CordovaService::hasCookie())
        {!! CordovaService::injectCoreAssets() !!}
        <script src="{{ mix('js/dist/cordova/app.js') }}" defer></script>
    @endif

    @stack('vuejs')

    <script src="{{ asset('js/script.js') }}" defer></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    @stack('js')

</body>
</html>
