<template>
  <li class="chat__wrapper sender d-flex align-items-start">
    <!-- Profile Image -->
    <img
      class="rounded-circle me-3 img--profile_image_sm"
      :src="message.user_image"
      alt="User"
      width="40"
      @error="
        Common.handleNotFoundImageException(
          $event,
          DefaultImageCategory.RIO_NEO
        )
      "
    />

    <div class="d-flex flex-column align-items-start">
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
      <p class="chat__time mt-2 mb-0 fs-xs">{{ message.date }}</p>
    </div>
  </li>
</template>

<script>
import { ref, computed } from 'vue';
import AttachmentItem from './AttachmentItem.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';

export default {
  name: 'ChatMessageReceived',
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
      Common,
      DefaultImageCategory,
    };
  },
};
</script>

<style scoped>
.chat_image {
  height: 200px;
  width: 200px;
}
</style>
