<template>
  <div
    class="modal fade"
    id="delete-folder-modal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit.prevent="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t('buttons.delete') }}
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
                {{ $t('messages.rio.delete_confirmation') }}
                <input type="hidden" name="id" />
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.delete') }}
            </button>
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
import { objectifyForm } from '../../../utils/objectifyForm';
import DocumentApiService from '../../../api/document_management/document-option';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default {
  name: 'DeleteFolderModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const documentApiService = new DocumentApiService();
    const modalLoading = ref(false);

    // Initialize form data
    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#delete-folder-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
    };

    /**
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      // Handle responses
      documentApiService
        .deleteDocument(formData.id)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.document_management.document_deleted')
          );

          emit('reset-folder-list');
          resetModal();
        })
        .catch(() => {
          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitForm,
      resetModal,
      modalLoading,
    };
  },
};
</script>
