<template>
  <div>
    <!-- Issue payment modal -->
    <issue-payment-modal
      v-if="product != null"
      :contact="contact"
      :product="product"
      :settings="settings"
      :bank_accounts="bank_accounts"
    />

    <!-- Issued payment URL modal -->
    <issued-payment-url-modal
      v-if="product != null"
      @set-alert="setAlert"
      @update-messages="handleGetMessages"
    />

    <!-- Image preview modal -->
    <image-preview-modal />

    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Page loader -->
    <page-loader :show="pageLoading" />

    <div class="container position-relative zindex-5 h-100 mb-md-3">
      <div class="row">
        <div class="col-md-12 col-12 p-0 position-relative">
          <div class="message__wrapper">
            <!-- Message header -->
            <div class="container">
              <h3 class="py-3 mb-0 text-center">
                {{
                  $t('headers.service_owned_classifieds', { name: serviceName })
                }}
              </h3>

              <div v-if="product != null">
                <div v-if="!is_buyer" class="text-center">
                  <div v-if="hasSettings">
                    <button
                      type="button"
                      class="btn btn-primary w-100 w-md-auto"
                      data-bs-toggle="modal"
                      data-bs-target="#issue-payment"
                    >
                      {{ $t('buttons.issue_payment_screen') }}
                    </button>
                  </div>
                  <div v-else>
                    <button
                      type="button"
                      class="btn btn-primary w-100 w-md-auto"
                      disabled
                    >
                      {{ $t('buttons.issue_payment_screen') }}
                    </button>
                    <span class="text-danger">
                      {{
                        $t('messages.classifieds.no_receiving_account_setting')
                      }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center p-1">
                  <a :href="productDetailLink" target="_blank">
                    {{ contact.title }}
                  </a>
                </div>
              </div>
              <div v-else class="text-center">
                <p class="text-danger font-bolder">
                  {{ $t('messages.classifieds.product_has_been_deleted') }}
                </p>
              </div>

              <div class="text-center position-relative p-2 border-bottom">
                <i
                  class="ai-arrow-left message__back hoverable"
                  @click="handleRedirectBack"
                ></i>
                <strong>{{ receiverName }}</strong>
              </div>
            </div>

            <!-- Message conversation -->
            <ul class="chat chat-inquiry px-2 pt-3">
              <div v-for="message in messages" :key="message.id">
                <!-- Sent messages by selected service -->
                <div v-if="isSentMessage(message)" class="mt-2">
                  <sent-message
                    :message="message"
                    @preview-image="openPreviewModal"
                  />
                </div>

                <!-- Received message from other users -->
                <div v-else class="mt-2">
                  <received-message
                    :message="message"
                    @preview-image="openPreviewModal"
                  />
                </div>
              </div>
            </ul>

            <!-- Send message section -->
            <form
              class="
                d-flex
                align-items-center
                justify-content-center
                p-2
                chat__message chat__message--inquiry
                bg-light
                w-100
              "
              ref="formRef"
              style="display: inline-flex"
              @submit.prevent="handleSendMessage"
            >
              <span class="hoverable" @click="openFileBrowser">
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
                <div class="file-uploader" v-show="hasUploadFile">
                  <input
                    type="file"
                    class="js-file-uploader"
                    name="upload_file[]"
                    data-upload="/api/classifieds/messages/file/process"
                    data-chunk="/api/classifieds/messages/file/chunk"
                    data-revert="/api/classifieds/messages/file/revert"
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
import { defineComponent, ref, onMounted, computed, watch } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import IssuePaymentModal from '../components/IssuePaymentModal.vue';
import IssuedPaymentUrlModal from '../components/IssuedPaymentUrlModal.vue';
import ImagePreviewModal from '../components/ImagePreviewModal.vue';
import SentMessage from '../components/SentMessage.vue';
import ReceivedMessage from '../components/ReceivedMessage.vue';
import ClassifiedMessageSenderEnum from '../../../enums/ClassifiedMessageSender';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';
import ContactMessageApiService from '../../../api/classifieds/contact-messages';
import { objectifyForm } from '../../../utils/objectifyForm';
import BpheroConfig from '../../../config/bphero';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'ViewProductsPage',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    contact: {
      type: [Array, Object],
      required: true,
    },
    receiver: {
      type: [Array, Object],
      required: true,
    },
    product: {
      type: [Array, Object],
      required: false,
    },
    settings: {
      type: [Array, Object],
      required: true,
    },
    bank_accounts: {
      type: [Array, Object],
      required: true,
    },
    is_buyer: {
      type: Boolean,
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    IssuePaymentModal,
    IssuedPaymentUrlModal,
    ImagePreviewModal,
    SentMessage,
    ReceivedMessage,
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
  setup(props) {
    /**
     * Data properties
     */
    const contactMessageApiService = new ContactMessageApiService();
    const service = ref(props.service);
    const contact = ref(props.contact);
    const receiver = ref(props.receiver);
    const settings = ref(props.settings);
    const isBuyer = ref(props.is_buyer);
    const pageLoading = ref(false);
    const messages = ref([]);
    const chatMessage = ref(null);
    const hasUploadFile = ref(false);
    const hasValidImages = ref(true);
    const hasMessage = ref(false);
    const hasSettings = ref(false);
    const fileUploader = ref({});
    const formRef = ref({});
    const alert = ref({
      success: false,
      failed: false,
    });

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
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case ServiceSelectionTypesEnum.RIO:
          return data.full_name;
        case ServiceSelectionTypesEnum.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Get contact receiver name
     *
     * @returns {string}
     */
    const receiverName = computed(() => {
      const { data } = receiver.value;

      switch (receiver.value.type) {
        case ServiceSelectionTypesEnum.RIO:
          return data.full_name;
        case ServiceSelectionTypesEnum.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Set product detail redirection link
     *
     * @returns {string}
     */
    const productDetailLink = computed(
      () => `/classifieds/${contact.value.classified_sale_id}`
    );

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
     * Broadcast messages
     */
    const channel = `inquiry.conversation.${contact.value.id}`;
    window.Echo.private(channel).listen('.inquiry.receive', (data) => {
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
     * Reset Height upon sending
     *
     * @returns {void}
     */
    const resetHeight = () => {
      const messageInput = document.querySelector('#message-input');
      messageInput.style.height = 'auto';
    };

    /**
     * Checks if message is owned by current service
     */
    const isSentMessage = (message) =>
      message.owner_type === service.value.type &&
      message.owner_id === service.value.data.id;

    /**
     * Remove uploaded files
     */
    const clearLocalUploadFiles = () => {
      fileUploader.value.clearFiles();
    };

    /**
     * Append new message to conversation
     */
    const appendNewMessage = () => {
      const newMessage = {
        classified_contact_id: contact.value.id,
        owner_type: service.value.type,
        owner_id: service.value.data.id,
        sender: isBuyer.value
          ? ClassifiedMessageSenderEnum.BUYER
          : ClassifiedMessageSenderEnum.SELLER,
        name: serviceName,
        message: chatMessage.value,
        image_urls: [],
        date: null, // Loader
      };

      if (hasUploadFile.value) {
        const files = fileUploader.value.pond.getFiles();
        files.forEach(() => {
          newMessage.image_urls.push(null);
        });
      }

      messages.value.push(newMessage);
    };

    /**
     * Get inquiry messages
     */
    const handleGetMessages = async () => {
      resetAlert();
      setPageLoading(true);

      await contactMessageApiService
        .getMessages(contact.value.id)
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
     * Send inquiry message
     */
    const handleSendMessage = async (chosenFile = []) => {
      const data = {
        classified_contact_id: contact.value.id,
        sender: isBuyer.value
          ? ClassifiedMessageSenderEnum.BUYER
          : ClassifiedMessageSenderEnum.SELLER,
        message: chatMessage.value,
        attaches: null,
        upload_file: null,
      };

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
      appendNewMessage();

      // Auto scroll to new message
      handleAutoScroll();

      // Reset Height of input
      resetHeight();

      // Reset chatbox
      chatMessage.value = '';

      // Clear upload files
      hasUploadFile.value = false;

      await contactMessageApiService
        .sendMessage(data)
        .then((response) => {
          const [newMessage] = response.data.data;

          // Update messages
          messages.value.pop();
          messages.value.push(newMessage);
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.chat.failed_to_send_messages'
          );

          // Remove latest message and return error
          messages.value.pop();
          setAlert('failed', errorMessage);
        })
        .finally(() => {
          clearLocalUploadFiles();
        });
    };

    /**
     * Redirection back to inquiry list page
     */
    const handleRedirectBack = () => {
      window.location.href = '/classifieds/messages';
    };

    /**
     * Open image preview modal
     */
    const openPreviewModal = (image) => {
      /* eslint no-undef: 0 */
      const imagePreviewNode = document.getElementById('image-preview-modal');
      const imagePreview = computed(
        () => new bootstrap.Modal(imagePreviewNode)
      );

      const imageSelector = imagePreviewNode.querySelector('.js-image-src');
      imageSelector.src = image;
      imagePreview.value.show();
    };

    /**
     * Check if message is sendable
     */
    const isSendable = () => {
      if (!hasValidImages.value) {
        return false;
      }

      return hasUploadFile.value || hasMessage.value;
    };

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
        hasValidImages.value = fileUploader.value.isValidFiles();
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
        selector: '.js-file-uploader',
        allowFileTypeValidation: true,
        maxFileSize: BpheroConfig.CLASSIFIED_SERVICE_MAX_FILE_SIZE,
        maxFiles: BpheroConfig.CLASSIFIED_SERVICE_MAX_FILES_COUNT,
        acceptedFileTypes: BpheroConfig.CLASSIFIED_SERVICE_ALLOWED_TYPES,
        allowMultiple: true,
        chunkUploads: true,
        instantUpload: false,
        allowReorder: true,
        allowProcess: false,
        labelIdle: '',
      });
    };

    /**
     * Check if there are selectable settings
     */
    const attachNoSettingsListener = () => {
      hasSettings.value = settings.value.length !== 0;
    };

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
     * Mounted properties
     */
    onMounted(() => {
      handleGetMessages();
      initializeFileuploader();
      attachUpdateFilesListener();
      attachUploadWarningListener();
      attachNoSettingsListener();
    });

    return {
      serviceName,
      receiverName,
      pageLoading,
      formRef,
      alert,
      setAlert,
      resetAlert,
      messages,
      isSentMessage,
      chatMessage,
      isSendable,
      openFileBrowser,
      hasUploadFile,
      hasSettings,
      handleSendMessage,
      handleRedirectBack,
      productDetailLink,
      openPreviewModal,
      handleGetMessages,
    };
  },
});
</script>
