@extends('layouts.main')

@section('content')
    <schedule-create
        :rio='@json($rio)'
        :service='@json($service)'
        :time_selection='@json($timeSelections)'
        :service_selection='@json($serviceSelections)'
        :connected_list='@json($connectedList)'
        :member_list='@json($memberList)'
        :merged_list='@json($mergedList)'
        :owner='@json($owner)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/schedules/index.js') }}" defer></script> 
@endpush
