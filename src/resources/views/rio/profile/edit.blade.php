@extends('layouts.main')

@section('content')
    <rio-profile-edit
        :user='@json($user)'
        :rio='@json($rio)'
        :rio_profile='@json($rioProfile)'
        :rio_expert='@json($rioExpert)'
        :prefectures='@json($prefectures)'
        :years_of_experiences='@json($yearsOfExperiences)'
        :business_categories='@json($businessCategories)'
    />
@endsection

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endpush
    

@push('pre-css')
    <link rel="stylesheet" href="{{ asset('css/intlTelInput.min.css') }}">
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ asset('js/intl-tel-input/intlTelInput.min.js') }}"></script>
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush
