<div class="toast fade hide cstm-toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <h6 class="fs-sm mb-0 me-auto"></h6>
        <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body"></div>
</div>

@push('js')
    <script>
        const setToastNotification = function(title, message) {
            // Fetch toast element and instance
            var toastElement = document.querySelector('.cstm-toast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastElement);

            // Inject toast content
            toastElement.querySelector('h6').innerText = title;
            toastElement.querySelector('.toast-body').innerText = message;

            // Show toast
            toast.show();
        }
    </script>
@endpush
