<template>
  <div class="mb-0 mt-4">
    <!-- Page Loader -->
    <section-loader :show="listLoading" />

    <!-- Alert Message -->
    <alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Rename Modal -->
    <rename-file-modal
      @reset-file-list="getSharedDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Delete Modal -->
    <delete-file-modal
      @reset-file-list="getSharedDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Shared Link Modal -->
    <shared-link-modal @set-alert="setAlert" @reset-alert="resetAlert" />

    <!-- Share Setting Modal -->
    <share-setting-modal
      @share-setting="getSharedDocument"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <p class="bg-dark c-white p-2 mb-0">{{ $t('labels.folder') }}</p>
    <div class="tab-content">
      <shared-folder-list
        ref="sharedFolderListRef"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.FOLDER"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      >
      </shared-folder-list>
    </div>
    <p class="bg-dark c-white p-2 mb-0 mt-4">{{ $t('labels.file') }}</p>
    <div class="tab-content">
      <shared-file-list
        ref="sharedFileListRef"
        :key="'file_list.' + documentTypeEnum.FILE"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.FILE"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      >
      </shared-file-list>
    </div>
    <p class="bg-dark c-white p-2 mb-0 mt-4" v-if="isRoot">
      {{ $t('labels.chat') }}
    </p>
    <div class="tab-content" v-if="isRoot">
      <shared-file-list
        ref="sharedAttachmentListRef"
        :key="'file_list.' + documentTypeEnum.ATTACHMENT"
        :service="service"
        :request_data="request_data"
        :id="directory_id"
        :document_type="documentTypeEnum.ATTACHMENT"
        @set-alert="setAlert"
        @reset-alert="resetAlert"
      >
      </shared-file-list>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import Alert from '../base/BaseAlert.vue';
import SharedFolderList from './lists/SharedFolderList.vue';
import SharedFileList from './lists/SharedFileList.vue';
import RenameFileModal from './modals/ChangeFileNameModal.vue';
import DeleteFileModal from './modals/DeleteFileModal.vue';
import SectionLoader from '../base/BaseSectionLoader.vue';
import ShareSettingModal from './share-setting/ShareSettingModal.vue';
import DocumentType from '../../enums/DocumentType';
import SharedLinkModal from './modals/SharedLinkModal.vue';

export default {
  name: 'SharedSection',
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
    SharedFolderList,
    SharedFileList,
    SectionLoader,
    RenameFileModal,
    DeleteFileModal,
    ShareSettingModal,
    SharedLinkModal,
  },
  setup(props) {
    const documentTypeEnum = DocumentType;
    const sharedFolderListRef = ref(null);
    const sharedFileListRef = ref(null);
    const sharedAttachmentListRef = ref(null);
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
    const getSharedDocument = () => {
      sharedFolderListRef.value.getSharedDocument();
      sharedFileListRef.value.getSharedDocument();

      if (sharedAttachmentListRef.value !== null) {
        sharedAttachmentListRef.value.getSharedDocument();
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
      sharedFolderListRef,
      sharedFileListRef,
      sharedAttachmentListRef,
      getSharedDocument,
      documentTypeEnum,
      isRoot,
    };
  },
};
</script>
