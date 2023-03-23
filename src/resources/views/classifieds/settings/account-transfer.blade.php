@extends('layouts.main')

@section('content')
    <account-transfer :service='@json($service)' :service_name='@json($serviceName)' />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
