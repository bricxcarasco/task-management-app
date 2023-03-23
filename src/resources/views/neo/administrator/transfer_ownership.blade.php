@extends('layouts.main')

@section('content')
<div class="container position-relative pb-4 mb-md-3 home--height pt-6">
    <div class="row">
        {{-- Flash Alert --}}
        @include('components.alert')
        
        <div class="col-12 offset-md-3 col-md-9">
            <neo-transfer-ownership
                :neo_data='@json($neo)'
                :owner_data='@json($owner)'
                :members_data='@json($members)'
                :neo_administrator_link='@json($neoAdministratorLink)'
                :transfer_ownership_post='@json($transferOwnershipPost)'
            />
        </div>
    </div>
</div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush
