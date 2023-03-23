@php
    use \App\Enums\Neo\RoleType;
    use App\Enums\Neo\NeoBelongStatusType;
    use App\Enums\Neo\AcceptParticipationType;
    use App\Enums\Neo\RestrictConnectionType;
    use App\Enums\ServiceSelectionTypes;
    use App\Enums\Neo\ConnectionStatusType;
@endphp

@extends('layouts.main')

@section('content')
    <div class="container container--mobile">
        {{-- Alert message --}}
        @include('components.alert')

        <div class="row align-items-center pt-4">
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
                </div>
                <div class="mt-4">
                    {{-- Main tabs section --}}
                    @include('layouts.components.neo.profile_tab_links', ['active' => __('Participant')])

                    @if ($isParticipant)
                        {{-- Sub tabs section --}}
                        @include('layouts.components.neo.profile_tab_sub_links', ['active' => __('Participant List')])
                    @endif

                    <div class="tab-content">
                        <div class="tab-pane fade active show">
                            @if ($participants->isNotEmpty())
                                <div class="card p-4 shadow mt-2">
                                    <div class="connection__wrapper">
                                        <ul class="connection__lists list-group list-group-flush mt-2">
                                            @foreach ($participants as $participant)
                                                <li class="list-group-item px-0 py-2 position-relative list--white">
                                                    <div>
                                                        <span class="c-primary ms-2 mb-0 float-start d-inline-block">
                                                            <img class="rounded-circle me-2 d-inline-block img--profile_image_sm"
                                                                src="{{ $participant->rio_profile->profile_image }}"  onerror="this.src='{{ asset('images/profile/user_default.png') }}'" alt="RIO" width="40">
                                                            <span>{{ $participant->full_name }}</span>
                                                        </span>

                                                        @if ($participant->neo_belongs->role == RoleType::OWNER)
                                                            <span class="labeler float-end">{{ __('Owner') }}</span>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <p class="text-center my-5">
                                    {{ __('No participants') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('layouts.components.neo.management_menu_modal')
    @include('layouts.components.neo.profile_apply_modal')
    @include('layouts.components.neo.profile_cancel_participation_modal', ['neoId' => $neo->id])
    @include('layouts.components.neo.profile_dialogue_modal')
    @include('layouts.components.neo.exit_neo_modal')
    @include('layouts.components.neo.profile_apply_connection_modal')
    @include('layouts.components.neo.profile_connection_dialogue_modal')
    @include('layouts.components.neo.profile_cancel_connection_modal')
    @include('layouts.components.neo.profile_disconnect_connection_modal')
@endsection