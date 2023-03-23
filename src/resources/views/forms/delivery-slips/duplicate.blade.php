@extends('layouts.main')

@section('content')
    <delivery-slip-duplicate
        :rio='@json($rio)'
        :service='@json($service)'
        :form_basic_setting='@json($formBasicSetting)'
        :form='@json($form)'
    />
@endsection

@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}" />
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/imagecanvas-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush