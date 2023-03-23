<div class="modal fade" id="update-neo-group-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            {{-- Modal form --}}
            <form id="update-group-form" action="" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Group name change') }}</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-fn">
                                {{ __('Group name') }} {{ __('(required)') }}
                                <sup class="text-danger ms-1">*</sup>
                            </label>
                            <input class="form-control" type="text" name="group_name">
                            <div class="invalid-feedback">
                                <strong class="error-message"></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-success btn-shadow btn-sm js-update-group" type="button">
                        {{ __('Save') }}
                    </button>
                    <button class="btn btn-secondary btn-shadow btn-sm" type="button" data-bs-dismiss="modal"
                        aria-label="Close">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(function() {
            /**
             * On update group name
             */
            function executeUpdate(event) {
                event.preventDefault();

                const form = '#update-group-form';
                const url = $(form).attr('action');
                const inputSelector = $(form + ' input[name=group_name]');
                const errorSelector = $(form + ' strong.error-message');
                const loader = $('.js-section-loader');
                const data = {
                    group_name: inputSelector.val()
                };

                // Start loader
                loader.removeClass('d-none');

                $.ajax({
                    type: 'PUT',
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

                        if (response.status_code === 422) {
                            // Render validation error
                            inputSelector.addClass('is-invalid');
                            errorSelector.text(response.data.group_name);
                        } else {
                            console.error(error);
                            loader.addClass('d-none');
                        }

                        // End loader
                        loader.addClass('d-none');
                    },
                });
            }

            $('#update-group-form').on('submit', function(event) {
                executeUpdate(event);
            });

            $('.js-update-group').unbind().bind('click', function(event) {
                executeUpdate(event);
            });
        });
    </script>
@endpush
