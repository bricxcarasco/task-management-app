<template>
  <div>
    <!-- Free slots explanation modal -->
    <explanation-modal />

    <!-- Manual registration of email address modal -->
    <manual-email-register
      ref="manualEmailRegisterModalRef"
      @register-manual-recipient="handleManualRecipientRegister"
      @set-alert="setAlert"
    />

    <!-- Document List Modal -->
    <document-list-modal
      ref="documentListModalRef"
      :document_type="sendDocumentType"
      :service="service"
      @set-alert="setAlert"
      @choose-document-file="handleChooseDocumentFile"
    />

    <!-- Connection List Modal -->
    <connection-list-modal
      ref="connectionListModalRef"
      :service="service"
      @set-alert="setAlert"
      @choose-target-connection="handleSelectedRecipient"
    />

    <transition-confirmation-modal
      ref="transitionConfirmationModalRef"
      :form_data="formData"
      @set-alert="setAlert"
      @set-errors="setErrors"
      @reset-form="resetFormData"
      @update-slots="updateSlots"
    />

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
      <base-alert
        :success="alert.success"
        :danger="alert.failed"
        :message="alert.message"
        @closeAlert="resetAlert"
      />
      <div class="row">
        <!-- Content-->
        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div class="position-relative">
              <h3 class="py-3 mb-0 text-center">
                {{
                  $t('headers.electronic_contract_registration', {
                    name:
                      service.data.full_name ||
                      service.data.organization_name ||
                      '',
                  })
                }}
              </h3>
              <div class="text-end">
                <!-- Slot Component -->
                <span>
                  {{ $t('headers.free_tier') }}
                </span>
                <span class="badge bg-green badge-size">
                  {{ $t('labels.remaining') }} {{ availableSlot.slot }}
                </span>
                <button
                  v-if="!is_app"
                  type="button"
                  class="btn btn-link p-0"
                  data-bs-toggle="modal"
                  data-bs-target="#electronicContractExplanation"
                >
                  <i class="ai-alert-circle fs-xl"></i>
                </button>
                <div class="text-gray">
                  {{ $t('labels.validity_period') }}
                  {{ availableSlot.expiration_date }}
                </div>
              </div>
            </div>
            <div class="mt-4">
              <div class="mb-3">
                <!-- Sender Email -->
                <label for="search-input" class="form-label">
                  ■{{
                    $t('labels.email_address_of', {
                      name:
                        service.data.full_name ||
                        service.data.organization_name ||
                        '',
                    })
                  }}
                </label>
                <div v-if="service.email_addresses.length > 0">
                  <div
                    v-for="(email_address, index) in service.email_addresses"
                    :key="email_address"
                    class="form-check"
                  >
                    <input
                      v-model="formData.sender_email"
                      type="radio"
                      class="form-check-input"
                      id="email-address-radio"
                      name="email-address-radio"
                      :checked="index === 0"
                      :value="email_address"
                      :disabled="!isAccessible"
                    />
                    <label class="form-check-label">
                      {{ email_address }}
                    </label>
                    <div
                      v-show="errors?.sender_email"
                      v-for="(error, index) in errors?.sender_email"
                      :key="index"
                      class="invalid-feedback d-block"
                    >
                      {{ error }}
                    </div>
                  </div>
                </div>
                <p class="text-center p-2 mb-0" v-else>
                  {{ $t('messages.email_is_not_registered') }}
                </p>
                <!-- Error Message - Document Field -->
                <div
                  v-show="errors?.sender_email"
                  v-for="(error, index) in errors?.sender_email"
                  :key="index"
                  class="invalid-feedback d-block"
                >
                  {{ error }}
                </div>
              </div>
              <div class="mb-3">
                <!-- File Selection -->
                <label for="search-input" class="form-label">
                  ■{{ $t('labels.file_selection') }}
                </label>
                <!-- Shared File Selection -->
                <button
                  type="button"
                  class="btn btn-outline-secondary w-100 btn--xs-font"
                  @click="openDocumentList(DocumentListType.SHARED)"
                  :disabled="!isAccessible"
                >
                  {{ $t('buttons.document_management_sharing') }}
                </button>
                <!-- Personal File Selection -->
                <button
                  class="btn btn-outline-secondary w-100 mt-2 btn--xs-font"
                  @click="openDocumentList(DocumentListType.PERSONAL)"
                  :disabled="!isAccessible"
                >
                  {{ $t('buttons.document_management_personal') }}
                </button>
                <button
                  class="btn btn-outline-secondary w-100 mt-2 btn--xs-font"
                  @click="openFileBrowser"
                  :disabled="!isAccessible"
                >
                  {{ $t('buttons.select_from_local_files') }}
                </button>
              </div>
              <div v-show="isDocumentSelected" class="mt-4 border p-2 p-md-4">
                <!-- Document Display -->
                <p>
                  {{ $t('labels.selected_file') }}
                </p>
                <div
                  :class="
                    hasUploadFile
                      ? ''
                      : 'd-flex align-items-center justify-content-between'
                  "
                >
                  <!-- Local File Display -->
                  <div class="file-uploader mt-2" v-show="hasUploadFile">
                    <input
                      type="file"
                      class="js-chat-file-uploader"
                      name="upload_file[]"
                      data-upload="/api/electronic-contracts/file/process"
                      data-chunk="/api/electronic-contracts/file"
                      data-revert="/api/electronic-contracts/file"
                    />
                  </div>
                  <!-- Document Management Display -->
                  <span
                    v-if="!hasUploadFile"
                    id="selected-file-for-contract"
                    class="electronic-contract-selected-file"
                    >{{ file?.name }}</span
                  >
                  <input
                    type="hidden"
                    id="document-id"
                    name="document-id"
                    v-model="formData.selected_document_id"
                  />
                  <button
                    v-if="!hasUploadFile"
                    type="button"
                    class="btn btn-link"
                    @click="deleteSelectedFile()"
                  >
                    {{ $t('buttons.delete') }}
                  </button>
                </div>
              </div>
              <!-- Error Message - Document Field -->
              <div
                v-show="errors?.document_field"
                v-for="(error, index) in errors?.document_field"
                :key="index"
                class="invalid-feedback d-block"
              >
                {{ error }}
              </div>
              <div class="mt-3 mb-3">
                <!-- Recipient Selection -->
                <label for="search-input" class="form-label">
                  ■{{ $t('labels.electronic_contract_destination_selection') }}
                </label>
                <!-- Connection Selection -->
                <button
                  type="button"
                  class="btn btn-outline-secondary w-100 btn--xs-font"
                  @click="openConnectionList"
                  :disabled="!isAccessible"
                >
                  {{ $t('buttons.connection_selection') }}
                </button>
                <!-- Manual Recipient Registration -->
                <button
                  class="btn btn-outline-secondary w-100 mt-2 btn--xs-font"
                  @click="openManualEmailRegisterModal"
                  :disabled="!isAccessible"
                >
                  {{ $t('buttons.enter_email_address') }}
                </button>
              </div>
              <!-- Recipient Display -->
              <div
                v-if="isManualRecipientSelected"
                class="mt-4 border p-2 p-md-4"
              >
                <p>
                  {{ $t('labels.destination_name') }}
                </p>
                <div class="d-flex align-items-center justify-content-between">
                  <span>
                    {{ formData.recipient_name }}
                  </span>
                  <button
                    class="btn btn-link"
                    type="button"
                    @click="deleteSelectedRecipient"
                  >
                    {{ $t('buttons.delete') }}
                  </button>
                </div>
                <p>
                  {{ formData.recipient_email }}
                </p>
              </div>
              <!-- Connected Recipient -->
              <div
                v-if="isConnectedRecipientSelected"
                class="mt-4 border p-2 p-md-4"
                id="selected-recipient-for-contract-div"
              >
                <p>
                  {{ $t('labels.destination_name') }}
                </p>
                <div
                  class="
                    d-flex
                    align-items-between align-items-center
                    w-100
                    p-2
                  "
                >
                  <div class="flex-1">
                    <img
                      id="selected-connection-for-contract-image"
                      class="
                        rounded-circle
                        me-2
                        d-inline-block
                        img--profile_image_sm
                      "
                      :src="recipient.profile_photo"
                      :alt="recipient.name"
                      width="40"
                    />
                    <span
                      class="fs-xs c-primary ms-2"
                      id="selected-connection-for-contract-name"
                      >{{ recipient.name }}</span
                    >
                  </div>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="deleteSelectedRecipient"
                  >
                    {{ $t('buttons.delete') }}
                  </button>
                </div>
                <div v-if="recipientEmailList.length > 0">
                  <div
                    v-for="(emailList, index) in recipientEmailList"
                    :key="emailList.id"
                    class="form-check"
                  >
                    <input
                      :key="index"
                      v-model="formData.recipient_email"
                      type="radio"
                      class="form-check-input"
                      :id="'selected-recipient-email-address-radio-' + index"
                      name="selected-recipient-email-address-radio"
                      :value="emailList.email"
                    />
                    <label class="form-check-label">
                      {{ emailList.email }}
                    </label>
                  </div>
                </div>
                <p class="text-center p-2 mb-0" v-else>
                  {{ $t('messages.email_is_not_registered') }}
                </p>
              </div>
              <!-- Error Message - Recipient -->
              <div
                v-show="errors?.recipient"
                v-for="(error, index) in errors?.recipient"
                :key="index"
                class="invalid-feedback d-block"
              >
                {{ error }}
              </div>
              <div class="text-center mt-4">
                <button
                  v-if="isAccessible"
                  type="button"
                  :disabled="isSubmitButtonDisabled"
                  class="btn btn-primary btn--xs-font"
                  id="confirm-contract-button"
                  data-bs-toggle="modal"
                  data-bs-target="#electronic-contract-transition-confirm-modal"
                >
                  {{ $t('buttons.link_with_electronic_contract_service') }}
                </button>
                <button
                  v-else
                  type="button"
                  class="btn btn-link d-md-block mx-auto fs-md p-0 mt-2"
                  data-bs-toggle="modal"
                  data-bs-target="#electronicContractExplanation"
                >
                  {{ $t('buttons.wish_to_use') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import BpheroConfig from '../../../config/bphero';
import i18n from '../../../i18n';
import BaseAlert from '../../base/BaseAlert.vue';
import ExplanationModal from '../modals/ExplanationModal.vue';
import ManualEmailRegister from '../modals/ManualEmailRegisterModal.vue';
import DocumentListModal from '../modals/DocumentListModal.vue';
import ConnectionListModal from '../modals/ConnectionListModal.vue';
import ConnectionListApiService from '../../../api/electronic_contract/connection-list';
import DocumentListType from '../../../enums/DocumentListType';
import TransitionConfirmationModal from '../modals/TransitionConfirmationModal.vue';

export default {
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
    available_slot: {
      type: [Array, Object],
      required: true,
    },
    is_app: {
      type: [Boolean],
      required: true,
    },
  },
  components: {
    BaseAlert,
    ExplanationModal,
    ManualEmailRegister,
    DocumentListModal,
    ConnectionListModal,
    TransitionConfirmationModal,
  },
  setup(props) {
    const connectionListApi = new ConnectionListApiService();
    const availableSlot = ref(props.available_slot);
    const sendDocumentType = ref(0);
    const file = ref({});
    const errors = ref(null);
    const fileUploader = ref({});
    const hasUploadFile = ref(false);
    const recipient = ref({});
    const recipientEmailList = ref({});
    const isDocumentSelected = ref(false);
    const isConnectedRecipientSelected = ref(false);
    const isManualRecipientSelected = ref(false);

    const documentListModalRef = ref(null);
    const connectionListModalRef = ref(null);
    const manualEmailRegisterModalRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
      message: '',
    });

    const formData = ref({
      sender_email: props.service.email_addresses[0],
    });

    /**
     * Reset alert message
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
      alert.value.message = '';
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
     * Set errors
     */
    const setErrors = (messages) => {
      errors.value.document_field =
        messages.local_file ?? messages.selected_document_id ?? null;
      errors.value.recipient =
        messages.selected_connection_id ??
        messages.recipient_name ??
        messages.recipient_email ??
        null;
    };

    /**
     * Open file browser
     */
    const openFileBrowser = () => {
      fileUploader.value.pond.getFile();
      fileUploader.value.pond.browse();
    };

    /**
     * Remove uploaded files
     */
    const clearLocalUploadFiles = () => {
      fileUploader.value.clearFiles();
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
     * Open document list modal depends on document type (sharing or personal)
     *
     * @param {int} id
     */
    const openDocumentList = (id) => {
      setDocumentType(id);
      documentListModalRef.value.show();
    };

    /**
     * Open document list modal depends on document type (sharing or personal)
     *
     * @param {int} id
     */
    const openConnectionList = () => {
      connectionListModalRef.value.show();
    };

    /**
     * Close all related document sharing modals
     *
     */
    const closeDocumentList = () => {
      documentListModalRef.value.setLoading(false);
      documentListModalRef.value.hide();
    };

    /**
     * Close all related document sharing modals
     *
     */
    const closeConnectionList = () => {
      connectionListModalRef.value.setLoading(false);
      connectionListModalRef.value.hide();
    };

    /**
     * Get shared document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const getRecipientEmailList = async (targetRecipient) => {
      await connectionListApi
        .getEmails(targetRecipient.value)
        .then((response) => {
          recipientEmailList.value = response.data.data;

          // Set first email to form data
          if (recipientEmailList.value.length > 0) {
            formData.value.recipient_email = recipientEmailList.value[0].email;
          }
        })
        .catch((error) => {
          setAlert(error.response.data);
        });
    };

    /**
     * Delete selected file for contract
     *
     */
    const deleteSelectedFile = () => {
      file.value = {};
      isDocumentSelected.value = false;
      hasUploadFile.value = false;
      delete formData.value.selected_document_id;
      delete formData.value.local_file;
      clearLocalUploadFiles();
    };

    /**
     * Display Selected file for contract
     *
     * @param {object} chosenFile
     */
    const handleChooseDocumentFile = (chosenFile) => {
      deleteSelectedFile();
      file.value = chosenFile;
      formData.value.selected_document_id = file.value.document_id;
      isDocumentSelected.value = true;
      closeDocumentList();
    };

    /**
     * Delete selected recipient
     *
     * @param {object} chosenFile
     */
    const deleteSelectedRecipient = () => {
      isManualRecipientSelected.value = false;
      isConnectedRecipientSelected.value = false;
      delete formData.value.recipient_name;
      delete formData.value.recipient_email;
      delete formData.value.selected_connection_id;
      delete formData.value.selected_connection_type;
    };

    /**
     * Display Selected recipient
     *
     * @param {object} chosenRecipient
     */
    const handleSelectedRecipient = (chosenRecipient) => {
      deleteSelectedRecipient();
      recipient.value = chosenRecipient;
      formData.value.recipient_name = recipient.value.name;
      formData.value.selected_connection_id = recipient.value.id;
      formData.value.selected_connection_type = recipient.value.service;
      isConnectedRecipientSelected.value = true;
      getRecipientEmailList(recipient);
      closeConnectionList();
    };

    /**
     * Open manual email register modal
     *
     * @returns {void}
     */
    const openManualEmailRegisterModal = () => {
      manualEmailRegisterModalRef.value.show();
    };

    /**
     * Close manual email register modal
     *
     * @returns {void}
     */
    const closeManualEmailRegisterModal = () => {
      manualEmailRegisterModalRef.value.setLoading(false);
      manualEmailRegisterModalRef.value.hide();
    };

    /**
     * Handle process of manual registration of recipients
     *
     * @returns {void}
     */
    const handleManualRecipientRegister = (data) => {
      deleteSelectedRecipient();
      isManualRecipientSelected.value = true;
      formData.value.recipient_name = data.name;
      formData.value.recipient_email = data.email_address;
      closeManualEmailRegisterModal();
    };

    /**
     * Computed property for identifying when to disable submit button
     */
    const isSubmitButtonDisabled = computed(
      () =>
        !(
          formData.value.sender_email &&
          (formData.value.selected_document_id || formData.value.local_file) &&
          formData.value.recipient_email &&
          formData.value.recipient_name
        )
    );

    /**
     * Computed property for identifying when form is accessible
     */
    const isAccessible = computed(
      () => availableSlot.value.slot > 0 && !availableSlot.value.expired
    );

    /**
     * Reset form data
     */
    const resetFormData = () => {
      formData.value = {};
      /* eslint-disable prefer-destructuring */
      formData.value.sender_email = props.service.email_addresses[0];
      file.value = {};
      errors.value = null;
      hasUploadFile.value = false;
      recipient.value = {};
      recipientEmailList.value = {};
      isDocumentSelected.value = false;
      isConnectedRecipientSelected.value = false;
      isManualRecipientSelected.value = false;
      clearLocalUploadFiles();
    };

    /**
     * Update Slots
     */
    const updateSlots = (data) => {
      availableSlot.value = data;
    };

    /**
     * Initialize file uploader for chat service
     */
    const initializeFileuploader = () => {
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-chat-file-uploader',
        maxFileSize: BpheroConfig.ELECTRONIC_CONTRACT_MAX_FILE_SIZE,
        acceptedFileTypes: ['application/pdf'],
        allowMultiple: false,
        allowReplace: true,
        chunkUploads: true,
        instantUpload: true,
        allowReorder: false,
        allowProcess: false,
        labelIdle: '',
      });
    };

    /**
     * Attach file uploader update file event listener
     */
    const attachUpdateFilesListener = () => {
      fileUploader.value.pond.on('addfile', () => {
        file.value = {};
        delete formData.value.selected_document_id;
        delete formData.value.local_file;

        hasUploadFile.value = true;
        isDocumentSelected.value = true;
      });

      fileUploader.value.pond.on('removefile', () => {
        // Remove local file info
        delete formData.value.local_file;
        hasUploadFile.value = false;

        // If no selected document id exists, hide display
        if (!formData.value.selected_document_id) {
          isDocumentSelected.value = false;
        }
      });
    };

    /**
     * Attach file uploader process file event listener
     */
    const attachProcessFileListener = () => {
      fileUploader.value.pond.on('processfile', (error, item) => {
        if (!error) {
          formData.value.local_file = item.serverId;
        }
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
     * Mounted properties
     */
    onMounted(() => {
      initializeFileuploader();
      attachUpdateFilesListener();
      attachUploadWarningListener();
      attachProcessFileListener();
    });

    return {
      availableSlot,
      formData,
      errors,
      setErrors,
      alert,
      setAlert,
      resetAlert,
      hasUploadFile,
      openFileBrowser,
      documentListModalRef,
      connectionListModalRef,
      openDocumentList,
      openConnectionList,
      DocumentListType,
      sendDocumentType,
      handleChooseDocumentFile,
      handleSelectedRecipient,
      recipient,
      deleteSelectedFile,
      deleteSelectedRecipient,
      manualEmailRegisterModalRef,
      recipientEmailList,
      isSubmitButtonDisabled,
      isDocumentSelected,
      isConnectedRecipientSelected,
      openManualEmailRegisterModal,
      handleManualRecipientRegister,
      isManualRecipientSelected,
      file,
      resetFormData,
      isAccessible,
      updateSlots,
    };
  },
};
</script>

<style scoped>
.badge-size {
  font-size: 18px;
  margin-left: 10px;
  margin-right: 10px;
}

.btn-primary.disabled,
.btn-primary:disabled {
  background-color: #9e9ea4 !important;
  border-color: #9e9ea4 !important;
}

.btn-outline-secondary:disabled {
  color: #5a5b75 !important;
}
</style>
