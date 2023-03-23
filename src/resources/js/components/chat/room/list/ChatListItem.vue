<template>
  <li class="list-group-item list-item-hover" @click="handleClickChatList">
    <div class="d-flex justify-content-between align-items-center">
      <div style="flex: 1">
        <span class="text-danger chat-list-text">
          <strong>
            {{ chat.chat_type }}
          </strong>
        </span>
        <span
          v-if="chat.unread_messages_count > 0"
          class="badge bg-danger"
          style="margin-left: 10px"
        >
          {{ chat.unread_messages_count }}
        </span>
      </div>
      <span class="fs-md me-2 chat-list-text">
        {{ chat.last_message_date }}
      </span>
      <button
        type="button"
        @click="handleClickArchive"
        class="btn btn-link p-0"
        style="width: 25px"
      >
        <i class="ai-archive c-primary"></i>
      </button>
    </div>
    <div class="d-flex align-items-center">
      <img
        class="rounded-circle me-3 img--profile_image_smd"
        :src="chat.chat_image ?? ''"
        :alt="chat.chat_name"
        @error="handleImageLoadError"
        width="60"
      />
      <div>
        <p v-if="chat.unread_messages_count > 0" class="mb-0">
          <strong> {{ chat.chat_name }} </strong>
        </p>
        <p v-else class="mb-0">
          {{ chat.chat_name }}
        </p>
        <p class="mb-0 fs-xs text-gray chat-list-text">
          {{ chat.last_message }}
        </p>
      </div>
    </div>
  </li>
</template>

<script>
import ChatRoomApiService from '../../../../api/chat/room';
import BpheroConfig from '../../../../config/bphero';

export default {
  name: 'ChatListItem',
  props: {
    chat: {
      type: Object,
    },
  },
  emits: ['openChatMessage', 'handleClickArchive'],
  setup(props, { emit }) {
    const chatRoomApiService = new ChatRoomApiService();

    const handleClickChatList = () => {
      emit('openChatMessage', props.chat.chat_id);
    };

    const handleClickArchive = (event) => {
      event.preventDefault();
      event.stopPropagation();
      emit('handleClickArchive', props.chat.chat_id);
    };
    /**
     * Handle invalid or empty images
     *
     * @param {Event} event
     * @returns {void}
     */
    const handleImageLoadError = (event) => {
      /* eslint-disable no-param-reassign */
      event.target.src = BpheroConfig.DEFAULT_IMG;
    };

    return {
      handleClickChatList,
      handleImageLoadError,
      chatRoomApiService,
      handleClickArchive,
    };
  },
};
</script>

<style scoped>
.list-item-hover {
  cursor: pointer;
}

.chat-list-text {
  font-size: 14px !important;
}
</style>
