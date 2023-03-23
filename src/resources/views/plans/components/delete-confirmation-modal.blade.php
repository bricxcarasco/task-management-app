<div class="modal fade" id="delete-confirmation-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            {{-- Modal form --}}
            <form id="delete-confirm-form" action="" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button id="closeDeleteModal" class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ __('Delete this payment method') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-danger btn-shadow btn-sm js-confirm-delete" type="button">
                        {{ __('Delete Button') }}
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
             * Handle on modal close
             */
            let modalCloseBtn = document.querySelector('#closeDeleteModal');
            modalCloseBtn.addEventListener('click', function(event) {
                $("#delete-confirmation-modal").modal("hide");
            });

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
                    success: function(response) {
                        modalCloseBtn.click();
                        setAlert('success', response.data.message);
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
