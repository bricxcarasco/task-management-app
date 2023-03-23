@extends('layouts.main')

@section('content')
    <basic-settings :basic_settings='@json($basicSettings)' :user_image='@json($userImage)' />
@endsection

@push('css')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('pre-css')
    <link rel="stylesheet" href="{{ asset('css/intlTelInput.min.css') }}">
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ asset('js/intl-tel-input/intlTelInput.min.js') }}"></script>
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush
