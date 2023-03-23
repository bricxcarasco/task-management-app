<template>
  <span v-if="workflowStatusPending" class="label label-success">
    {{ $t('buttons.workflow_status_applying') }}
  </span>
  <span v-else-if="workflowStatusRemanded" class="label label-success">
    {{ $t('buttons.workflow_status_remanded') }}
  </span>

  <span v-else-if="workflowStatusApproved" class="label label-success">
    {{ $t('buttons.workflow_status_approved') }}
  </span>
  <span v-else-if="workflowStatusRejected" class="label label-success">
    {{ $t('buttons.workflow_status_rejected') }}
  </span>
</template>

<script>
import { defineComponent, computed } from 'vue';
import StatusTypes from '../../../enums/WorkflowsStatusTypes';
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
    const workflowStatusPending = computed(
      () =>
        props.workflow.status === StatusTypes.APPLYING &&
        props.user_type === ApproverTypes.OWNER
    );

    const workflowStatusRemanded = computed(
      () =>
        props.workflow.status === StatusTypes.REMANDED &&
        props.user_type === ApproverTypes.OWNER
    );

    const workflowStatusApproved = computed(
      () =>
        props.workflow.status === StatusTypes.APPROVED &&
        props.user_type === ApproverTypes.OWNER
    );

    const workflowStatusRejected = computed(
      () =>
        props.workflow.status === StatusTypes.REJECTED &&
        props.user_type === ApproverTypes.OWNER
    );

    return {
      workflowStatusPending,
      workflowStatusRemanded,
      workflowStatusApproved,
      workflowStatusRejected,
    };
  },
});
</script>
