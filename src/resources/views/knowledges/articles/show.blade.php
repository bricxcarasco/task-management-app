@extends('layouts.main')

@section('content')
    @include('components.alert')

    <article-detail
        :rio='@json($rio)'
        :service='@json($service)'
        :article='@json($knowledge)'
        :comments='@json($comments)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/knowledges/index.js') }}" defer></script>
@endpush
