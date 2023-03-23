<template>
  <div
    class="modal fade"
    id="shared-link-modal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t('messages.document_management.shared_link') }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-12 text-center">
                {{ $t('messages.document_management.manuallly_copy') }}
                <input
                  class="form-control"
                  type="text"
                  name="sharedLink"
                  autofocus
                  onfocus="this.select()"
                />
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-center">
            <button
              class="btn btn-secondary btn-shadow btn-sm"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            >
              {{ $t('buttons.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'SharedLinkModal',
  components: {
    SectionLoader,
  },
  setup() {
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#shared-link-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
    };

    return {
      resetModal,
      modalLoading,
    };
  },
};
</script>
