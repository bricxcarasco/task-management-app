<template>
  <div
    class="modal fade"
    id="create-folder-modal"
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
              {{ $t('headers.create_knowledge_folder') }}
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
              <!-- Folder Name -->
              <div class="col-12">
                <label for="text-input" class="form-label">{{
                  $t('labels.enter_folder_name')
                }}</label>
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
                {{ $t('headers.create_knowledge_folder') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import i18n from '../../../i18n';

export default {
  name: 'CreateFolderModal',
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
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const submitForm = async () => {
      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Prepare initial data
      if (props.directory_id !== null) {
        formData.value.directory_id = props.directory_id;
      }

      // Handle responses
      apiService
        .createFolder(formData.value)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.knowledges.create_folder_successful')
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
