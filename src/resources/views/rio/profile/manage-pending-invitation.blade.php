@extends('layouts.main')

@section('content')
    <rio-pending-invitation
        :invitation_lists='@json($invitationLists)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush
