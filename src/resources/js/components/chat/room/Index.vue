<template>
  <div>
    <div
      class="container position-relative pb-4 mb-md-3 home--height pt-6 pt-md-6"
    >
      <create-neo-group-modal
        :session="sessionData"
        @reset-list="getList"
        @set-alert="setAlert"
        @closeAlert="resetAlert"
      />
      <base-alert
        :success="alert.success"
        :danger="alert.failed"
        :message="alert.message"
        @closeAlert="resetAlert"
      />
      <div class="row">
        <div
          class="col-12 offset-md-3 col-md-9 position-relative message__room"
        >
          <div class="message__tools">
            <!-- Talk Subject Selection -->
            <div class="mb-2">
              <select
                class="form-select"
                v-model="selectData"
                id="select-input"
                @change="updateTalkSubject()"
              >
                <option
                  v-for="(value, key) in talk_subjects"
                  :key="key"
                  v-bind:value="{
                    id: value.id,
                    type: value.type,
                  }"
                >
                  {{ value.display_name }}
                </option>
              </select>
            </div>
            <div class="text-end mb-2">
              <a
                :href="neoMessageURL"
                v-if="selectData.type === ServiceSelectionTypes.NEO"
              >
                <i class="ai-plus me-2"></i
                >{{ $t('buttons.compose_neo_message') }}</a
              >
            </div>
            <!-- Chat Room Search -->
            <div class="mb-2 pb-4 border-bottom">
              <div class="input-group">
                <input
                  v-model="searchData.name"
                  class="form-control text-center"
                  type="text"
                  @input="handleSearchInput"
                  :placeholder="$t('buttons.search')"
                />
              </div>
            </div>
          </div>
          <div class="position-relative">
            <section-loader :show="sectionLoading" />

            <!-- Chat Room Actions -->
            <chat-room-action
              :key="sessionData"
              :session="sessionData"
              @handleClickRestore="handleClickRestore"
            />
            <!-- Chat Room List -->
            <ul
              v-if="chatList.length > 0"
              class="list-group list-group-flush messages mt-2"
            >
              <chat-list-item
                v-for="chat in chatList"
                :key="chat.chat_id"
                :chat="chat"
                @openChatMessage="openChatMessage"
                @handleClickArchive="handleClickArchive"
              />
            </ul>
            <div v-else class="d-flex justify-content-center mt-3">
              {{ $t('paragraphs.chat_result_empty') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useI18n } from 'vue-i18n';
import ChatRoomAction from './list/ChatRoomAction.vue';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';
import ChatRoomApiService from '../../../api/chat/room';
import BaseAlert from '../../base/BaseAlert.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import CreateNeoGroupModal from './modals/CreateGroupModal.vue';
import ChatListItem from './list/ChatListItem.vue';

export default {
  name: 'ChatRoomIndex',
  props: {
    talk_subjects: {
      type: [Array, Object],
      required: true,
    },
    session: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAlert,
    ChatRoomAction,
    ChatListItem,
    SectionLoader,
    CreateNeoGroupModal,
  },
  setup(props) {
    const chatRoomApiService = new ChatRoomApiService();
    const searchData = ref({});
    const chatList = ref([]);
    const sectionLoading = ref(false);
    const neoMessageURL = ref('/messages/neo-message');
    const selectData = ref({
      id: props.session.data.id,
      type: props.session.type,
    });
    const sessionData = ref(props.session);
    const alert = ref({
      success: false,
      failed: false,
    });
    const i18n = useI18n();

    /**
     * Reset alert values
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
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
     * Get list of chat rooms
     *
     * @returns {void}
     */
    const getList = async () => {
      sectionLoading.value = true;
      resetAlert();

      try {
        const getListApi = await chatRoomApiService.searchChat(
          searchData.value
        );
        const getListResponseData = getListApi.data;
        chatList.value = getListResponseData?.data || [];
        sessionData.value = getListResponseData?.current_session || [];
      } catch (error) {
        setAlert('failed');
        chatList.value = [];
        throw error;
      } finally {
        sectionLoading.value = false;
      }
    };

    /**
     * Update talk subject selection
     *
     * @returns {void}
     */
    const updateTalkSubject = async () => {
      const data = {
        id: selectData.value.id,
        type: selectData.value.type,
      };

      sectionLoading.value = true;
      resetAlert();

      try {
        await chatRoomApiService.updateTalkSubject(data);

        getList();
      } catch (error) {
        sectionLoading.value = false;
        chatList.value = [];
        setAlert('failed');
        throw error;
      }
    };

    const handleClickArchive = async (id) => {
      sectionLoading.value = true;
      await chatRoomApiService
        .archiveTalkRoom(id)
        .then(() => {
          getList();
          // Show success alert
          setAlert('success', i18n.t('messages.archive_restore.archived'));
        })
        .catch(() => setAlert('failed'))
        .finally(() => {
          sectionLoading.value = false;
        });
    };

    const handleClickRestore = async () => {
      sectionLoading.value = true;
      await chatRoomApiService
        .restoreTalkRoom(searchData.value)
        .then(() => {
          getList();
          // Show success alert
          setAlert('success', i18n.t('messages.archive_restore.restored'));
        })
        .catch(() => setAlert('failed'))
        .finally(() => {
          sectionLoading.value = false;
        });
    };

    /**
     * Handle search input event
     *
     * @returns {void}
     */
    const handleSearchInput = computed(() => debounce(getList, 400));

    /**
     * Redirects to chat room
     *
     * @returns {void}
     */
    const openChatMessage = (id) => {
      window.location.href = `/messages/${id}`;
    };

    /**
     * Watch on alert changes
     */
    watch(alert.value, () => {
      if (alert.value.success || alert.value.failed) {
        setTimeout(() => {
          resetAlert();
        }, 3000);
      }
    });

    // Initial fetch of chat room list
    getList();

    return {
      ServiceSelectionTypes,
      ChatRoomApiService,
      updateTalkSubject,
      neoMessageURL,
      selectData,
      searchData,
      sessionData,
      chatList,
      openChatMessage,
      alert,
      resetAlert,
      setAlert,
      getList,
      sectionLoading,
      handleSearchInput,
      handleClickArchive,
      handleClickRestore,
    };
  },
};
</script>
