<template>
  <div>
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
            :parent="`default-files`"
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
import DefaultDocumentItem from './DefaultDocumentItem.vue';
import DefaultListApiService from '../../../api/document_management/default-list';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import DocumentType from '../../../enums/DocumentType';

export default {
  name: 'DefaultFileList',
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
    DefaultDocumentItem,
    SectionLoader,
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
     * Get list of personal files
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
      listLoading,
      personalDocuments,
      requestData,
      currentService,
      getPersonalDocument,
    };
  },
};
</script>
