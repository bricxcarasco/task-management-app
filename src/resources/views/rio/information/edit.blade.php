@extends('layouts.main')

@section('content')
    <rio-information-edit
        :user='@json($user)'
        :rio='@json($rio)'
        :secret_questions='@json($secretQuestions)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush