@php
/**
 * Reusable alert box component
 */
$hasSession = session()->has('alert');
$alertStatus = session('alert.status');
$alertMessage = session('alert.message');
$sessionClass = $hasSession ? "alert-{$alertStatus} show" : '';
@endphp

<div class="alert-fixed">
    <div class="alert {{ $sessionClass }} alert-dismissible col-md-12 fade js-flash-alert" role="alert">
        <span class="common-alert-message" hidden>{{ session('alert.message') }}</span>
        <button type="button" class="btn-close common-alert-btn" aria-label="Close" hidden></button>
    </div>
</div>

{{ session()->forget(['alert.status', 'alert.message']) }}

@push('js')
    <script>
        let dismissAlertTimeout;

        /**
         * Close Alert
         *
         * @returns {void}
         */
            const closeAlert = function() {
            const alert = document.querySelector('.js-flash-alert');

            if (alert) {
                let spanMessage = alert.querySelector('span');
                alert.classList.remove('alert-danger', 'alert-success', 'show');

                if (spanMessage) {
                    spanMessage.textContent = '';
                }
            }
        }

        /**
         * Dismiss Alert
         *
         * @returns {void}
         */
        const dismissAlert = function() {
            dismissAlertTimeout = setTimeout(() => {
                closeAlert();
            }, 3000);
        }

        /**
         * Set Alert
         *
         * @params {string} status
         * @params {string} message
         * @returns {void}
         */
            const setAlert = function(status, message) {
            const alert = document.querySelector('.js-flash-alert');

            closeAlert();
            clearTimeout(dismissAlertTimeout);

            if (alert) {
                let spanMessage = alert.querySelector('span');
                alert.classList.add('alert-' + status, 'show');

                if (spanMessage) {
                    spanMessage.textContent = message;
                }

                dismissAlert();
            }
        }

        /**
         * Event listener for on page load to dismiss alert
         */
        document.addEventListener("bphero.onload", function(event) {
            const closeBtn = document.querySelector('.common-alert-btn');
            const alertMsg = document.querySelector('.common-alert-message');
            closeBtn.removeAttribute('hidden');
            alertMsg.removeAttribute('hidden');
            const alert = document.querySelector('.js-flash-alert.show');

            if (alert) {
                dismissAlert();
            }
        });

        /**
         * Event listener for closing alert to clear auto-dismiss
         */
        document.addEventListener("DOMContentLoaded", function(event) {
            document.querySelector('.js-flash-alert .btn-close').addEventListener('click', function(event) {
                closeAlert();
                clearTimeout(dismissAlertTimeout);
            });
        });
    </script>
@endpush
