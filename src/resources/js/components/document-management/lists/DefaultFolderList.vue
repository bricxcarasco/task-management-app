<template>
  <div>
    <!-- rename Modal -->
    <rename-folder-modal
      @reset-folder-list="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="$emit('reset-alert')"
    />
    <delete-folder-modal
      @reset-folder-list="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="$emit('reset-alert')"
    />
    <!-- Share Setting Modal -->
    <share-setting-modal
      @share-setting="getPersonalDocument"
      @set-alert="setAlert"
      @reset-alert="$emit('reset-alert')"
    />
    <div class="card" id="connection-requests-list-items">
      <section-loader :show="listLoading" />
      <div class="connection__wrapper">
        <ul
          v-if="personalDocuments.length > 0"
          class="list-group list-group-flush"
        >
          <default-document-item
            v-for="(request, index) in personalDocuments"
            :key="`${request.id}${index}`"
            :request="request"
            :currentService="currentService"
            :parent="`default-folders`"
          />
        </ul>
        <div v-else class="d-flex justify-content-center mt-3 pb-3">
          {{ $t('paragraphs.there_are_no_folders') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import DefaultDocumentItem from './DefaultDocumentItem.vue';
import DefaultListApiService from '../../../api/document_management/default-list';
import RenameFolderModal from '../modals/ChangeFolderNameModal.vue';
import DeleteFolderModal from '../modals/DeleteFolderModal.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ShareSettingModal from '../share-setting/ShareSettingModal.vue';
import DocumentType from '../../../enums/DocumentType';

export default {
  name: 'DefaultFolderList',
  props: {
    service: {
      type: [Object, null],
      required: true,
    },
    request_data: {
      type: Object,
    },
    id: {
      type: Number,
      default: null,
    },
    document_type: {
      type: Number,
      default: DocumentType.FOLDER,
    },
  },
  components: {
    RenameFolderModal,
    DeleteFolderModal,
    DefaultDocumentItem,
    SectionLoader,
    ShareSettingModal,
  },
  setup(props, { emit }) {
    const defaultListApi = new DefaultListApiService();
    const personalDocuments = ref([]);
    const listLoading = ref(false);
    const requestData = ref(props.request_data ?? {});
    const currentService = ref(props.service);
    const directoryId = ref(props.id);
    const currentUrl = ref(window.location.pathname);

    /**
     * Handle set alert emit
     *
     * @returns {void}
     */
    const setAlert = (status, message) => {
      emit('set-alert', status, message);
    };

    /**
     * Get list of personal folders
     *
     * @returns {void}
     */
    const getPersonalDocument = async () => {
      listLoading.value = true;

      try {
        requestData.value.document_type = props.document_type;

        const getDocumentsApi =
          currentUrl.value === '/document'
            ? await defaultListApi.getPersonalDocuments(requestData.value)
            : await defaultListApi.getPersonalDocumentsFolder(
                directoryId.value,
                requestData.value
              );
        const documentResponseData = getDocumentsApi.data;

        personalDocuments.value = documentResponseData?.data.result || [];
      } catch (error) {
        emit('reset-alert');
        emit('set-alert', 'failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    getPersonalDocument();

    return {
      setAlert,
      listLoading,
      personalDocuments,
      requestData,
      currentService,
      getPersonalDocument,
    };
  },
};
</script>
