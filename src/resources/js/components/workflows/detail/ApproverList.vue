<template>
  <ol class="ps-0 border-1 list--approver list-group">
    <approver-item
      v-for="approver in approvers"
      :key="approver.id"
      :approver="approver"
      @open-modal="$emit('open-modal', approver)"
    />
  </ol>
</template>

<script>
import { defineComponent } from 'vue';
import ApproverItem from './ApproverItem.vue';

export default defineComponent({
  name: 'WorkflowApproverList',
  props: {
    approvers: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    ApproverItem,
  },
  emits: ['open-modal'],
});
</script>
