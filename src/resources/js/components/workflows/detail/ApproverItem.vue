<template>
  <li
    class="
      list-group-item
      d-flex
      align-items-center
      justify-content-between
      bg-light
      ps-4 ps-md-5
    "
  >
    <span>{{ approver.name }}</span>
    <div>
      <div
        v-if="approver.reaction !== ReactionTypes.PENDING"
        class="text--circle me-2"
      >
        <span>{{ approver.family_name }}</span>
      </div>
      <i
        @click="$emit('open-modal', approver)"
        v-show="approver.comment && approver.reaction !== ReactionTypes.PENDING"
        class="ai-message-square me-2"
        style="cursor: pointer"
      ></i>
      <button
        v-if="approver.reaction === ReactionTypes.APPROVED"
        type="button"
        class="btn btn-link p-0"
      >
        {{ $t('labels.workflow_reaction_approved') }}
      </button>
      <button
        v-if="approver.reaction === ReactionTypes.RETURNED"
        type="button"
        class="btn btn-link p-0"
      >
        {{ $t('labels.workflow_reaction_returned') }}
      </button>
      <button
        v-if="approver.reaction === ReactionTypes.REJECTED"
        type="button"
        class="btn btn-link p-0"
      >
        {{ $t('labels.workflow_reaction_rejected') }}
      </button>
    </div>
  </li>
</template>

<script>
import { defineComponent } from 'vue';
import ReactionTypes from '../../../enums/WorkflowsReactionTypes';

export default defineComponent({
  name: 'WorkflowApproverItem',
  props: {
    approver: {
      type: Object,
      required: true,
    },
  },
  setup() {
    return {
      ReactionTypes,
    };
  },
});
</script>
