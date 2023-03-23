@php
    $scheduleText = config('bphero.maintenance.schedule');
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- SEO Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon and Touch Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('around/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('around/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('around/img/favicon/favicon-16x16.png') }}">

    <meta name="msapplication-TileColor" content="#766df4">
    <meta name="theme-color" content="#ffffff">

    <!-- Main Theme Styles + Bootstrap-->
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/theme.min.css') }}">
    <link rel="stylesheet" media="screen" href="{{ asset('around/css/custom.css') }}">
</head>
<body>
    <div id="app">
        <main class="page-wrapper d-flex flex-column">
            @include('layouts.components.plain_header')
            <div class="container py-5 py-sm-6 py-md-7">
                <div class="row justify-content-center pt-5">
                    <div class="col-lg-7 col-md-9 col-sm-11 py-5">
                        <h1 class="h2 text-center">
                            <i class="ai-tool me-2"></i>
                            {{ __('maintenance.header') }}
                        </h1>
                        <div class="border-top text-center mt-4 pt-4">
                            {{-- Display maintenance schedule --}}
                            @if (!empty($scheduleText))
                                <p class="fs-sm">【{{ __('maintenance.schedule') }}】</p>
                                <p class="fs-sm">{{ $scheduleText }}</p>
                            @endif
                            <p class="fs-sm pt-2">
                                {{ __('maintenance.paragraph') }}<br>
                                {{ __('maintenance.subparagraph') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('layouts.components.plain_footer')
    </div>
</body>
</html>
