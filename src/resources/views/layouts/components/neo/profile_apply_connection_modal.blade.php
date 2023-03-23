<div class="modal fade" id="apply-connection" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" id="close-message-btn" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <p class="text-center">{{ $neo->organization_name }}<br/>
                    {{ __('Do you want to apply for connection?') }}
                </p>
                <div class="mb-3">
                    <label for="textarea-input" class="form-label">
                        {{ __('Messages') }}
                    </label>
                    <textarea class="form-control"
                        id="message"
                        rows="5"
                        name="message"
                    ></textarea>
                    <div class="invalid-feedback">
                        <p id="invalid_message"></p>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-primary btn--dialogue" id="apply-connection-btn" type="button" style="width: 200px;">
                        {{ __('Apply for connection') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(function () {
        $('#apply-connection').modal({backdrop: 'static', keyboard: false})  

        // Process connection application
        $("#apply-connection-btn").unbind().bind('click', function() {
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let message = document.querySelector('#message').value;
            let url = "{{ route('neo.profile.connection.create-connection', ['neo' => $neo->id]) }}";

            // Change url when message is not empty/null
            if(message){
                url = "{{ route('neo.profile.connection.create-connection', ['neo' => $neo->id, 'message' => 'messageRequest']) }}";
                url = url.replace('messageRequest', message)
            }

            // Clear validation class trigger
            $('[name="message"]').removeClass("is-invalid");

            // Check message request
            if (message.length > 255) {
                $('[name="message"]').addClass("is-invalid");
                $("p#invalid_message").text("{{ __('Your message must be 255 characters or less') }}");
                loader.addClass('d-none');
            } else {
                fetch(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    method: 'POST',
                    credentials: "same-origin",
                })
                .then(response => {
                    if (response.ok) {
                        $('#apply-connection').modal('hide')
                        $('.modal-backdrop:first-of-type').remove();
                        $('#connection-dialogue').modal('show')
                        $("#introduction-buttons").load(" #introduction-buttons > *");
                        $("#message").val('');
                        $('[name="message"]').removeClass("is-invalid");
                        
                        loader.addClass('d-none');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    loader.addClass('d-none');
                })
            }
        })

        // Reset modal when closed
        $("#close-message-btn").unbind().bind('click', function() {
            $("#message").val('');
            $('[name="message"]').removeClass("is-invalid");
        })
    });
</script>
@endpush