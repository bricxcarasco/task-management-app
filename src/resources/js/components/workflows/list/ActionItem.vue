<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <a :href="'/workflows/' + list.id" style="color: inherit">
      <div class="d-flex align-items-center justify-content-between">
        <span class="fs-ms" style="vertical-align: inherit">
          {{ $t('labels.application_date') }}：{{ list.created_at }}
        </span>
        <span class="fs-ms" style="vertical-align: inherit">
          {{ $t('labels.applicant') }}: {{ list.name }}
        </span>
      </div>
      <div class="d-flex align-items-center justify-content-between mt-2">
        <div class="text-center">
          <span
            v-if="list.approver_status === WorkflowApproverStatusTypes.PENDING"
            class="badge bg-primary d-block"
          >
            {{ $t('labels.pending') }}
          </span>
          <span
            v-else-if="
              list.approver_status === WorkflowApproverStatusTypes.DONE
            "
            class="badge bg-primary d-block"
          >
            {{ $t('labels.done') }}
          </span>
          <span
            v-else-if="
              list.approver_status === WorkflowApproverStatusTypes.COMPLETED
            "
            class="badge bg-primary d-block"
          >
            {{ $t('labels.completed') }}
          </span>
          <span
            v-else-if="
              list.approver_status === WorkflowApproverStatusTypes.REJECTED
            "
            class="badge bg-primary d-block"
          >
            {{ $t('labels.rejected') }}
          </span>
          <span
            v-else-if="
              list.approver_status === WorkflowApproverStatusTypes.CANCELLED
            "
            class="badge bg-primary d-block"
          >
            {{ $t('labels.cancelled') }}
          </span>
          <span v-if="list.priority === WorkflowPriorityTypes.LOW">!</span>
          <span v-else-if="list.priority === WorkflowPriorityTypes.MID"
            >!!</span
          >
          <span v-else-if="list.priority === WorkflowPriorityTypes.HIGH"
            >!!!</span
          >
        </div>
        <span class="ellipsis">{{ list.workflow_title }}</span>
        <button class="btn"></button>
      </div>
    </a>
  </li>
</template>

<script>
import WorkflowPriorityTypes from '../../../enums/WorkflowPriorityTypes';
import WorkflowApproverStatusTypes from '../../../enums/WorkflowApproverStatusTypes';

export default {
  props: {
    list: {
      type: Object,
      required: true,
    },
  },
  setup() {
    return {
      WorkflowPriorityTypes,
      WorkflowApproverStatusTypes,
    };
  },
};
</script>
