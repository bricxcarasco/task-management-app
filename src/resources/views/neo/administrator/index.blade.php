@extends('layouts.main')

@section('content')
<div class="container position-relative pb-4 mb-md-3 home--height pt-6">
    <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
            <neo-administrator
                :neo_data='@json($neo)'
                :owner_data='@json($owner)'
                :administrators_data='@json($administrators)'
                :members_data='@json($members)'
                :neo_profile_link='@json($neoProfileLink)'
                :transfer_owner_link='@json($transferOwnerLink)'
            />
        </div>
    </div>
</div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush
