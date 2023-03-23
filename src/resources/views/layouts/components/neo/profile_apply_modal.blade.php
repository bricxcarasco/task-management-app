<div class="modal fade" id="apply" tabindex="-1" style="display: none;" aria-hidden="true">
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
                <p class="text-center">{{ $neo->organization_name }}<br/>
                {{ __('Do you want to apply for participation?') }}
                </p>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-primary btn--dialogue" id="apply-participation" type="button" style="width: 200px;">
                        {{ __('Apply for participation') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(function () {
        $('#apply').modal({backdrop: 'static', keyboard: false})  

        $('#apply-participation').unbind().bind('click', function() {
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('neo.profile.update.create-participation', ['id' => $neo->id]) }}", {
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
                    $('#apply').modal('hide');
                    $('.modal-backdrop:first-of-type').remove();
                    $('#dialogue').modal('show');
                    $("#introduction-buttons").load(" #introduction-buttons > *");
                    loader.addClass('d-none');
                    window.location.reload();
                } else {
                    window.location.reload();
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