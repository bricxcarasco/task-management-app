<div class="modal fade" id="delete-confirmation-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            {{-- Modal form --}}
            <form id="delete-confirm-form" action="" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Delete draft article') }}</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ __('Do you want to delete this draft article') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-danger btn-shadow btn-sm js-confirm-delete" type="button">
                        {{ __('Delete') }}
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
             * On delete confirmation
             */
            $('.js-confirm-delete').unbind().bind('click', function(event) {
                event.preventDefault();

                const url = $('#delete-confirm-form').attr('action');
                const loader = $('.js-section-loader');

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
                        // Reload page
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error(error);
                        loader.addClass('d-none');
                    },
                });
            });
        });
    </script>
@endpush
