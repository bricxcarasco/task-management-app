@extends('layouts.main')

@section('content')
    <create-article
        :rio='@json($rio)'
        :service='@json($service)'
        :directory_id='@json($directoryId)'
        :knowledge='@json($knowledge)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/knowledges/index.js') }}" defer></script> 
@endpush