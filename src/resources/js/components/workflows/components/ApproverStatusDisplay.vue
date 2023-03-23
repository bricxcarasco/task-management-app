<template>
  <span v-if="isApproverWorkflowStatusApproved" class="label label-success">
    {{ $t('buttons.workflow_completed') }}
  </span>
  <span
    v-else-if="isApproverWorkflowStatusRejected"
    class="label label-success"
  >
    {{ $t('buttons.workflow_rejected') }}
  </span>
  <span v-else-if="isApproverReactionPending" class="label label-success">
    {{ $t('buttons.workflow_waiting') }}
  </span>
  <span
    v-else-if="isApproverReactionApprovedReturned"
    class="label label-success"
  >
    {{ $t('buttons.workflow_done') }}
  </span>
</template>

<script>
import { defineComponent, computed } from 'vue';
import StatusTypes from '../../../enums/WorkflowsStatusTypes';
import ReactionTypes from '../../../enums/WorkflowsReactionTypes';
import ApproverTypes from '../../../enums/WorkflowUserApproverTypes';

export default defineComponent({
  name: 'ApproverStatusDisplay',
  props: {
    workflow: {
      type: [Array, Object],
      required: true,
    },
    user_type: {
      type: String,
    },
    rio: {
      type: [Array, Object],
    },
  },
  setup(props) {
    const isApprover =
      props.user_type === ApproverTypes.CURRENT_APPROVER ||
      props.user_type === ApproverTypes.APPROVER;

    const userWorkflowAction = props.workflow.actions.find(
      (action) => action.rio_id === props.rio.id
    );

    const isApproverReactionPending = computed(
      () => userWorkflowAction?.reaction === ReactionTypes.PENDING && isApprover
    );

    const isApproverReactionApprovedReturned = computed(
      () =>
        (userWorkflowAction?.reaction === ReactionTypes.APPROVED ||
          userWorkflowAction?.reaction === ReactionTypes.RETURNED) &&
        isApprover
    );

    const isApproverWorkflowStatusApproved = computed(
      () => props.workflow.status === StatusTypes.APPROVED && isApprover
    );

    const isApproverWorkflowStatusRejected = computed(
      () => props.workflow.status === StatusTypes.REJECTED && isApprover
    );

    return {
      isApproverReactionPending,
      isApproverReactionApprovedReturned,
      isApproverWorkflowStatusApproved,
      isApproverWorkflowStatusRejected,
    };
  },
});
</script>
