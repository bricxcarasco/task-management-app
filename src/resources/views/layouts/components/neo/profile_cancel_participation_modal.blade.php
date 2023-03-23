<div class="modal fade" id="cancel-participation" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <input id="neo-id" type="hidden" name="id" value="{{ $neo->id }}"/>
                <p class="text-center">
                    {{ __('Do you want to cancel the participation application?') }}
                </p>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-primary btn--dialogue" id="cancel-participation-btn" type="button" style="width: 200px;">
                    {{ __('To cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(function () {
        var id = $('#neo-id').val();
        $('#cancel-participation').modal({backdrop: 'static', keyboard: false})  

        $('#cancel-participation-btn').unbind().bind('click', function() {
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('neo.profile.update.cancel-participation', ['id' => $neoBelong->id ?? 0]) }}", {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken
                },
                method: 'DELETE',
                credentials: "same-origin",
            })
            .then(response => {
                if (response.ok) {
                    if (window.location.pathname == ('/neo/profile/groups/' + id)) {
                        window.location.href = window.location.protocol + '//' + window.location.host + '/neo/profile/participants/' + id;
                    } else {
                        window.location.reload();
                    }
                }
            })
            .catch(function(error) {
                console.log(error);
                loader.addClass('d-none');
            })
        })
    });
</script>
@endpush