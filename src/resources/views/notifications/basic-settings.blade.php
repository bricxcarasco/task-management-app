@extends('layouts.main')

@section('content')
    <basic-settings
        :user='@json($user)'
        :rio='@json($rio)'
        :mail_templates='@json($mailTemplates)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/notifications/index.js') }}" defer></script>
@endpush
