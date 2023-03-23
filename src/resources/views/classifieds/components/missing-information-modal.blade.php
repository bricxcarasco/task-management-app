<div class="modal fade" id="missing-info-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <h4 class="modal-title ">
                    {{ __('Card payment receiving account setting (Stripe linkgge)') }}
                </h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="mx-4 my-4">
                    <span>{{ __('To complete the Stripe card payment setup, you need to register your company information on Stripe.') }}</span>
                </div>
                <div class="mx-4 my-3 mt-5 text-center">
                    <form method="POST" action="{{ route('classifieds.settings.save-card-payment') }}" novalidate>
                        @csrf
                        <button class="btn btn-primary js-card-payment-submit" type="submit">
                            {{ __('Continue Card Payment setup') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $('.js-card-payment-submit').unbind().bind('click', function(event) {
            const loader = $('.js-section-loader');

            // Start loader
            loader.removeClass('d-none');
        });
    </script>
@endpush
