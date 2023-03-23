<template>
  <div
    class="modal fade"
    id="approver-action-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="handleFormSubmission" ref="formRef" novalidate>
          <input type="hidden" name="id" v-model="formData.id" />

          <div class="modal-header">
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="hide"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">
                ■ {{ $t('labels.workflow_reaction_selection') }}
                <span class="text-danger">*</span>
              </label>
              <select
                v-model="formData.reaction"
                name="reaction"
                class="form-select"
                :class="errors?.reaction != null ? 'is-invalid' : ''"
              >
                <option value="">-</option>
                <option
                  v-for="(reaction, index) in reaction_selections"
                  :key="index"
                  :value="index"
                >
                  {{ reaction }}
                </option>
              </select>
              <base-validation-error :errors="errors?.reaction" />
            </div>
            <div class="mb-3">
              <label class="form-label">
                ■ {{ $t('labels.workflow_comment_lbl') }}
                <span
                  v-if="formData.reaction > ReactionTypes.APPROVED"
                  class="text-danger"
                  >*</span
                >
              </label>
              <textarea
                v-model="formData.comment"
                class="form-control"
                id="textarea-input"
                rows="5"
                name="comment"
                :class="errors?.comment != null ? 'is-invalid' : ''"
              ></textarea>
              <base-validation-error :errors="errors?.comment" />
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.complete') }}
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
import BaseValidationError from '../../base/BaseValidationError.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ReactionTypes from '../../../enums/WorkflowsReactionTypes';
import i18n from '../../../i18n';

export default {
  name: 'ApproverActionModal',
  props: {
    reaction_selections: {
      type: [Array, Object],
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    workflow_id: {
      type: Number,
      required: true,
    },
  },
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const workflowApiService = new WorkflowAPIService();
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const errors = ref(null);
    const modalLoading = ref(false);
    const rio = ref(props.rio);
    const id = props.workflow_id;

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
      formData.value = {};
      errors.value = null;
    };

    /**
     * Event listener for add url form submit
     *
     * @returns {void}
     */
    const handleFormSubmission = async (event) => {
      event.preventDefault();
      // Reinitialize state
      errors.value = null;
      modalLoading.value = true;
      emit('reset-alert');

      try {
        // spread the formdata and rio id to a new data
        const data = { rio_id: rio.value.id, ...formData.value };

        await workflowApiService.updateReaction(id, data);

        emit(
          'set-alert',
          'success',
          i18n.global.t('messages.workflows.success_responded')
        );

        hide();
        window.location.reload();
      } catch (error) {
        // Inject validation errors
        if (error.response.status === 422) {
          errors.value = error.response.data.errors;
          return;
        }
        emit('set-alert', 'failed');
      } finally {
        modalLoading.value = false;
      }
    };

    return {
      modalRef,
      modal,
      show,
      hide,
      formRef,
      formData,
      handleFormSubmission,
      ReactionTypes,
      errors,
      modalLoading,
    };
  },
};
</script>
