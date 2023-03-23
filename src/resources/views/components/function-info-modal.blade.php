@auth
    @if (auth()->user()->feature_description ?? false)
        <div
            class="modal fade"
            id="function-info-modal"
            tabindex="-1"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button
                            class="btn-close"
                            type="button"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 js-function-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth

@push('js')
    <script>
        $(document).ready(function() {
            const modalElement = $('#function-info-modal');

            if (modalElement.length > 0) {
                // When modal exists, display info buttons
                $('.js-function-info-modal').removeClass('d-none');

                $(document).on('click', '.js-function-info-modal', function () {
                    // Get function info modal and content that will be placed
                    const modalElement = $('#function-info-modal');
                    const content = $(this).parent().children('.js-function-info-content').html();

                    // Get currently shown modal if it exists
                    const shownModalElement = $('.modal.show');

                    if (shownModalElement.length > 0) {
                        // Attach event listener for show function modal on close existing
                        shownModalElement.one('hidden.bs.modal', function () {
                            modalElement.modal('show');
                        })

                        // Close existting
                        shownModalElement.modal('hide');

                        // On close of function modal, show previous modal
                        modalElement.one('hidden.bs.modal', function () {
                            shownModalElement.modal('show');
                        })
                    } else {
                        modalElement.modal('show');
                    }

                    // Inject function content
                    modalElement.find('.js-function-content').html(content);
                });
            }
        })
    </script>
@endpush
