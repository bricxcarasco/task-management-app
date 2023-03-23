@extends('layouts.main')

@section('content')
    <task-list
        :rio='@json($rio)'
        :service='@json($service)'
        :priority_selections='@json($prioritySelections)'
        :service_selections='@json($serviceSelections)'
        :task_subject_selection='@json($taskSubjectSelection)'
        :time_selection='@json($timeSelections)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/tasks/index.js') }}" defer></script>
@endpush
