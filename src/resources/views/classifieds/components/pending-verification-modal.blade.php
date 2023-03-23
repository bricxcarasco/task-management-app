<div class="modal fade" id="pending-verification-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <h4 class="modal-title ">
                    {{ __('Verification in progress') }}
                </h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="mx-4 my-4">
                    <span>{{ __('The created Stripe account is being verified by Stripe. Please reload the page after about 1-5 minutes.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
