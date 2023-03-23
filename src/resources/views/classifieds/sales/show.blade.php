@extends('layouts.main')

@section('content')
    <view-product :rio='@json($rio)' :service='@json($service)'
        :product='@json($product)' />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
