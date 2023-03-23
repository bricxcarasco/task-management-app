@extends('layouts.main')

@section('content')
    <test-room />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/chat/index.js') }}" defer></script>
@endpush
