@php
use App\Enums\Form\ProductTaxDistinction;
use App\Helpers\CommonHelper;
@endphp

@extends('layouts.main')

@section('content')
    <update-history
        :rio='@json($rio)'
        :service='@json($service)'
        :form='@json($form)'
        :form_type='@json($formType)'
    />
@endsection
@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}" />
@endpush
@push('vuejs')
    <script src="{{ mix('js/dist/imagecanvas-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush