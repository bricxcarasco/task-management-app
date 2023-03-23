<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <a :href="'/workflows/' + list.id" style="color: inherit">
      <span class="fs-ms d-block"
        >{{ $t('labels.application_date') }}：{{ list.created_at }}</span
      >
      <div class="d-flex align-items-center justify-content-between mt-2">
        <div class="text-center">
          <span
            v-if="list.status === WorkflowStatusTypes.APPLYING"
            class="badge bg-primary d-block"
          >
            {{ $t('labels.applying') }}
          </span>
          <span
            v-else-if="list.status === WorkflowStatusTypes.REMANDED"
            class="badge bg-primary d-block"
          >
            {{ $t('labels.remanded') }}
          </span>
          <span
            v-else-if="list.status === WorkflowStatusTypes.APPROVED"
            class="badge bg-primary d-block"
          >
            {{ $t('labels.approved') }}
          </span>
          <span
            v-else-if="list.status === WorkflowStatusTypes.REJECTED"
            class="badge bg-primary d-block"
          >
            {{ $t('labels.rejected') }}
          </span>
          <span
            v-else-if="list.status === WorkflowStatusTypes.CANCELLED"
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
        <button class="btn">
          <!-- <i class="fs-4 ai-copy"></i> -->
        </button>
      </div>
    </a>
  </li>
</template>

<script>
import WorkflowPriorityTypes from '../../../enums/WorkflowPriorityTypes';
import WorkflowStatusTypes from '../../../enums/WorkflowStatusTypes';

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
      WorkflowStatusTypes,
    };
  },
};
</script>
