<div class="modal fade" id="unset-confirmation-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <form method="POST" action="{{ route('classifieds.settings.unset-card-payment') }}" novalidate>
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ __('Unset stripe') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-danger btn-shadow btn-sm js-confirm-unset" type="submit">
                        {{ __('Lift') }}
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
            $('.js-confirm-unset').unbind().bind('click', function(event) {
                const loader = $('.js-section-loader');

                // Start loader
                loader.removeClass('d-none');
            });
        });
    </script>
@endpush