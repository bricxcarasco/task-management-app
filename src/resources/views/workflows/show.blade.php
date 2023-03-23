@extends('layouts.main')

@section('content')
    <workflow-details :workflow='@json($workflow)' :attachments='@json($attachments)'
        :reaction_selections='@json($reactionsSelection)' :rio='@json($rio)'
        :user_type='@json($userType)' />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/workflows/index.js') }}" defer></script>
@endpush
