@extends('layouts.main')

@section('content')
    <neo-message 
        :lists='@json($lists)'
        :session='@json($session)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/chat/index.js') }}" defer></script>
@endpush
