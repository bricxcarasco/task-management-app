<template>
  <div
    class="modal fade"
    id="createWorkflowModalRef"
    aria-hidden="true"
    data-bs-backdrop="static"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('headers.workflow_creation') }}
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
          <!-- Create Workfow Form -->
          <workflow-form
            v-show="workflowStep === CreateWorkflowSteps.FORM"
            :form_data="formData"
            @set-loading="setLoading"
            @next-step="handleApproval"
            @open-attachment="$emit('open-attachment')"
            @form-handler="formDataHandler"
            @remove-file="removeFile"
            ref="workflowFormRef"
          />

          <!-- Create Workfow Approval -->
          <workflow-approval
            v-show="workflowStep === CreateWorkflowSteps.APPROVAL"
            :form_data="formData"
            :service="service"
            :is_previous="isPreviousWorkflowApproval"
            @set-loading="setLoading"
            @previous-step="handlePreviousStep"
            @next-step="handleConfirmation"
            ref="workflowApprovalRef"
          />

          <!-- Create Workfow Confirmation -->
          <workflow-confirmation
            v-if="workflowStep === CreateWorkflowSteps.CONFIRMATION"
            :form_data="formData"
            @set-loading="setLoading"
            @previous-step="handlePreviousStep"
            @save-workflow="handleSaveWorkflow"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import CreateWorkflowSteps from '../../../enums/CreateWorkflowSteps';
import WorkflowPriorities from '../../../enums/WorkflowPriorities';
import WorkflowApiService from '../../../api/workflows/workflow';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import WorkflowForm from '../components/WorkflowForm.vue';
import WorkflowApproval from '../components/WorkflowApproval.vue';
import WorkflowConfirmation from '../components/WorkflowConfirmation.vue';
import i18n from '../../../i18n';

export default {
  name: 'CreateWorkflowModal',
  components: {
    SectionLoader,
    WorkflowForm,
    WorkflowApproval,
    WorkflowConfirmation,
  },
  props: {
    file_names: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    file_attaches: {
      type: [Array, Object],
    },
  },
  emits: [
    'open-attachment',
    'save-success',
    'clear-attaches',
    'remove-uploaded-files',
    'update-list',
    'set-alert',
    'remove-selected-file',
  ],
  setup(props, { emit }) {
    const workflowApiService = new WorkflowApiService();
    const modalRef = ref(null);
    const modalLoading = ref(false);

    const workflowStep = ref(CreateWorkflowSteps.FORM);
    const formData = ref({
      priority: WorkflowPriorities.NONE.value,
      attaches: null,
      attachments: [],
      approvers: [
        {
          sequence: Math.floor(Math.random() * 10000),
          name: '',
          id: '',
        },
      ],
    });
    const workflowFormRef = ref(null);
    const workflowApprovalRef = ref(null);
    const isPreviousWorkflowApproval = ref(false);
    const errors = ref(null);
    const errorMessage = ref(null);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Set default form data values
     *
     * @returns {void}
     */
    const setDefaultFormData = () => {
      formData.value = {
        workflow_title: '',
        caption: '',
        priority: WorkflowPriorities.NONE.value,
        attaches: null,
        attachments: [],
        approvers: [
          {
            sequence: Math.floor(Math.random() * 10000),
            name: '',
            id: '',
          },
        ],
      };
      isPreviousWorkflowApproval.value = false;
    };

    /**
     * Show select connected RIO/NEO modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * Go to approval component
     *
     * @returns {void}
     */
    const handlePreviousStep = (step, isBack = false) => {
      isPreviousWorkflowApproval.value = isBack;
      workflowStep.value = step;
    };

    /**
     * Go to approval component
     *
     * @returns {void}
     */
    const handleApproval = (data) => {
      workflowStep.value = CreateWorkflowSteps.APPROVAL;
      formData.value = data;
    };

    /**
     * Go to confirmation component
     *
     * @returns {void}
     */
    const handleConfirmation = (data) => {
      workflowStep.value = CreateWorkflowSteps.CONFIRMATION;
      formData.value = data;
    };

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      emit('remove-uploaded-files');
      emit('clear-attaches');
      setDefaultFormData();
      workflowFormRef.value.resetErrorsValue();
      setLoading(false);
    };

    /**
     * Save workflow
     *
     * @returns {void}
     */
    const handleSaveWorkflow = () => {
      // Reinitialize state
      setLoading(true);

      // setting the data
      const data = formData.value;

      // Handle responses
      workflowApiService
        .saveWorkflow(data)
        .then((response) => {
          // Clear upload files
          workflowFormRef.value.showFilesUploaded(false);

          if (response.data.status_code === 200) {
            emit('update-list');
            emit('save-success', response.data.message);
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Check validation error message
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            if (
              errors.value?.upload_file[0] ===
              i18n.global.t(
                'messages.document_management.insufficient_free_space'
              )
            ) {
              errorMessage.value = errors.value?.upload_file[0];
            }
          }

          emit('set-alert', 'failed', errorMessage.value);
        })
        .finally(() => resetModal());
    };

    /**
     * form handler to not mutate the form data when passing as props
     */
    const formDataHandler = (e) => {
      const { name } = e.target;
      const { value } = e.target;

      formData.value[name] = value;
    };

    /**
     * form handler to not mutate the form data when passing as props
     *
     * @param {int} id
     */
    const removeFile = (id) => {
      emit('remove-selected-file', id);
    };

    /**
     * Show files uploaded.
     *
     * @returns {void}
     */
    const showFiles = (state) => {
      workflowFormRef.value.showFilesUploaded(state);
    };

    /**
     * Enable button after file uploaded.
     *
     * @returns {void}
     */
    const enableButton = (state) => {
      workflowFormRef.value.buttonEnable(state);
    };

    watch(
      () => props.file_names,
      () => {
        formData.value.attachments = props.file_names;
      },
      { deep: true }
    );

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        emit('remove-uploaded-files');
        setDefaultFormData();
        workflowFormRef.value.clearFormData();
        workflowApprovalRef.value.clearFormData();
        setLoading(false);
        workflowStep.value = CreateWorkflowSteps.FORM;
      });
      modalRef.value.addEventListener('shown.bs.modal', async () => {
        // eslint-disable-next-line
        if ($('.approver-select-ui').data('select2')) {
          // eslint-disable-next-line
          $('.approver-select-ui').select2('destroy');
        }

        await workflowApprovalRef.value.getApproverList();
        workflowApprovalRef.value.loadSelect2();
      });
    });

    watch(
      () => props.file_attaches,
      () => {
        formData.value.attaches = props.file_attaches;
      },
      { deep: true }
    );

    return {
      CreateWorkflowSteps,
      modalRef,
      workflowFormRef,
      workflowApprovalRef,
      modal,
      modalLoading,
      show,
      setLoading,
      formData,
      workflowStep,
      isPreviousWorkflowApproval,
      handlePreviousStep,
      handleApproval,
      handleConfirmation,
      handleSaveWorkflow,
      enableButton,
      showFiles,
      resetModal,
      formDataHandler,
      removeFile,
      setDefaultFormData,
    };
  },
};
</script>

<style scoped>
.section-loader {
  z-index: 2999 !important;
}
</style>
