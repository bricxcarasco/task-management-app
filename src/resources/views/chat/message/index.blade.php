@extends('layouts.chat')

@section('content')
    <chat-message-room
        :user='@json($user)'
        :rio='@json($rio)'
        :talk_subject='@json($talkSubject)'
        :talk_subjects='@json($talkSubjects)'
        :chat='@json($chat)'
        :participant='@json($participant)'
    />
@endsection

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/chat/index.js') }}" defer></script>
@endpush