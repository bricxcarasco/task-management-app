@extends('layouts.main')
{{-- Flash Alert --}}
@include('components.alert')
@section('content')
    <all-product-list :rio='@json($rio)' :service='@json($service)' />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
