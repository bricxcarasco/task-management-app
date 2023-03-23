<template>
  <div>
    <p class="text-center mb-0">
      {{ $t('paragraphs.please_enter_your_application_details') }}
    </p>

    <form action="" @submit="handleFormValidate" ref="formRef" novalidate>
      <div class="mb-3">
        <label for="text-input" class="form-label">
          ■{{ $t('labels.title') }}
          <span class="text-danger">*</span>
        </label>
        <input
          v-model="formData.workflow_title"
          class="form-control"
          :class="errors?.workflow_title != null ? 'is-invalid' : ''"
          type="text"
          id="text-input"
          name="workflow_title"
        />
        <base-validation-error :errors="errors?.workflow_title" />
      </div>
      <div class="mb-3">
        <label for="text-input" class="form-label">
          ■{{ $t('labels.explanation') }}
          <span class="text-danger">*</span>
        </label>
        <textarea
          v-model="formData.caption"
          class="form-control"
          :class="errors?.caption != null ? 'is-invalid' : ''"
          type="text"
          id="text-input"
          name="caption"
        ></textarea>
        <base-validation-error :errors="errors?.caption" />
      </div>
      <div class="mb-3">
        <label for="select-input" class="form-label">
          ■{{ $t('labels.priority') }}
        </label>
        <br />
        <select
          v-model="formData.priority"
          class="form-select w-25 d-inline-block"
          :class="errors?.priority != null ? 'is-invalid' : ''"
          id="select-input"
          name="priority"
        >
          <option
            v-for="priority in WorkflowPriorities"
            :key="priority.value"
            :value="priority.value"
          >
            {{ priority.text }}
          </option>
        </select>
        <base-validation-error :errors="errors?.priority" />
      </div>
      <div class="mb-3 d-flex align-items-center justify-content-between">
        <p class="form-label">
          ■{{ $t('labels.attachment') }}
          <span class="text-danger">*</span>
        </p>
        <button
          type="button"
          class="btn btn-link"
          @click="() => openAttachment()"
        >
          <i class="ai-plus me-2"></i>
          {{ $t('buttons.addition') }}
        </button>
      </div>

      <!-- Display local upload files -->
      <div class="file-uploader mt-2" v-show="hasUploadFile">
        <input
          type="file"
          class="js-workflow-file-uploader"
          name="upload_file[]"
          data-upload="/api/workflows/file/process"
          data-chunk="/api/workflows/file"
          data-revert="/api/workflows/file"
        />
      </div>

      <div class="mb-3 px-2">
        <span :class="errors?.upload_file != null ? 'is-invalid' : ''"></span>
        <base-validation-error :errors="errors?.upload_file" />
      </div>

      <ul class="mb-2 px-2">
        <li
          class="d-flex align-items-center justify-content-between"
          v-for="attachment in form_data.attaches"
          :key="attachment"
        >
          <a class="text-break me-5" href="#">{{ attachment.link }}</a>
          <button
            @click="removeAttacment(attachment.document_id)"
            type="button"
            class="p-0 btn btn-link fs-sm"
          >
            {{ $t('buttons.delete') }}
          </button>
        </li>
      </ul>

      <div class="mb-3 px-2">
        <span :class="errors?.attaches != null ? 'is-invalid' : ''"></span>
        <base-validation-error :errors="errors?.attaches" />
      </div>

      <div class="mb-3 px-2">
        <span :class="errors?.attachments != null ? 'is-invalid' : ''"></span>
        <base-validation-error :errors="errors?.attachments" />
      </div>

      <div class="mb-3 px-2">
        <span
          :class="errors?.attachment_count != null ? 'is-invalid' : ''"
        ></span>
        <base-validation-error :errors="errors?.attachment_count" />
      </div>

      <div class="text-center mt-4">
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="!fileUploaded()"
        >
          {{ $t('buttons.next') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { defineComponent, ref, watchEffect } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import WorkflowApiService from '../../../api/workflows/workflow';
import CreateWorkflowSteps from '../../../enums/CreateWorkflowSteps';
import WorkflowPriorities from '../../../enums/WorkflowPriorities';
import BaseValidationError from '../../base/BaseValidationError.vue';

export default defineComponent({
  name: 'WorkflowForm',
  components: {
    BaseValidationError,
  },
  props: {
    form_data: {
      type: [Array, Object],
    },
  },
  setup(props, { emit }) {
    const workflowApiService = new WorkflowApiService();
    const formData = ref(props.form_data);
    const formRef = ref({});
    const errors = ref(null);
    const hasUploadFile = ref(false);
    const hasFileUploaded = ref(false);

    const resetErrorsValue = () => {
      errors.value = null;
    };

    /**
     * Update vue model with existing form data
     *
     * @param {int} id
     * @returns {void}
     */
    const clearFormData = () => {
      errors.value = null;
      formData.value = {
        workflow_title: '',
        caption: '',
        priority: WorkflowPriorities.NONE.value,
        attaches: null,
        attachments: [],
        upload_file: [],
        approvers: [],
      };
    };

    const removeAttacment = (id) => {
      emit('remove-file', id);
    };

    /**
     * Show files.
     */
    const showFilesUploaded = (state) => {
      hasUploadFile.value = state;
    };

    /**
     * Enable button if file uploaded.
     */
    const buttonEnable = (state) => {
      hasFileUploaded.value = state;
    };

    const fileUploaded = () => hasFileUploaded.value;

    /**
     * Go to approval component
     *
     * @returns {void}
     */
    const handleFormValidate = async (event) => {
      event.preventDefault();

      // Reinitialize state
      emit('set-loading', true);
      errors.value = null;

      const data = formData.value;

      // Handle file upload
      if (hasUploadFile.value) {
        // Inject server codes to input data
        const formObject = objectifyForm(formRef.value);
        data.upload_file = formObject.upload_file;
      } else {
        data.upload_file = [];
      }

      // Handle responses
      workflowApiService
        .validateWorkflow(data)
        .then(() => emit('next-step', data, CreateWorkflowSteps.APPROVAL))
        .catch((error) => {
          const responseData = error.response?.data;

          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
          }
        })
        .finally(() => {
          emit('set-loading', false);
        });
    };

    const openAttachment = () => {
      emit('open-attachment');
    };

    /**
     * Apply tracking changes
     */
    watchEffect(() => {
      formData.value = props.form_data;
    });

    return {
      WorkflowPriorities,
      formData,
      formRef,
      errors,
      resetErrorsValue,
      handleFormValidate,
      hasUploadFile,
      fileUploaded,
      buttonEnable,
      showFilesUploaded,
      removeAttacment,
      openAttachment,
      clearFormData,
    };
  },
});
</script>
