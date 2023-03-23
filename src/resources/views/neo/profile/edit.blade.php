@extends('layouts.main')

@section('content')
    <neo-profile-edit
        :user='@json($user)'
        :neo='@json($neo)'
        :neo_profile='@json($neoProfile)'
        :prefectures='@json($prefectures)'
        :years_of_experiences='@json($yearsOfExperiences)'
        :business_categories='@json($businessCategories)'
        :organization_types='@json($organizationTypes)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush