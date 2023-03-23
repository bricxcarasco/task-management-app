@extends('layouts.main')

@section('content')
    <receipt-edit
        :rio='@json($rio)'
        :service='@json($service)'
        :form='@json($form)'
        :form_basic_setting='@json($formBasicSetting)'
    />
@endsection

@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}" />
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/imagecanvas-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush
