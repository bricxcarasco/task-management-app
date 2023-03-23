<div class="modal fade" id="group-member-list-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <h4 class="modal-title ">{{ __('Member list') }}</h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-1">
                {{-- Hidden group id --}}
                <input type="hidden" value="">

                {{-- Member list JS container --}}
                <div class="js-group-member-list connection__wrapper overflow-y-scroll"></div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(function() {
            handleGroupMemberList();
            handleRemoveMember();
        });

        /**
         * Get member list on modal open handler
         */
        function handleGroupMemberList() {
            $('#group-member-list-modal').unbind().bind('show.bs.modal', function() {
                handleGetMembers();
            });
        }

        /**
         * Remove group member handler
         */
        function handleRemoveMember() {
            $('.js-group-member-list').unbind().bind('click', '.js-remove-member-btn', function(event) {
                event.preventDefault();

                const url = $(event.target).data('action');
                const loader = $('.js-section-loader');

                if (url !== undefined) {
                    // Start loader
                    loader.removeClass('d-none');

                    $.ajax({
                        type: 'DELETE',
                        dataType: 'json',
                        url,
                        beforeSend: function(request) {
                            let csrfToken = $('meta[name="csrf-token"]').attr('content');
                            let lang = $('html').attr('lang');
                            request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                            request.setRequestHeader('Accept-Language', lang);
                        },
                        success: function(data) {
                            handleGetMembers();
                        },
                        error: function(error) {
                            let response = error.responseJSON;

                            if (response.status_code == 404) {
                                // Reload page
                                window.location.reload();
                            }

                            loader.addClass('d-none');
                        },
                    });
                }
            });
        }

        /**
         * Get all member list handler
         */
        function handleGetMembers() {
            const url = $('#group-member-list-modal input[type=hidden]').val();
            const memberListContainer = $('.js-group-member-list');
            const loader = $('.js-section-loader');

            // Empty member list
            memberListContainer.empty();
            memberListContainer.addClass('overflow-y-scroll');

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
                    let members = response.data.members;

                    if (members.length > 0) {
                        let memberList = '';

                        // Loop through all group members
                        $.each(members, function() {
                            memberList += `
                                <li class="list-group-item px-0 py-2 position-relative list--white px-2">
                                    <div class="float-start">
                                        <img class="rounded-circle me-2 d-inline-block img--profile_image_sm"
                                            src="${this.rio_details?.rio_profile.profile_image}" alt="Product" width="40">
                                        <span class="fs-xs c-primary ms-2">${this.rio.full_name}</span>
                                    </div>
                            `;

                            // Remove member button
                            if (this.rio_id !== user.rio_id) {
                                memberList += `
                                    <div class="float-end">
                                        <button class="btn-delete mx-3 mt-2 js-remove-member-btn" type="button" data-action="${this.leave_url}"></button>
                                    </div>
                                </li>`;
                            }

                            memberList += `</li>`;
                        });

                        let wrapper = `
                            <ul class="connection__lists list-group list-group-flush mt-2">
                                ${memberList}
                            </ul>
                        `;

                        // Append list to DOM
                        memberListContainer.append(wrapper);
                    } else {
                        let message = "{{ __('No members') }}";

                        // Append no members message to DOM
                        memberListContainer.removeClass('overflow-y-scroll');
                        memberListContainer.append(
                            `<div class="connection__wrapper">
                                <p class="text-center my-5">${message}</p>
                            </div>`
                        );
                    }
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
    </script>
@endpush
