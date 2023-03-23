@php
use App\Services\CordovaService;
@endphp

@extends('layouts.main')

@section('content')
    <electronic-contract
        :service='@json($service)'
        :available_slot='@json($availableSlot)'
        :is_app='@json(CordovaService::hasCookie())'
    />
@endsection

@push('css')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/electronic-contract/index.js') }}" defer></script>
@endpush
