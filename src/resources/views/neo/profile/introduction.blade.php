@php
    use App\Enums\Neo\RoleType;
    use App\Enums\Neo\NeoBelongStatusType;
    use App\Enums\Neo\AcceptParticipationType;
    use App\Enums\Neo\RestrictConnectionType;
    use App\Enums\ServiceSelectionTypes;
    use App\Enums\Neo\ConnectionStatusType;
@endphp

@extends('layouts.main')

@section('content')

@include('layouts.components.neo.management_menu_modal')
@include('layouts.components.neo.profile_apply_modal')
@include('layouts.components.neo.profile_cancel_participation_modal', ['neoId' => $neo->id])
@include('layouts.components.neo.profile_dialogue_modal')
@include('layouts.components.neo.exit_neo_modal')
@include('layouts.components.neo.profile_apply_connection_modal')
@include('layouts.components.neo.profile_connection_dialogue_modal')
@include('layouts.components.neo.profile_cancel_connection_modal')
@include('layouts.components.neo.profile_disconnect_connection_modal')
@include('components.profile_reference_url_modal')

<div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">

    {{-- Flash Alert --}}
    @include('components.alert')

    <div class="row pt-2">
        <div class="col-12">
            <div class="card p-4">
                @include('neo.profile.components.profile-avatar')
                <div class="d-flex" id="introduction-buttons">
                    @if(!$neoBelong || ($serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id !== $neo->id))
                        @if(!$isParticipant && $serviceSelected && $serviceSelected->type !== ServiceSelectionTypes::NEO && $privacySetting->accept_belongs === AcceptParticipationType::ACCEPT_PARTICIPATION)
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="participate-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#apply" style="width: 200px;">{{ __('Application for participation') }}</button>
                        @endif
                    @elseif($neoBelong && $isParticipant && $serviceSelected && $serviceSelected->type !== ServiceSelectionTypes::NEO && $privacySetting->accept_belongs === AcceptParticipationType::ACCEPT_PARTICIPATION && ($neoBelong->status ?? null) === NeoBelongStatusType::APPLYING)
                        <button class="fs-xs btn btn-primary mt-2 me-2" id="cancel-participate-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#cancel-participation">{{ __('Cancellation of participation application') }}</button>
                    @elseif((!in_array(($neoBelong->role ?? null), [RoleType::OWNER])) && (($neoBelong->status ?? null) === NeoBelongStatusType::AFFILIATE))
                        <button class="fs-xs btn btn-primary mt-2 me-2" id="exitNeoBTN" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#exit-modal" style="width: 200px;">{{ __('Exit from this NEO') }}</button>
                    @endif
                    @if(($serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id !== $neo->id) ||
                        ($serviceSelected->type === ServiceSelectionTypes::RIO &&
                            ($neo->id !== $user->rio->id || empty($serviceSelected->data->organization_type)) &&
                            (!in_array(($neoBelong->role ?? null), [RoleType::OWNER]))))
                        @if(!$neoRequest && !$neoConnection && $privacySetting->accept_connections === RestrictConnectionType::ACCEPT_CONNECTION)
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#apply-connection" style="width: 200px;">{{ __('Connection application') }}</button>
                        @endif
                        @if(!$neoRequest && $neoConnection && $neoConnection->status === ConnectionStatusType::APPLYING)
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="cancel-connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#cancel-connection">{{ __('Cancellation of connection application') }}</button>
                        @endif
                        @if(($neoConnection && $neoConnection->status === ConnectionStatusType::CONNECTION_STATUS) ||
                            (($neoRequest && $neoRequest->status === ConnectionStatusType::CONNECTION_STATUS)))
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="disconnect-connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#disconnect-connection" style="width: 200px;">{{ __('Disconnection') }}</button>
                        @endif
                    @endif
                </div>
            <div class="mt-4">
                @include('layouts.components.neo.profile_tab_links', ['active' => __('Introduction')])
                <div class="card p-4 shadow">
                    <p class="m-0">{!! nl2br(e($neo->neo_profile->self_introduce)) ?? '' !!}</p>
                </div>
                @if(!empty($neo->neo_profile->profile_video))
                    <h3 class="mt-4">{{ __('Profile Video') }}</h3>
                    <div class="w-100 py-5 d-flex align-items-center justify-content-center">
                        <p>{{ $neo->neo_profile->profile_video }}</p>
                    </div>
                @endif
                @if(!$neo->products->isEmpty())
                    <h3 class="mt-4">{{ __('Registered Products') }}</h3>
                    <div class="row">
                        @foreach ($neo->products as $product)
                            <div class="col-12 col-md-4 mb-2">
                                @include('layouts.components.neo.profile.product', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        $(document).on('click', '.openURLModal', function(event){
            const referenceUrl = $(this).attr('data-reference-url');
            
            $('#productReferenceURLModal').modal('show');
            $('#productReferenceURLModal input[type="hidden"]').val(referenceUrl);
        });

        $(document).on('click', '#productReferenceURLModal .redirectReferenceURL', function(event){
            const referenceUrl = $('#productReferenceURLModal input[type="hidden"]').val();
            
            window.open(referenceUrl, '_blank');

            $('#productReferenceURLModal').modal('hide');
        });
    });
</script>
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush