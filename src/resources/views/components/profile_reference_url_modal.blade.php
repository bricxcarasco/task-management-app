<div id="productReferenceURLModal" class="modal fade" aria-hidden="true" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center fs-sm">
                    {{ __('This is a link to an external site, are you sure you want to access') }}
                </p>
                <div class="d-flex align-items-center justify-content-center">
                    <input type="hidden" name="reference_url" class="reference_url" />
                    <button class="btn btn-primary btn-sm redirectReferenceURL" type="button">
                        {{ __('Accept') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>