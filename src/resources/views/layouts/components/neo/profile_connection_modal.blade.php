<div class="modal fade" id="apply-connection" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        @csrf
        <div class="modal-body">
            <p class="text-center">{{ $neo->organization_name }}<br/>
            {{ __('Do you want to apply for participation?') }}
            </p>
            <div class="d-flex align-items-center justify-content-center">
                <button class="btn btn-primary btn--dialogue" id="apply-connection" type="button" style="width: 200px;">
               {{ __('Apply for connection') }}
                </button>
            </div>
        </div>
        </div>
    </div>
</div>