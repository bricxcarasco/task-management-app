<template>
  <div>
    <div class="card" id="connection-requests-list-items">
      <section-loader :show="listLoading" />
      <div class="connection__wrapper">
        <ul
          v-if="sharedDocuments.length > 0"
          class="list-group list-group-flush"
        >
          <shared-document-item
            v-for="(request, index) in sharedDocuments"
            :key="`${request.id}${index}`"
            :request="request"
            :currentService="currentService"
            :parent="`shared-files`"
            @set-alert="setAlert"
          />
        </ul>
        <div v-else class="d-flex justify-content-center mt-3 pb-3">
          {{ $t('paragraphs.there_are_no_files') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import SharedDocumentItem from './SharedDocumentItem.vue';
import SharedListApiService from '../../../api/document_management/shared-list';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import DocumentType from '../../../enums/DocumentType';

export default {
  name: 'SharedFileList',
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
      default: DocumentType.FILE,
    },
  },
  components: {
    SharedDocumentItem,
    SectionLoader,
  },
  setup(props, { emit }) {
    const sharedListApi = new SharedListApiService();
    const sharedDocuments = ref([]);
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
     * Get list of shared files and folders to/from user
     *
     * @returns {void}
     */
    const getSharedDocument = async () => {
      listLoading.value = true;

      try {
        requestData.value.document_type = props.document_type;

        const getDocumentsApi =
          currentUrl.value === '/document/shared'
            ? await sharedListApi.getSharedDocuments(requestData.value)
            : await sharedListApi.getSharedDocumentsFolder(
                directoryId.value,
                requestData.value
              );
        const documentResponseData = getDocumentsApi.data;

        sharedDocuments.value = documentResponseData?.data.result || [];
      } catch (error) {
        emit('reset-alert');
        emit('set-alert', 'failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    getSharedDocument();

    return {
      listLoading,
      sharedDocuments,
      requestData,
      currentService,
      getSharedDocument,
      setAlert,
    };
  },
};
</script>
