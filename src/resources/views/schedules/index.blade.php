@extends('layouts.main')

@section('content')
    <schedule-list
        :rio='@json($rio)'
        :service='@json($service)'
        :invitations_count='@json($invitationsCount)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/schedules/index.js') }}" defer></script>
@endpush
