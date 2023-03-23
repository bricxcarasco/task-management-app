<div class="modal fade" id="participate-confirmation-modal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            {{-- Modal form --}}
            <form id="participate-confirm-form" action="" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ __('Are you joining this group') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-success btn-shadow btn-sm js-confirm-participate" type="button">
                        {{ __('Participate Button') }}
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
             * On paricipation confirmation
             */
            $('.js-confirm-participate').unbind().bind('click', function(event) {
                event.preventDefault();

                const url = $('#participate-confirm-form').attr('action');
                const loader = $('.js-section-loader');

                // Start loader
                loader.removeClass('d-none');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url,
                    beforeSend: function(request) {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');
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

                        if (response.status_code == 404) {
                            // Reload page
                            window.location.reload();
                        }

                        loader.addClass('d-none');
                    },
                });
            });
        });
    </script>
@endpush
