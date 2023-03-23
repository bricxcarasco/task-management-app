@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        {{-- Alert --}}
        @include('components.alert')

        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    {{-- Connection name & tabs section --}}
                    @include('connection.components.name-tabs-section', [
                        'fullname' => $rio->full_name,
                        'active_tab' => 'group'
                    ])

                    <div class="text-end mb-2">
                        <a href="{{ route('connection.groups.create') }}" class="btn btn-link">
                            <i class="me-2 ai-plus"></i>
                            {{ __('Create a connection group') }}
                        </a>
                    </div>

                    {{-- Connection participation & request tabs section --}}
                    @include('connection.components.participating-request-tabs', [
                        'active_tab' => 'participating'
                    ])

                    <div class="tab-content">
                        <div class="tab-pane fade active show">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="mb-0" style="flex: 1;">
                                    {{ __('Cases in total', ['case' => $totalConnections]) }}

                                    {{-- Connection Group Directions --}}
                                    <x-function-info-button>
                                        <h5 class="border-bottom">RIOつながりグループメンバー招待</h5>
                                        <p class="fs-sm mb-4">RIOが作成したグループに参加させたいつながりのあるRIOのみを招待することができます。</p>
                                        <h6 class="border-bottom">使い方</h6>
                                        <p class="fs-xs">
                                            1. ホーム画面のダッシュボードにて「つながり」をタップします。
                                            <br>
                                            2. 画面上部にあるメニューの中から「つながりグループ」をタップします。
                                            <br>
                                            3. 対象 のグループチャットの「...」アイコンをタップし、「メンバー招待」をタップします。
                                            <br>
                                            4. 招待したいRIOのの「招待する」リンクをタップし、「メンバー招待」モーダルが表示されます。
                                            <br>
                                            5. メッセージを記入して「登録」をタップします。
                                        </p>
                                    </x-function-info-button>
                                </p>
                            </div>
                            @if ($connectionList->isNotEmpty())
                                <div class="card p-4 shadow mt-2">
                                    <div class="connection__wrapper">
                                        <ul class="connection__lists list-group list-group-flush mt-2">
                                            @foreach ($connectionList as $connection)
                                                <li class="list-group-item px-0 py-2 position-relative list--white">
                                                    <p class="c-primary ms-2 mb-0 mt-2 float-start">
                                                        {{ $connection->group->group_name }}
                                                    </p>

                                                    <div class="float-end">
                                                        <a href="{{ route('chat.message.index', $connection->group->chat_id) }}" class="btn btn-link p-2" target="_blank">{{ __('Chat') }}</a>

                                                        @if ($connection->group->is_owner)
                                                            {{-- Group list menu modal --}}
                                                            @include('connection.components.group-list-menu-modal', [
                                                                'connection' => $connection
                                                            ])
                                                        @else
                                                            {{-- Leave group option --}}
                                                            <button class="btn btn-link p-2 js-leave-group-btn"
                                                                data-action="{{ route('connection.group-user.delete', ['user' => $connection->id]) }}">
                                                                {{ __('Exit') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <p class="text-center my-5">
                                    {{ __('No connected groups') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.delete-confirmation-modal')
    @include('components.leave-confirmation-modal')
    @include('connection.components.group-update-modal')
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/connection-group/index.js') }}" defer></script>
@endpush

@push('js')
    <script>
        $(function() {
            updateGroupModalHandler();
            deleteGroupModalHandler();
            leaveGroupModalHandler();
        });

        /**
         * Connection group update name modal handler
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
                $('#update-connection-group-modal').modal('show');
            });
        }

        /**
         * Connection group delete modal handler
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
         * Leave group connection handler
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
    </script>
@endpush
