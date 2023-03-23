<template>
  <div
    class="modal fade"
    id="issued-payment-url"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.issue_payment_screen') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="handleCloseModal"
          ></button>
        </div>
        <div class="modal-body">
          <p class="text-center">
            {{ $t('messages.classifieds.issue_payment_screen_url') }}
          </p>
          <div class="border p-2">
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ $t('messages.classifieds.payment_page_url') }}</span>
              <button
                type="button"
                class="btn btn-link p-0"
                @click="handleCopyLink"
              >
                {{ $t('buttons.copy') }}
              </button>
            </div>
            <a class="payment__link js-payment-url" href="#"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'IssuedPaymentUrlModalComponents',
  setup(props, { emit }) {
    /**
     * Event listener for copying payment URL
     *
     * @returns {void}
     */
    const handleCopyLink = () => {
      const modal = document.querySelector('#issued-payment-url');
      const url = modal.querySelector('.js-payment-url').innerHTML;

      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.classifieds.copied_payment_link')
          );
        });
      } else {
        const textArea = document.createElement('textarea');
        textArea.innerHTML = url;
        modal.appendChild(textArea);
        textArea.select();

        try {
          document.execCommand('copy');
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.classifieds.copied_payment_link')
          );
        } catch {
          emit(
            'set-alert',
            'failed',
            i18n.global.t('messages.classifieds.failed_to_copy')
          );
        } finally {
          modal.removeChild(textArea);
        }
      }
    };

    /**
     * Event listener when modal is closed
     *
     * @returns {void}
     */
    const handleCloseModal = () => {
      emit('update-messages');
    };

    return {
      handleCopyLink,
      handleCloseModal,
    };
  },
});
</script>
