@extends('layouts.main')

@section('content')
    @include('components.alert')
    <knowledge-list
        :knowledge='@json($knowledge)'
        :rio='@json($rio)'
        :service='@json($service)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/knowledges/index.js') }}" defer></script>
@endpush
