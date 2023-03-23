<template>
  <div
    class="modal fade"
    id="upload-file-modal"
    tabindex="-1"
    aria-hidden="true"
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
              {{ $t('headers.file_upload') }}
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
              <!-- File Uploader -->
              <div class="col-12">
                <input
                  type="file"
                  class="js-document-management m-0"
                  name="upload_file[]"
                  data-upload="/api/document/file/process"
                  data-chunk="/api/document/file"
                  data-revert="/api/document/file"
                />
                <div
                  v-show="errors?.upload_file"
                  v-for="(error, index) in errors?.upload_file"
                  :key="index"
                  class="invalid-feedback"
                >
                  {{ error }}
                </div>
              </div>
            </div>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer">
            <button
              class="btn btn-success btn-shadow btn-sm"
              type="submit"
              :disabled="!isSubmitActive"
            >
              {{ $t('buttons.register') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import BpheroConfig from '../../../config/bphero';
import CreateActionApiService from '../../../api/document_management/create-action';
import i18n from '../../../i18n';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import StorageTypes from '../../../enums/StorageTypes';
import { objectifyForm } from '../../../utils/objectifyForm';

export default {
  name: 'UploadFileModal',
  props: {
    directory_id: {
      type: Number,
      default: null,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const apiService = new CreateActionApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const fileUploader = ref({});
    const isSubmitActive = ref(false);
    const errorMessage = ref(
      i18n.global.t('messages.document_management.file_upload_failed')
    );

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
      fileUploader.value.clearFiles();
      isSubmitActive.value = false;
    };

    /**
     * Parse validation errors
     *
     * @returns {void}
     */
    const parseValidationErrors = (data) => {
      const validationErrors = [];
      data.forEach((item) => {
        validationErrors[item.field_name] = [item.message];
      });

      return validationErrors;
    };

    /**
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const submitForm = async () => {
      // Reinitialize state
      errors.value = null;
      emit('reset-alert');

      // Prepare initial data
      formData.value.storage_type_id = StorageTypes.HERO;
      if (props.directory_id !== null) {
        formData.value.directory_id = props.directory_id;
      }

      try {
        // Start uploading all files
        await fileUploader.value.uploadFiles();

        // Prepare for final upload process
        modalLoading.value = true;
        const formObject = objectifyForm(formRef.value);
        formData.value.code = formObject.upload_file;

        // Handle responses
        apiService
          .saveFiles(formData.value)
          .then(() => {
            emit(
              'set-alert',
              'success',
              i18n.global.t(
                'messages.document_management.file_upload_successful'
              )
            );
            emit('reset-list');
            resetModal();
          })
          .catch((error) => {
            const responseData = error.response.data;

            // Check validation error message
            if (responseData.status_code === 422) {
              errors.value = parseValidationErrors(responseData.data);
              if (
                errors.value?.code[0] ===
                i18n.global.t(
                  'messages.document_management.insufficient_free_space'
                )
              ) {
                errorMessage.value = errors.value?.code[0];
              }
            }

            resetModal();
            emit('reset-list');
            emit('set-alert', 'failed', errorMessage.value);
          })
          .finally(() => {
            modalLoading.value = false;
          });
      } catch (error) {
        modalLoading.value = false;
        throw error;
      }
    };

    onMounted(() => {
      /**
       * Initialize discount application registration file uploader
       */
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-document-management',
        maxFileSize: BpheroConfig.DOCUMENT_MANAGEMENT_MAX_FILE_SIZE,
        allowMultiple: true,
        chunkUploads: true,
        instantUpload: false,
        allowReorder: true,
        allowProcess: false,
      });

      fileUploader.value.pond.on('updatefiles', (files) => {
        isSubmitActive.value = files.length > 0;
      });
    });

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
      isSubmitActive,
    };
  },
};
</script>
