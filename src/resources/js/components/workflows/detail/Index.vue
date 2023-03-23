<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <action-comment-modal ref="actionCommentModalRef" />
    <approver-action-modal
      ref="approverActionModalRef"
      :reaction_selections="reaction_selections"
      :rio="rio"
      :workflow_id="workflow.id"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <application-cancellation-modal
      ref="applicationCancellationModalRef"
      :workflow_id="workflow.id"
      @set-alert="setAlert"
    />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-10 pb-md-10 rounded-3">
            <div class="position-relative">
              <br />
              <div class="mb-0 mb-md-2 position-relative border-bottom pb-2">
                <a href="/workflows" class="btn btn-secondary btn--back">
                  <i class="ai-arrow-left"></i>
                </a>
                <div
                  v-show="workflow.status !== StatusTypes.CANCELLED"
                  class="text-end"
                >
                  <button
                    v-if="user_type === ApproverTypes.CURRENT_APPROVER"
                    type="button"
                    class="btn btn-secondary me-2"
                    @click="() => approverActionModalRef.show()"
                  >
                    {{ $t('buttons.workflow_button_handle') }}
                  </button>

                  <!-- <button
                    v-if="isReapplyable"
                    type="button"
                    class="btn btn-secondary"
                  >
                    {{ $t('buttons.workflow_reapply') }}
                  </button> -->

                  <button
                    v-if="isCancellable"
                    type="button"
                    class="btn btn-secondary"
                    @click="() => applicationCancellationModalRef.show()"
                  >
                    {{ $t('buttons.workflow_cancel_application') }}
                  </button>

                  <approver-status-display
                    :rio="rio"
                    :workflow="workflow"
                    :user_type="user_type"
                  />
                  <applicant-status-display
                    :rio="rio"
                    :workflow="workflow"
                    :user_type="user_type"
                  />
                </div>
              </div>
            </div>
          </div>
          <br />
          <div
            v-show="workflow.status === StatusTypes.CANCELLED"
            class="bg-danger px-2 py-3 text-white mb-2"
          >
            {{ $t('labels.workflow_application_cancelled') }}
          </div>
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.workflow_title') }}
                </label>
                <p class="mb-0">{{ workflow.workflow_title }}</p>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.workflow_applicant') }}
                </label>
                <p class="mb-0">{{ workflow.name }}</p>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.date_of_application') }}
                </label>
                <p class="mb-0">
                  {{ moment(workflow.created_at).format('YYYY-MM-DD h:mm') }}
                </p>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.workflow_description') }}
                </label>
                <p class="mb-0">
                  {{ workflow.caption }}
                </p>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.workflow_priority') }}
                </label>
                <p class="mb-0">{{ workflow.priority }}</p>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.workflow_attachment') }}
                </label>
                <ul class="ps-0" style="list-style: none">
                  <li v-for="(attachment, index) in attachments" :key="index">
                    <a
                      v-if="attachment.fileId"
                      class="d-flex align-items-center text-break"
                      :class="{ 'text-muted': !attachment.isExist }"
                      href="#"
                      @click.stop="accessFile(attachment)"
                    >
                      <span>{{ attachment.fileName }} </span>
                      <i
                        v-show="!attachment.isExist"
                        style="font-size: large"
                        class="ai-alert-circle text-danger"
                        data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        :title="$t('messages.workflows.tooltip_error')"
                      ></i>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="mb-3">
                <label class="fs-sm fw-bold">
                  ■ {{ $t('labels.designated_approvers') }}
                </label>
                <approver-list
                  @open-modal="handleShowComment"
                  :approvers="workflow.actions"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, computed, ref } from 'vue';
import moment from 'moment';
import { Tooltip } from 'bootstrap';
import ApproverList from './ApproverList.vue';
import ActionCommentModal from '../components/ActionCommentModal.vue';
import ApproverActionModal from '../components/ApproverActionModal.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import StatusTypes from '../../../enums/WorkflowsStatusTypes';
import ApplicationCancellationModal from '../components/ApplicationCancellationModal.vue';
import ReactionTypes from '../../../enums/WorkflowsReactionTypes';
import ApproverTypes from '../../../enums/WorkflowUserApproverTypes';
import ApplicantStatusDisplay from '../components/ApplicantStatusDisplay.vue';
import ApproverStatusDisplay from '../components/ApproverStatusDisplay.vue';

export default defineComponent({
  name: 'WorkFlowDetailIndex',
  props: {
    workflow: {
      type: [Array, Object],
      required: true,
    },
    attachments: {
      type: [Array, Object],
    },
    reaction_selections: {
      type: [Array, Object],
    },
    user_type: {
      type: String,
    },
    rio: {
      type: [Array, Object],
    },
  },
  components: {
    ApproverList,
    ActionCommentModal,
    ApproverActionModal,
    BaseAlert,
    ApplicationCancellationModal,
    ApplicantStatusDisplay,
    ApproverStatusDisplay,
  },
  setup(props) {
    let formModalNode = null;
    let formModal = null;
    const actionCommentModalRef = ref(null);
    const approverActionModalRef = ref(null);
    const applicationCancellationModalRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });

    /**
     * Access a file
     *
     * @param {object} request
     * @returns {void}
     */
    const accessFile = (attachment) => {
      if (attachment.isExist) {
        window.open(`${attachment.link}/${attachment.fileName}`, '_blank');
      }
    };

    /**
     * Set alert messages
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status = 'success', message = null) => {
      const statusValue = status === 'success' ? 'success' : 'failed';
      alert.value[statusValue] = true;
      alert.value.message = message;
    };

    /**
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * show action comment modal
     *
     * @param {object} workflow_action
     * @returns {void}
     */
    const handleShowComment = (approver) => {
      actionCommentModalRef.value.updateCommentModal(approver.comment);
      formModal.value.show();
    };

    const isCancellable = computed(
      () =>
        props.user_type === 'owner' &&
        (props.workflow.status === StatusTypes.APPLYING ||
          props.workflow.status === StatusTypes.REMANDED)
    );

    const isReturnedByApprover = props.workflow.actions.some(
      (action) => action.reaction === ReactionTypes.RETURNED
    );

    const isReapplyable = computed(
      () => props.user_type === 'owner' && isReturnedByApprover
    );

    /**
     * On mounted properties
     */
    onMounted(() => {
      /* eslint no-undef: 0 */
      formModalNode = document.getElementById('workflow-action-comment-modal');
      formModal = computed(() => new bootstrap.Modal(formModalNode));
      // Bootstrap Tooltip
      /* eslint-disable no-new */
      new Tooltip(document.body, {
        selector: "[data-bs-toggle='tooltip']",
      });
    });

    return {
      handleShowComment,
      actionCommentModalRef,
      approverActionModalRef,
      alert,
      resetAlert,
      setAlert,
      StatusTypes,
      applicationCancellationModalRef,
      ReactionTypes,
      ApproverTypes,
      isCancellable,
      moment,
      accessFile,
      isReapplyable,
    };
  },
});
</script>
