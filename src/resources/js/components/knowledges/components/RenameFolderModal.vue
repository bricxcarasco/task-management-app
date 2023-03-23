<template>
  <div
    class="modal fade"
    id="rename-folder-modal"
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
              {{ $t('headers.title_change') }}
            </h4>
            <input type="hidden" name="id" />
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
              <!-- Folder Name -->
              <div class="col-12">
                <input
                  class="form-control"
                  :class="errors?.task_title != null ? 'is-invalid' : ''"
                  name="task_title"
                  type="text"
                  v-model="formData.task_title"
                  required
                />
                <div
                  v-show="errors?.task_title"
                  v-for="(error, index) in errors?.task_title"
                  :key="index"
                  class="invalid-feedback"
                >
                  {{ error }}
                </div>
              </div>
            </div>
            <!-- Modal Actions -->
            <div class="text-center mt-4">
              <button class="btn btn-success btn-shadow btn-sm" type="submit">
                {{ $t('buttons.save') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import i18n from '../../../i18n';
import { objectifyForm } from '../../../utils/objectifyForm';

export default {
  name: 'RenameFolderModal',
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
    const apiService = new KnowledgeApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
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
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const submitForm = async () => {
      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Handle responses
      apiService
        .renameFolder(formData.value.id, formData.value)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.knowledges.rename_folder_successful')
          );
          emit('reset-list');

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = parseValidationErrors(responseData.data);
            return;
          }

          resetModal();
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
