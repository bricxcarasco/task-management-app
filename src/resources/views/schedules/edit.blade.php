@extends('layouts.main')

@section('content')
    <schedule-edit
        :rio='@json($rio)'
        :service='@json($service)'
        :time_selection='@json($timeSelections)'
        :service_selection='@json($serviceSelections)'
        :connected_list='@json($connectedList)'
        :owner='@json($owner)'
        :schedule='@json($schedule)'
        :current_guests='@json($currentGuests)'
        :member_list='@json($memberList)'
        :merged_list='@json($mergedList)'
        :selected='@json($selected)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/schedules/index.js') }}" defer></script>
@endpush
