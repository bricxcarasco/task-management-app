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
                        @include('layouts.components.neo.profile_tab_sub_links', ['active' => __('Group List')])
                    @endif

                    @if ($neo->is_authorized_user)
                        <div class="text-end mb-2">
                            <button class="btn btn-link js-create-group-btn"
                                data-action="{{ route('neo.profile.group.create', ['neo' => $neo->id]) }}">
                                <i class="me-2 ai-plus"></i>
                                {{ __('Create a group') }}
                            </button>
                        </div>
                    @endif

                    <div class="tab-content">
                        <div class="tab-pane fade active show">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="mb-0" style="flex: 1;">
                                    {{ __('Cases in total', ['case' => $totalGroups]) }}
                                </p>
                            </div>

                            @if ($groups->isNotEmpty())
                                <div class="card p-4 shadow mt-2">
                                    <div class="connection__wrapper">
                                        <ul class="connection__lists list-group list-group-flush mt-2">
                                            @foreach ($groups as $group)
                                                <li class="list-group-item px-0 py-2 position-relative list--white">
                                                    <p class="c-primary ms-2 mb-0 float-start">
                                                        {{ $group->group_name }}
                                                    </p>

                                                    <div class="float-end">
                                                        {{-- Join/Leave group button --}} 
                                                        @if(($neoBelong && $isParticipant && $serviceSelected && ($neoBelong->status ?? null) === NeoBelongStatusType::AFFILIATE) || (in_array(($neoBelong->role ?? null), [RoleType::OWNER, RoleType::ADMINISTRATOR])))
                                                            @if ($group->is_member)
                                                                <button class="btn btn-blue js-leave-group-btn"
                                                                    data-action="{{ route('neo.profile.group.leave', ['id' => $group->id]) }}">
                                                                    {{ __('Exit Button') }}
                                                                </button>
                                                            @else
                                                                <button
                                                                    class="btn btn-link btn-blue-outline js-participate-group-btn"
                                                                    data-action="{{ route('neo.profile.group.join', ['id' => $group->id]) }}">
                                                                    {{ __('Participate Button') }}
                                                                </button>
                                                            @endif
                                                        @endif

                                                        @if ($neo->is_authorized_user)
                                                            {{-- Group menu component --}}
                                                            @include('neo.profile.components.group-list-menu-modal', [
                                                            'group' => $group
                                                            ])
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <p class="text-center my-5">
                                    {{ __('No groups') }}
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
    @include('components.delete-confirmation-modal')
    @include('components.leave-confirmation-modal')
    @include('components.participate-confirmation-modal')
    @include('neo.profile.components.group-member-list-modal')
    @include('neo.profile.components.add-group-member-modal')
    @include('neo.profile.components.create-group-modal')
    @include('neo.profile.components.update-group-modal')
    @include('layouts.components.neo.exit_neo_modal')
    @include('layouts.components.neo.profile_apply_connection_modal')
    @include('layouts.components.neo.profile_connection_dialogue_modal')
    @include('layouts.components.neo.profile_cancel_connection_modal')
    @include('layouts.components.neo.profile_disconnect_connection_modal')
@endsection

@push('js')
    <script>
        $(function() {
            createGroupModalHandler();
            updateGroupModalHandler();
            deleteGroupModalHandler();
            leaveGroupModalHandler();
            participateGroupModalHandler();
            fetchMemberListModalHandler();
            addGroupMemberModalHandler();
        });

        /**
         * Create NEO group modal handler
         */
        function createGroupModalHandler() {
            $('.js-create-group-btn').on('click', function() {
                let form = '#create-group-form';
                let nameSelector = $(form + ' input[name=group_name]');
                let errorSelector = $(form + ' strong.error-message');

                // Set group name & form action
                nameSelector.val($(this).data('name'));
                $(form).attr('action', $(this).data('action'));

                // Reset validation error on modal
                nameSelector.removeClass('is-invalid');
                errorSelector.text('');

                // Show modal
                $('#create-neo-group-modal').modal('show');
            });
        }

        /**
         * Group update name modal handler
         */
        function updateGroupModalHandler() {
            $('.js-update-group-btn').on('click', function() {
                let form = '#update-group-form';
                let nameSelector = $(form + ' input[name=group_name]');
                let errorSelector = $(form + ' strong.error-message');

                // Set group name & form action
                nameSelector.val($(this).data('name'));
                $(form).attr('action', $(this).data('action'));

                // Reset validation error on modal
                nameSelector.removeClass('is-invalid');
                errorSelector.text('');

                // Show modal
                $('#update-neo-group-modal').modal('show');
            });
        }

        /**
         * Group delete modal handler
         */
        function deleteGroupModalHandler() {
            $('.js-delete-group-btn').on('click', function() {
                let formSelector = $('#delete-confirm-form');

                // Set form action
                formSelector.attr('action', $(this).data('action'));

                // Show modal
                $('#delete-confirmation-modal').modal('show');
            });
        }

        /**
         * Leave group handler
         */
        function leaveGroupModalHandler() {
            $('.js-leave-group-btn').on('click', function() {
                let formSelector = $('#leave-confirm-form');

                // Set form action
                formSelector.attr('action', $(this).data('action'));

                // Show modal
                $('#leave-confirmation-modal').modal('show');
            });
        }

        /**
         * Participate group handler
         */
        function participateGroupModalHandler() {
            $('.js-participate-group-btn').on('click', function() {
                let formSelector = $('#participate-confirm-form');

                // Set form action
                formSelector.attr('action', $(this).data('action'));

                // Show modal
                $('#participate-confirmation-modal').modal('show');
            });
        }

        /**
         * Fetch all member lists & display in modal
         */
        function fetchMemberListModalHandler() {
            $('.js-member-list-btn').on('click', function() {
                let inputSelector = $('#group-member-list-modal input[type=hidden]');

                // Set url in hidden field
                inputSelector.val($(this).data('action'));

                // Show modal
                $('#group-member-list-modal').modal('show');
            });
        }

        /**
         * Fetch all participants that can be added to a group in modal
         */
        function addGroupMemberModalHandler() {
            $('.js-add-member-btn').on('click', function() {
                let inputSelector = $('#add-group-member-modal input[type=hidden]');

                // Set url in hidden field
                inputSelector.val($(this).data('action'));

                // Show modal
                $('#add-group-member-modal').modal('show');
            });
        }
    </script>
@endpush
