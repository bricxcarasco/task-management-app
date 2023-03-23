@extends('layouts.main')

@section('content')
    {{-- Flash Alert --}}
    @include('components.alert')

    <form-csv-list type="@json($type)" csrf="{{ csrf_token() }}" />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>
@endpush
