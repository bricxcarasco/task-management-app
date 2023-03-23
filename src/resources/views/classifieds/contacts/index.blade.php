@extends('layouts.main')

@section('content')
    <contact-messages-list :rio='@json($rio)' :service='@json($service)' />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
