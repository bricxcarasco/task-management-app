@extends('layouts.main')

@section('content')
    <chat-rooms 
        :talk_subjects='@json($talkSubjects)'
        :session='@json($session)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/chat/index.js') }}" defer></script>
@endpush
