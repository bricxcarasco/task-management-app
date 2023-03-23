@extends('layouts.main')

@section('content')
    <neo-participation-invitation
        :neo='@json($neo)'
        :neo_id='@json($neoId)'
        :connected_lists='@json($connectedLists)'
    />
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush
