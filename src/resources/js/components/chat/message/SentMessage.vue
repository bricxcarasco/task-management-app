<template>
  <li class="chat__wrapper you d-flex flex-column align-items-end">
    <span class="chat__name mb-2" v-if="!isEmptyMessage">{{ name }}</span>
    <p class="chat__name mb-0" v-if="isEmptyMessage && message.attachments">
      {{ name }}
    </p>
    <!-- Message -->
    <div class="chat__box p-3" v-if="!isEmptyMessage">
      <p class="chat__content fs-md mt-0 m-0">
        {{ message.message }}
      </p>
    </div>

    <!-- Attachments -->
    <attachment-item
      :key="message.id + '.' + index"
      v-for="(attachment, index) in message.attachments"
      :attachment="attachment"
      @preview-full-image="(id) => $emit('preview-full-image', id)"
    >
    </attachment-item>

    <!-- Date -->
    <p class="chat__time mt-2 mb-0 fs-xs text-end">
      <span
        v-if="message.date == null"
        class="spinner-border spinner-border--sm text-primary"
      ></span>
      <span v-else
        >{{ message.date }}
        <i
          class="fs-md ai-trash delete-chat-message"
          @click="$emit('handleDeleteMessageModal', message.id)"
        ></i
      ></span>
    </p>
  </li>
</template>

<script>
import { ref, computed } from 'vue';
import AttachmentItem from './AttachmentItem.vue';

export default {
  name: 'ChatMessageSent',
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  components: {
    AttachmentItem,
  },
  setup(props) {
    const message = ref(props.message);
    const name = computed(() => `${message.value.name}`);
    const isEmptyMessage = props.message.message === null;

    return {
      name,
      isEmptyMessage,
    };
  },
};
</script>

<style scoped>
.delete-chat-message {
  cursor: pointer;
}
</style>
