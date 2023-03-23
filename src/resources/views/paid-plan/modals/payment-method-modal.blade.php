<div class="modal fade" id="paid-plan-payment-modal" aria-hidden="true" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{ __('Payment method selection') }}
                </h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#cc-input-modal">
                        {{ __('Credit card') }}
                    </a>
                    <!-- <a href="#" class="list-group-item">
                        {{ __('Bank transfer') }}
                    </a> -->
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <!-- <p class="text-danger">
                    {{ __('If you would like to pay annually, please select bank transfer') }}
                </p> -->
            </div>
        </div>
    </div>
</div>
