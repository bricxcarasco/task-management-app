@extends('layouts.chat')

@section('content')
    <view-conversation
        :rio='@json($rio)'
        :service='@json($service)'
        :contact='@json($contact)'
        :receiver='@json($receiver)'
        :is_buyer='@json($isBuyer)'
        :product='@json($product)'
        :settings='@json($settings)'
        :bank_accounts='@json($bankAccounts)'
    />
@endsection

@push('css')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/classifieds/index.js') }}" defer></script>
@endpush
