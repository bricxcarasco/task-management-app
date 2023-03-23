<template>
  <div>
    <!-- Page Loader -->
    <section-loader :show="listLoading" />

    <!-- Alert Message -->
    <alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Create Actions -->
    <create-action-section
      :directory_id="directory_id"
      :service="service"
      @reset-list="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Rename Modal -->
    <rename-file-modal
      @reset-file-list="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Delete Modal -->
    <delete-file-modal
      @reset-file-list="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Share Setting Modal -->
    <share-setting-modal
      @share-setting="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Personal Folders -->
    <p class="bg-dark c-white p-2 mb-0">{{ $t('labels.folder') }}</p>
    <div class="tab-content">
      <default-folder-list
        ref="defaultFolderListRef"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.FOLDER"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      />
    </div>

    <!-- Personal Files -->
    <p class="bg-dark c-white p-2 mb-0 mt-4">{{ $t('labels.file') }}</p>
    <div class="tab-content">
      <default-file-list
        ref="defaultFileListRef"
        :key="'file_list.' + documentTypeEnum.FILE"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.FILE"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      />
    </div>

    <!-- Personal Attachments -->
    <p class="bg-dark c-white p-2 mb-0 mt-4" v-if="isRoot">
      {{ $t('labels.chat') }}
    </p>
    <div class="tab-content" v-if="isRoot">
      <default-file-list
        ref="defaultAttachmentListRef"
        :key="'file_list.' + documentTypeEnum.ATTACHMENT"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.ATTACHMENT"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      />
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import Alert from '../base/BaseAlert.vue';
import DefaultFolderList from './lists/DefaultFolderList.vue';
import DefaultFileList from './lists/DefaultFileList.vue';
import CreateActionSection from './components/CreateActionSection.vue';
import RenameFileModal from './modals/ChangeFileNameModal.vue';
import DeleteFileModal from './modals/DeleteFileModal.vue';
import SectionLoader from '../base/BaseSectionLoader.vue';
import ShareSettingModal from './share-setting/ShareSettingModal.vue';
import DocumentType from '../../enums/DocumentType';

export default {
  name: 'DefaultSection',
  props: {
    service: {
      type: [Object, null],
      required: true,
    },
    request_data: {
      type: Object,
    },
    directory_id: {
      type: Number,
      default: null,
    },
  },
  components: {
    Alert,
    CreateActionSection,
    DefaultFolderList,
    DefaultFileList,
    SectionLoader,
    RenameFileModal,
    DeleteFileModal,
    ShareSettingModal,
  },
  setup(props) {
    const documentTypeEnum = DocumentType;
    const defaultFolderListRef = ref(null);
    const defaultFileListRef = ref(null);
    const defaultAttachmentListRef = ref(null);
    const listLoading = ref(false);
    const isRoot = props.directory_id === null;
    const alert = ref({
      success: false,
      failed: false,
    });

    /**
     * Reset Alert
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
     * Get list of personal files and folders
     *
     * @returns {void}
     */
    const getPersonalDocument = () => {
      defaultFolderListRef.value.getPersonalDocument();
      defaultFileListRef.value.getPersonalDocument();

      if (defaultAttachmentListRef.value !== null) {
        defaultAttachmentListRef.value.getPersonalDocument();
      }
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

    return {
      alert,
      listLoading,
      resetAlert,
      setAlert,
      defaultFolderListRef,
      defaultFileListRef,
      defaultAttachmentListRef,
      getPersonalDocument,
      documentTypeEnum,
      isRoot,
    };
  },
};
</script>
