<template>
  <div
    class="modal fade"
    id="workflow-application-cancellation-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form
          action=""
          @submit="submitApplicationCancel"
          ref="formRef"
          novalidate
        >
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t('labels.workflow_cancel_application_title_modal') }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="closeModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-5 text-center">
              {{ $t('labels.workflow_cancel_application_message_modal') }}
            </div>
            <div class="text-center mb-5">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.workflow_cancel_application') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import WorkflowAPIService from '../../../api/workflows/workflow';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default {
  name: 'ApplicationCancellationModal',
  props: {
    workflow_id: {
      type: Number,
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const workflowApiService = new WorkflowAPIService();
    const modalRef = ref(null);
    const id = props.workflow_id;
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide  modal
     *
     * @returns {void}
     */
    const hide = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    /**
     * Event listener for submitting cancel application
     *
     * @returns {void}
     */
    const submitApplicationCancel = async (event) => {
      event.preventDefault();

      try {
        // Reinitialize state
        modalLoading.value = true;

        await workflowApiService.cancelApplication(id);

        // fire if success alert message
        emit(
          'set-alert',
          'success',
          i18n.global.t('messages.workflows.success_cancel_application')
        );

        hide();
      } catch (error) {
        // fire set alert functon in index if its has error
        emit('set-alert', 'failed');
      } finally {
        modalLoading.value = false;
        // redirect to list of workflows
        window.location.href = `/workflows`;
      }
    };

    return {
      modalRef,
      modal,
      show,
      hide,
      modalLoading,
      submitApplicationCancel,
    };
  },
};
</script>
