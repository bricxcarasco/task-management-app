@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        {{-- Alert --}}
        @include('components.alert')

        {{-- Section loader --}}
        @include('components.section-loader', ['show' => false])
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
                    'active_tab' => 'invitation'
                    ])

                    <div class="tab-content">
                        <div class="tab-pane fade active show">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                {{ __('Cases in total', ['case' => $totalInvitations]) }}
                                
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
                            </div>

                            @if ($invitationList->isNotEmpty())
                                <div class="card p-4 shadow mt-2">
                                    <div class="connection__wrapper">
                                        <ul class="list-group">
                                            @foreach ($invitationList as $invitation)
                                                <li class="list-group-item border-0 border-bottom">
                                                    <p class="text-danger">
                                                        {{ __('Invited by', [
                                                            'name' => $invitation->group->rio->full_name,
                                                        ]) }}
                                                    </p>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>{{ $invitation->group->group_name }}</span>
                                                        <button class="btn btn-link js-member-list-btn" type="button"
                                                            data-action="{{ route('connection.groups.member-list', ['group' => $invitation->group->id]) }}">{{ __('Member list') }}</button>
                                                    </div>
                                                    <div class="p-4 bg-gray mt-2">
                                                        <p class="invite-wrapper">{{ $invitation->invite_message }}</p>
                                                    </div>
                                                    <div class="d-flex w-75 mx-auto align-center justify-content-center mt-4">
                                                        <button type="button" class="btn btn-link js-accept-btn" type="button"
                                                            data-action="{{ route('connection.group-user.accept-invitation', ['user' => $invitation->id]) }}">{{ __('Accept') }}</button>
                                                        <button type="button" class="btn btn-link js-decline-btn" type="button"
                                                            data-action="{{ route('connection.group-user.decline-invitation', ['user' => $invitation->id]) }}">{{ __('Decline') }}</button>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <p class="text-center my-5">
                                    {{ __('No invitation requests') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modals --}}
    @include('connection.components.group-member-list-modal')
@endsection

@push('js')
    <script>
        $(function() {
            fetchMemberListModalHandler();
            acceptGroupInvitationHandler();
            declineGroupInvitationHandler();
        });

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
         * Accept group connection invitation handler
         */
        function acceptGroupInvitationHandler() {
            $('.js-accept-btn').unbind().bind('click', function(event) {
                event.preventDefault();

                const loader = $('.js-section-loader');
                const url = $(this).data('action');

                // Start page loading
                loader.removeClass('d-none');

                $.ajax({
                    type: 'PUT',
                    dataType: 'json',
                    url,
                    beforeSend: function(request) {
                        let csrfToken = $("meta[name='csrf-token']").attr('content');
                        let lang = $('html').attr('lang');
                        request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        request.setRequestHeader('Accept-Language', lang);
                    },
                    complete: function() {
                        window.location.reload();
                    }
                });
            });
        }

        /**
         * Decline group connection invitation handler
         */
        function declineGroupInvitationHandler() {
            $('.js-decline-btn').unbind().bind('click', function(event) {
                event.preventDefault();

                const loader = $('.js-section-loader');
                const url = $(this).data('action');

                // Start page loading
                loader.removeClass('d-none');

                $.ajax({
                    type: 'DELETE',
                    dataType: 'json',
                    url,
                    beforeSend: function(request) {
                        let csrfToken = $("meta[name='csrf-token']").attr('content');
                        let lang = $('html').attr('lang');
                        request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        request.setRequestHeader('Accept-Language', lang);
                    },
                    success: function(response) {
                        // Reload page
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error(error);
                        loader.addClass('d-none');
                    }
                });
            });
        }
    </script>
@endpush
