<div class="modal fade" id="add-group-member-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            {{-- Modal form --}}
            <form id="add-member-form" action="" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title ">{{ __('Add member') }}</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Hidden group id --}}
                    <input type="hidden" value="">
                    <p class="mt-2 mb-5 js-group-name"></p>

                    {{-- Participant list JS container --}}
                    <p class="mt-2 mb-2">{{ __('Select participants to add') }}</p>
                    <div id="connection-requests-list-items" class="js-user-list connection__wrapper overflow-y-scroll">
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-success btn-shadow btn-sm js-add-member" type="button">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        let selectedParticipants = [];
        let currentIndex = 0;

        $(function() {
            handleParticipantList();
        });

        /**
         * Get member list on modal open handler
         */
        function handleParticipantList() {
            $('#add-group-member-modal').unbind().bind('show.bs.modal', function() {
                handleGetParticipants();
            });
        }

        /**
         * Get all member list handler
         */
        function handleGetParticipants() {
            const url = $('#add-group-member-modal input[type=hidden]').val();
            const participantContainer = $('.js-user-list');
            const groupNameContainer = $('.js-group-name');
            const loader = $('.js-section-loader');
            selectedParticipants = [];
            currentIndex = 0;

            // Empty member list
            participantContainer.empty();
            groupNameContainer.empty();
            participantContainer.addClass('overflow-y-scroll');

            // Start loader
            loader.removeClass('d-none');

            $.ajax({
                type: 'GET',
                dataType: 'json',
                url,
                beforeSend: function(request) {
                    let csrfToken = $("meta[name='csrf-token']").attr('content');
                    let lang = $('html').attr('lang');
                    request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    request.setRequestHeader('Accept-Language', lang);
                },
                success: function(response) {
                    let user = response.data.user;
                    let participants = response.data.participants;
                    let addUrl = response.data.add_url;

                    groupNameContainer.append(response.data.group_name);

                    if (participants.length > 0) {
                        let participant = '';

                        // Loop through all group members
                        $.each(participants, function() {
                            if (this.is_not_participating) {
                                participant += `
                                <li class="list-group-item px-0 py-2 position-relative list--white px-2 js-member-item">`;
                            } else {
                                participant += `
                                <li class="list-group-item px-0 py-2 position-relative list--white px-2 js-member-item" disabled>`;
                            }

                            participant += `
                                    <div class="float-start">
                                        <img class="rounded-circle me-2 d-inline-block img--profile_image_sm"
                                            src="${this.rio_profile.profile_image}" alt="Product" width="40">
                                        <span class="fs-xs c-primary ms-2">${this.full_name}</span>
                            `;

                            if (this.is_not_participating) {
                                participant += `
                                        <div class="vertical-right">
                                            <button class="btn btn-link">
                                                <i
                                                class="h2 m-0 ai-check"
                                                id="participant-${this.id}"
                                                style="display: none"
                                                ></i>
                                            </button>
                                        </div>
                                `;
                            } else {
                                participant += `
                                        <div class="vertical-right">
                                            <p class="fs-xs m-0 pe-2">{{ __('Participating') }}</p>
                                        </div>
                                `;
                            }

                            participant += `
                                    </div>
                                </li>`;
                        });

                        let wrapper = `
                            <ul class="connection__lists list-group list-group-flush mt-2">
                                ${participant}
                            </ul>
                        `;

                        // Append list to DOM
                        participantContainer.append(wrapper);

                        // Apply check/uncheck event for each participants
                        $( ".js-member-item" ).each(function(index) {
                            if ($(`#participant-${participants[index].id}`).length > 0) {
                                $(this).on("click", function(){
                                    let search = function (element) {
                                        return element === participants[index].id;
                                    };
                                    currentIndex = selectedParticipants.findIndex(search);
                                    if (currentIndex === -1) {
                                        $(this).find(`#participant-${participants[index].id}`).css('display', 'block');
                                        selectedParticipants.push(participants[index].id);
                                    } else {
                                        $(this).find(`#participant-${participants[index].id}`).css('display', 'none');
                                        selectedParticipants.splice(currentIndex, 1);
                                    }
                                });
                            }
                        });
                    } else {
                        let message = "{{ __('No members') }}";

                        // Append no members message to DOM
                        participantContainer.removeClass('overflow-y-scroll');
                        participantContainer.append(
                            `<div class="connection__wrapper">
                                <p class="text-center my-5">${message}</p>
                            </div>`
                        );
                    }

                    $('#add-member-form').attr('action', addUrl);
                },
                error: function(error) {
                    console.error(error);
                },
                complete: function() {
                    // End loader
                    loader.addClass('d-none');
                }
            });
        }

        /**
         * On add selected members
         */
        function executeAddMember(event) {
            event.preventDefault();

            const url = $('#add-member-form').attr('action');
            const loader = $('.js-section-loader');
            const data = {
                rio_id: selectedParticipants,
            };

            // Start loader
            loader.removeClass('d-none');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url,
                data,
                beforeSend: function(request) {
                    let csrfToken = $("meta[name='csrf-token']").attr('content');
                    let lang = $('html').attr('lang');
                    request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    request.setRequestHeader('Accept-Language', lang);
                },
                success: function(data) {
                    // Reload page
                    window.location.reload();
                },
                error: function(error) {
                    let response = error.responseJSON;

                    window.location.reload();
                    console.error(error);

                    // End loader
                    loader.addClass('d-none');
                },
            });
        }

        $('.js-add-member').unbind().bind('click', function(event) {
            executeAddMember(event);
        });
    </script>
@endpush
