<template>
  <div>
    <!-- Page loader -->
    <page-loader :show="pageLoading" />

    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Document List Modal -->
    <document-list-modal
      ref="documentListModalRef"
      :document_type="sendDocumentType"
      :talk_subject="selectData"
      @set-alert="setAlert"
      @choose-document-file="handleChooseDocumentFile"
    />

    <attachment-confirmation-modal
      ref="attachmentConfirmationModalRef"
      :file="file"
      @send-attachment="handleSendMessage"
    />

    <!-- Attachment Menu Modal -->
    <attachment-menu-modal
      @open-file-browser="openFileBrowser"
      @open-document-attachment="openDocumentAttachment"
    />

    <!-- Full Preview Image Modal -->
    <image-preview-modal
      :document_id="targetImagePreview"
      @reset-alert="resetAlert"
      @set-alert="setAlert"
    />

    <delete-chat-mesage
      ref="deleteChatMessageModalRef"
      @deleteChatMessage="handleDeleteMessage"
    />

    <div class="message__wrapper">
      <div class="bg-gray text-center py-2">{{ roomHeader }}</div>
      <div class="container">
        <div class="text-center position-relative p-2 border-bottom">
          <i
            class="ai-arrow-left message__back hoverable"
            @click="handleRedirectToMessages"
          ></i>
          <div class="room-name-container">
            <strong class="room-name-text">{{ roomName }}</strong>
          </div>
        </div>
      </div>
    </div>
    <div class="container position-relative zindex-5 h-100 mb-md-3">
      <div class="row h-100">
        <div class="col-md-4 d-none d-md-block position-relative">
          <div class="message__tools message__tools--chat">
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
                v-if="selectData.type === ServiceSelectionTypesEnum.NEO"
              >
                <i class="ai-plus me-2"></i
                >{{ $t('buttons.compose_neo_message') }}
              </a>
            </div>

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
          <ul
            v-if="chatList.length > 0"
            class="list-group list-group-flush messages mt-2"
            v-bind:class="
              selectData.type === ServiceSelectionTypesEnum.NEO
                ? 'messages--neo'
                : ''
            "
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
        <div class="col-md-8 col-12 p-0 position-relative">
          <!-- Messages list -->
          <ul class="chat px-2" style="overflow: auto">
            <div v-for="message in messages" :key="message.id">
              <!-- Sent messages by logged in user -->
              <div v-if="isSentMessage(message)" class="text-end">
                <sent-message
                  :message="message"
                  @handleDeleteMessageModal="openDeleteMessageModal"
                  @preview-full-image="previewFullImage"
                />
              </div>

              <!-- Received message from other users -->
              <div v-else>
                <received-message
                  :message="message"
                  @preview-full-image="previewFullImage"
                />
              </div>
            </div>
          </ul>
          <div
            v-if="
              (chat.chat_type === ChatTypeEnum.NEO_MESSAGE &&
                talk_subject.type === ServiceSelectionTypesEnum.RIO) ||
              (chat.chat_type === ChatTypeEnum.NEO_MESSAGE &&
                chat.owner_neo_id !== talk_subject.data.id)
            "
            class="d-flex align-items-center justify-content-center p-2 bg-gray"
          >
            {{ $t('paragraphs.cannot_reply_to_this_message') }}
          </div>

          <!-- Message textbox -->
          <div
            v-if="
              chat.chat_type !== ChatTypeEnum.NEO_MESSAGE ||
              (chat.chat_type === ChatTypeEnum.NEO_MESSAGE &&
                talk_subject.type === ServiceSelectionTypesEnum.NEO &&
                chat.owner_neo_id === talk_subject.data.id)
            "
            class="message_textbox__wrapper"
          >
            <form
              class="
                d-flex
                align-items-center
                justify-content-center
                p-2
                chat__message
                bg-light
                w-100
              "
              ref="formRef"
              style="display: inline-flex"
              @submit.prevent="handleSendMessage"
            >
              <span
                class="hoverable"
                data-bs-toggle="modal"
                data-bs-target="#attachment-menu-modal"
              >
                <i class="ai-file-plus fs-2 me-2"></i>
              </span>
              <div
                class="
                  position-relative
                  me-2
                  d-flex
                  flex-column flex-column-reverse
                  w-100
                "
              >
                <textarea
                  class="form-control"
                  type="text"
                  v-model="chatMessage"
                  ref="textarea"
                  id="message-input"
                  @focus="resize"
                  @keyup="resize"
                  rows="1"
                />
                <div
                  class="file-uploader file-uploader--chat"
                  v-show="hasUploadFile"
                >
                  <input
                    type="file"
                    class="js-chat-file-uploader"
                    name="upload_file[]"
                    data-upload="/api/document/file/process"
                    data-chunk="/api/document/file"
                    data-revert="/api/document/file"
                  />
                </div>
              </div>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="!isSendable()"
              >
                {{ $t('buttons.send') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  defineAsyncComponent,
  defineComponent,
  ref,
  computed,
  onMounted,
  watch,
} from 'vue';
import { debounce } from 'lodash';
import SentMessage from './SentMessage.vue';
import ReceivedMessage from './ReceivedMessage.vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import DocumentListModal from './modals/DocumentListModal.vue';
import AttachmentConfirmationModal from './modals/AttachmentConfirmationModal.vue';
import BpheroConfig from '../../../config/bphero';
import AttachmentMenuModal from './AttachmentMenuModal.vue';
import ImagePreviewModal from './ImagePreviewModal.vue';
import DeleteChatMesage from './modals/DeleteChatMessageModal.vue';
import MessageApiService from '../../../api/chat/message';

import ChatTypeEnum from '../../../enums/ChatTypes';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';
import { objectifyForm } from '../../../utils/objectifyForm';
import i18n from '../../../i18n';
import ChatRoomApiService from '../../../api/chat/room';

const ChatListItem = defineAsyncComponent(() =>
  import(
    '../room/list/ChatListItem.vue' /* webpackChunkName: "chat_list_item" */
  )
);

export default defineComponent({
  name: 'ChatMessageIndex',
  props: {
    talk_subjects: {
      type: [Array, Object],
      required: true,
    },
    user: {
      type: [Array, Object],
      required: true,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    talk_subject: {
      type: [Array, Object],
      required: true,
    },
    chat: {
      type: [Array, Object],
      required: true,
    },
    participant: {
      type: [Array, Object],
      required: true,
    },
  },
  mounted() {
    this.resize();
  },
  methods: {
    resize() {
      const { textarea } = this.$refs;
      textarea.style.height = `${textarea.scrollHeight - 4}px`;
    },
  },
  components: {
    AttachmentMenuModal,
    ImagePreviewModal,
    SentMessage,
    ReceivedMessage,
    DeleteChatMesage,
    PageLoader,
    BaseAlert,
    DocumentListModal,
    AttachmentConfirmationModal,
    ChatListItem,
  },
  setup(props) {
    const chatRoomApiService = new ChatRoomApiService();
    const messageApiService = new MessageApiService();
    const talkSubject = ref(props.talk_subject);
    const chat = ref(props.chat);
    const participant = ref(props.participant);
    const messages = ref([]);
    const chatMessage = ref(null);
    const pageLoading = ref(false);
    const formRef = ref({});
    const fileUploader = ref({});
    const hasUploadFile = ref(false);
    const hasMessage = ref(false);
    const targetImagePreview = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });
    const documentListModalRef = ref(null);
    const attachmentConfirmationModalRef = ref(null);
    const sendDocumentType = ref(0);
    const file = ref({});
    const selectData = ref({
      id: props.talk_subject.data.id,
      type: props.talk_subject.type,
    });
    const neoMessageURL = ref('/messages/neo-message');
    const searchData = ref({});
    const chatList = ref([]);
    const sectionLoading = ref(false);

    const deleteChatMessageModalRef = ref(null);
    const deleteMessageId = ref(0);
    const errors = ref(null);

    /**
     * Compute room header depending on chat type & talk room
     */
    const roomHeader = computed(() => {
      switch (chat.value.chat_type) {
        case ChatTypeEnum.CONNECTED:
          if (talkSubject.value.type === ServiceSelectionTypesEnum.RIO) {
            return `RIO (${talkSubject.value.data.full_name})`;
          }
          if (talkSubject.value.type === ServiceSelectionTypesEnum.NEO) {
            return `${talkSubject.value.data.organization_name}`;
          }

          return '-';
        case ChatTypeEnum.CONNECTED_GROUP:
        case ChatTypeEnum.NEO_GROUP:
          return `RIO (${talkSubject.value.data.full_name})`;
        case ChatTypeEnum.NEO_MESSAGE:
          if (talkSubject.value.type === ServiceSelectionTypesEnum.RIO) {
            return `RIO (${talkSubject.value.data.full_name})`;
          }
          if (talkSubject.value.type === ServiceSelectionTypesEnum.NEO) {
            return `${talkSubject.value.data.organization_name}`;
          }
          return '-';
        default:
          return '-';
      }
    });

    /**
     * Compute room name depending on chat type & talk room
     */
    const roomName = computed(() => {
      switch (chat.value.chat_type) {
        case ChatTypeEnum.CONNECTED:
          if (chat.value.rio_receiver !== null) {
            return `${chat.value.rio_receiver.full_name}`;
          }
          if (chat.value.neo_receiver !== null) {
            return `${chat.value.neo_receiver.organization_name}`;
          }

          return '-';
        case ChatTypeEnum.CONNECTED_GROUP:
        case ChatTypeEnum.NEO_GROUP:
          return `${chat.value.room_name}`;
        case ChatTypeEnum.NEO_MESSAGE:
          return `${chat.value.room_name}`;
        default:
          return '-';
      }
    });

    /**
     * Compute sender name depending on chat type & talk room
     */
    const senderName = computed(() => {
      switch (chat.value.chat_type) {
        case ChatTypeEnum.CONNECTED:
          if (talkSubject.value.type === ServiceSelectionTypesEnum.RIO) {
            return `${talkSubject.value.data.full_name}`;
          }
          if (talkSubject.value.type === ServiceSelectionTypesEnum.NEO) {
            return `${talkSubject.value.data.organization_name}`;
          }

          return '-';
        case ChatTypeEnum.CONNECTED_GROUP:
        case ChatTypeEnum.NEO_GROUP:
          return `${talkSubject.value.data.full_name}`;
        case ChatTypeEnum.NEO_MESSAGE:
          return `${talkSubject.value.data.organization_name}`;
        default:
          return '-';
      }
    });

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Set alert messages
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
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set value for document type
     *
     * @param {int} value
     */
    const setDocumentType = (value) => {
      sendDocumentType.value = value;
    };

    /**
     * Close all related document sharing modals
     *
     */
    const closeDocumentList = () => {
      documentListModalRef.value.setLoading(false);
      documentListModalRef.value.hide();
      attachmentConfirmationModalRef.value.setLoading(false);
      attachmentConfirmationModalRef.value.hide();
    };

    /**
     * Open document list modal depends on document type (sharing or personal)
     *
     * @param {int} id
     */
    const openDocumentAttachment = (id) => {
      setDocumentType(id);
      documentListModalRef.value.show();
    };

    /**
     * Open send attachment confirmation modal when attachment is selected
     *
     * @param {object} chosenFile
     */
    const handleChooseDocumentFile = (chosenFile) => {
      file.value = chosenFile;
      closeDocumentList();
      attachmentConfirmationModalRef.value.show();
    };

    /**
     * Open specified modal
     *
     * @returns {void}
     */
    const previewFullImage = (id) => {
      targetImagePreview.value = id;
      const targetModal = document.querySelector('#image-preview-modal');
      /* eslint no-undef: 0 */
      const modal = bootstrap.Modal.getOrCreateInstance(targetModal);
      modal.show();
    };

    /**
     * Auto-scroll chat to bottom
     *
     * @returns {void}
     */
    const handleAutoScroll = () => {
      const messageBody = document.querySelector('.chat');

      setTimeout(() => {
        messageBody.scrollTop = messageBody.scrollHeight;
      }, 2);
    };

    /**
     * Reset Height upon sending
     *
     * @returns {void}
     */
    const resetHeight = () => {
      const messageInput = document.querySelector('#message-input');
      messageInput.style.height = 'auto';
    };

    /**
     * Broadcast messages
     */
    const channel = `chat.room.${chat.value.id}`;
    window.Echo.private(channel).listen('.message.receive', (data) => {
      if (data.status === 200) {
        // Update messages
        messages.value = data.messages;

        handleAutoScroll();
      } else {
        const errorMessage = i18n.global.t(
          'messages.chat.failed_to_load_messages'
        );

        setAlert('failed', errorMessage);
      }
    });

    /**
     * Get messages
     */
    const handleGetMessages = async () => {
      resetAlert();
      setPageLoading(true);

      await messageApiService
        .getMessages(chat.value.id)
        .then((response) => {
          messages.value = response.data.data;
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.chat.failed_to_load_messages'
          );

          setAlert('failed', errorMessage);
          messages.value = [];
        })
        .finally(() => {
          setPageLoading(false);
          handleAutoScroll();
        });
    };

    /**
     * Remove uploaded files
     */
    const clearLocalUploadFiles = () => {
      fileUploader.value.clearFiles();
    };

    /**
     * Send message
     */
    const handleSendMessage = async (chosenFile = []) => {
      const data = {
        chat_id: chat.value.id,
        chat_participant_id: participant.value.id,
        message: chatMessage.value,
        attaches: null,
        upload_file: null,
      };
      const errorMessage = ref(
        i18n.global.t('messages.chat.failed_to_send_messages')
      );

      // Handle file upload
      if (hasUploadFile.value) {
        // Start uploading all files
        await fileUploader.value.uploadFiles();

        // Inject server codes to input data
        const formObject = objectifyForm(formRef.value);
        data.upload_file = formObject.upload_file;
      }

      // Handle document attachment
      if (Array.isArray(chosenFile) && chosenFile.length !== 0) {
        data.attaches = chosenFile;
      }

      // Reset alert
      resetAlert();

      // Append new message
      messages.value.push({
        participant_id: participant.value.id,
        name: senderName,
        participant_type: props.talk_subject.type,
        entity_id: props.talk_subject.data.id,
        message: chatMessage.value,
        date: null, // Loader
      });

      // Auto scroll to new message
      handleAutoScroll();

      // Reset Height of input
      resetHeight();

      // Reset chatbox
      chatMessage.value = '';

      // Clear upload files
      hasUploadFile.value = false;

      await messageApiService
        .sendMessage(data)
        .then((response) => {
          const [newMessage] = response.data.data;

          // Update messages
          messages.value.pop();
          messages.value.push(newMessage);
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Check validation error message
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            if (
              errors.value?.upload_file[0] ===
              i18n.global.t(
                'messages.document_management.insufficient_free_space'
              )
            ) {
              errorMessage.value = errors.value?.upload_file[0];
            }
            messages.value.pop();
          }

          setAlert('failed', errorMessage.value);
        })
        .finally(() => {
          clearLocalUploadFiles();
          closeDocumentList();
        });
    };

    /**
     * Close delete message modal
     */
    const closeDeleteMessageModal = () => {
      deleteMessageId.value = 0;
      deleteChatMessageModalRef.value.hide();
    };

    /**
     * Open delete message modal
     */
    const openDeleteMessageModal = (messageId) => {
      deleteMessageId.value = messageId;
      deleteChatMessageModalRef.value.show();
    };

    /**
     * Handle delete message process
     */
    const handleDeleteMessage = async () => {
      const data = {
        message_id: deleteMessageId.value,
      };

      // Reset alert
      resetAlert();

      // Auto scroll to new message
      handleAutoScroll();

      // Reset Height of input
      resetHeight();

      await messageApiService
        .deleteMessage(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            messages.value = messages.value.filter(
              (message) => message.id !== deleteMessageId.value
            );

            const successMessage = i18n.global.t(
              'messages.chat.success_to_delete_message'
            );
            setAlert('success', successMessage);
          }
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.chat.failed_to_send_messages'
          );
          setAlert('failed', errorMessage);
        })
        .finally(() => closeDeleteMessageModal());
    };

    /**
     * Redirect back to messages list
     */
    const handleRedirectToMessages = () => {
      window.location.href = '/messages';
    };

    /**
     * Redirect back to messages list
     */
    const isSendable = () => hasUploadFile.value || hasMessage.value;

    /**
     * Open file browser
     */
    const openFileBrowser = () => {
      fileUploader.value.pond.browse();
    };

    /**
     * Attach file uploader update file event listener
     */
    const attachUpdateFilesListener = () => {
      fileUploader.value.pond.on('updatefiles', (files) => {
        hasUploadFile.value = files.length > 0;
      });
    };

    /**
     * Attach file uploader warning event listener
     */
    const attachUploadWarningListener = () => {
      fileUploader.value.pond.on('warning', (error) => {
        if (error.body === 'Max files') {
          const errorMessage = i18n.global.t(
            'messages.chat.max_upload_files_reached'
          );
          setAlert('failed', errorMessage);

          return;
        }

        setAlert('failed', error.body);
      });
    };

    /**
     * Initialize file uploader for chat service
     */
    const initializeFileuploader = () => {
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-chat-file-uploader',
        maxFileSize: BpheroConfig.CHAT_SERVICE_MAX_FILE_SIZE,
        maxFiles: BpheroConfig.CHAT_SERVICE_MAX_FILES_COUNT,
        allowMultiple: true,
        chunkUploads: true,
        instantUpload: false,
        allowReorder: true,
        allowProcess: false,
        labelIdle: '',
      });
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
      } catch (error) {
        setAlert('failed');
        chatList.value = [];
        throw error;
      } finally {
        sectionLoading.value = false;
      }
    };

    /**
     * Handle search input event
     *
     * @returns {void}
     */
    const handleSearchInput = computed(() => debounce(getList, 400));

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

    /**
     * Checks if message is owned by current talk subject
     */
    const isSentMessage = (message) =>
      message.participant_type === props.talk_subject.type &&
      message.entity_id === props.talk_subject.data.id;

    const handleClickArchive = async (id) => {
      await chatRoomApiService
        .archiveTalkRoom(id)
        .then(() => {
          getList();
          // Show success alert
          setAlert(
            'success',
            i18n.global.t('messages.archive_restore.archived')
          );
        })
        .catch(() => setAlert('failed'));
    };

    const handleClickRestore = async () => {
      await chatRoomApiService
        .restoreTalkRoom(searchData.value)
        .then(() => {
          getList();
          // Show success alert
          setAlert(
            'success',
            i18n.global.t('messages.archive_restore.restored')
          );
        })
        .catch(() => setAlert('failed'));
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      handleGetMessages();
      initializeFileuploader();
      attachUpdateFilesListener();
      attachUploadWarningListener();
    });

    /**
     * Watch toggle disabling send button
     */
    watch(
      () => chatMessage.value,
      (newValue) => {
        hasMessage.value = newValue.length !== 0;
      }
    );

    /**
     * Redirects to chat room
     *
     * @returns {void}
     */
    const openChatMessage = (id) => {
      window.location.href = `/messages/${id}`;
    };

    // Initial fetch of chat room list
    getList();

    return {
      talkSubject,
      messages,
      chatMessage,
      roomHeader,
      roomName,
      senderName,
      isSendable,
      pageLoading,
      alert,
      setAlert,
      resetAlert,
      formRef,
      handleSendMessage,
      handleRedirectToMessages,
      deleteChatMessageModalRef,
      openDeleteMessageModal,
      handleDeleteMessage,
      ChatTypeEnum,
      ServiceSelectionTypesEnum,
      documentListModalRef,
      attachmentConfirmationModalRef,
      sendDocumentType,
      handleChooseDocumentFile,
      file,
      openDocumentAttachment,
      openFileBrowser,
      clearLocalUploadFiles,
      hasUploadFile,
      previewFullImage,
      targetImagePreview,
      ChatRoomApiService,
      updateTalkSubject,
      selectData,
      neoMessageURL,
      handleSearchInput,
      searchData,
      chatList,
      handleClickArchive,
      handleClickRestore,
      openChatMessage,
      isSentMessage,
    };
  },
});
</script>
