<template>
  <div
    class="modal fade"
    id="rename-folder-modal"
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
              {{ $t('links.change_name') }}
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
              <div class="col-sm-12 mb-3">
                <label class="form-label" for="document-name">
                  {{ $t('labels.document_name') }}
                  <sup class="text-danger ms-1">*</sup>
                </label>
                <input
                  type="hidden"
                  name="document_id"
                  v-model="formData.document_id"
                />
                <input
                  type="hidden"
                  name="document_type"
                  v-model="formData.document_type"
                />
                <input
                  v-model="formData.temp_name"
                  class="form-control"
                  :class="errors?.data != null ? 'is-invalid' : ''"
                  type="text"
                  id="document-name"
                  name="temp_name"
                  ref="nameRef"
                  required
                />
                <div
                  v-show="errors?.data"
                  v-for="(error, index) in errors?.data"
                  :key="index"
                  class="invalid-feedback"
                >
                  {{ error.message }}
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.change') }}
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
import { ref, onMounted } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import DocumentApiService from '../../../api/document_management/document-option';
import HttpResponse from '../../../enums/HttpResponse';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default {
  name: 'RenameFolderModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const documentApiService = new DocumentApiService();
    const errors = ref(null);
    const responseCode = HttpResponse;
    const modalRef = ref(null);
    const modalLoading = ref(false);

    // Initialize form data
    const formRef = ref({});
    const formData = ref({});

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      const documentId = formData.value.document_id;
      const requestBody = ref({
        document_name: formData.value.temp_name,
      });

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Handle responses
      documentApiService
        .updateDocumentName(documentId, requestBody.value)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t(
              'messages.document_management.document_folder_name_changed'
            )
          );

          emit('reset-folder-list');
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === responseCode.INVALID_PARAMETERS) {
            errors.value = responseData;
            return;
          }

          // Handle forbidden errors
          if (responseData.status_code === responseCode.FORBIDDEN) {
            resetModal();
          }

          emit('reset-folder-list');
          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        updateModel();
      });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
    });

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
    };
  },
};
</script>
