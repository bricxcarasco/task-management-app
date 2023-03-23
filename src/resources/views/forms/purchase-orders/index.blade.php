@php
use App\Enums\Form\Types;
@endphp

@extends('layouts.main')

@section('content')
    {{-- Flash Alert --}}
    @include('components.alert')

    <form-list
        :rio='@json($rio)'
        :service='@json($service)'
        :type="@json(Types::PURCHASE_ORDER)"
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush
