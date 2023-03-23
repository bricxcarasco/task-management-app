@extends('layouts.main')

@section('content')
    @include('components.alert')
    <registered-product-list
        :rio='@json($rio)'
        :service='@json($service)'
        :has_payment='@json($hasPaymentSetting)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
