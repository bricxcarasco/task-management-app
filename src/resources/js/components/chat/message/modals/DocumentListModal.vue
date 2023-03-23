<template>
  <div
    class="modal fade"
    id="sendDocumentList"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('links.sharing_settings') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            @click="hide"
          ></button>
        </div>
        <div class="modal-body">
          <i
            v-if="currentDocument.length !== 0"
            class="p-2 ai-arrow-left hoverable"
            @click="handlePreviousDocument"
          ></i>
          <div v-if="folders">
            <p class="bg-dark c-white p-2 mb-0">
              {{ $t('labels.folder') }}
            </p>
            <ul class="list-group list-group-flush" v-if="folders.length !== 0">
              <document-list-modal-item
                v-for="(folder, index) in folders"
                :key="`${folder.document_id}${index}`"
                :document="folder"
                :category="`folder`"
                :talk_subject="talk_subject"
                @open-folder="handleNextFolderContent"
              />
            </ul>
            <p class="text-center p-2 mb-0" v-else>
              {{ $t('paragraphs.there_are_no_folders') }}
            </p>
          </div>
          <div v-if="files">
            <p class="bg-dark c-white p-2 mb-0 mt-2">
              {{ $t('labels.file') }}
            </p>
            <ul class="list-group list-group-flush" v-if="files.length !== 0">
              <document-list-modal-item
                v-for="(file, index) in files"
                :key="`${file.document_id}${index}`"
                :document="file"
                :category="`file`"
                :talk_subject="talk_subject"
                @select-file="handleSelectedFile"
              />
            </ul>
            <p class="text-center p-2 mb-0" v-else>
              {{ $t('paragraphs.there_are_no_files') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import DocumentListModalItem from './DocumentListModalItem.vue';
import DefaultListApiService from '../../../../api/document_management/default-list';
import SharedListApiService from '../../../../api/document_management/shared-list';
import ChatDocumentAttachmentType from '../../../../enums/ChatDocumentAttachmentType';
import ServiceSelectionTypesEnum from '../../../../enums/ServiceSelectionTypes';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  name: 'DocumentListModal',
  components: {
    DocumentListModalItem,
    SectionLoader,
  },
  props: {
    document_type: {
      type: [Number, String],
      required: true,
    },
    talk_subject: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props, { emit }) {
    const personalDocumentsApi = new DefaultListApiService();
    const sharedDocumentsApi = new SharedListApiService();
    const modalRef = ref(null);
    const modalLoading = ref(false);
    const documents = ref({});

    const currentDocument = ref([]);
    const folderCount = ref(0);
    const folders = ref({});
    const fileCount = ref(0);
    const files = ref({});

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show cancel conection modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide cancel conection modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
      currentDocument.value.length = 0;
    };

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * Set current document value
     *
     * @returns {void}
     */
    const setCurrentDocument = (id) => {
      currentDocument.value.push(id);
    };

    /**
     * Get personal document list
     *
     * @returns {void}
     */
    const getPersonalDocuments = async () => {
      setLoading(true);
      await personalDocumentsApi
        .getPersonalDocuments()
        .then((response) => {
          documents.value = response.data.data;
          folders.value = documents.value.folder_result;
          files.value = documents.value.file_result;
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
        })
        .finally(() => setLoading(false));
    };

    /**
     * Get shared document list
     *
     * @returns {void}
     */
    const getSharedDocuments = async () => {
      setLoading(true);
      await sharedDocumentsApi
        .getSharedDocuments()
        .then((response) => {
          documents.value = response.data.data;
          folders.value = documents.value.folder_result;
          files.value = documents.value.file_result;
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
        })
        .finally(() => setLoading(false));
    };

    /**
     * Get personal document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const getPersonalDocumentsFolder = async (id) => {
      setLoading(true);
      await personalDocumentsApi
        .getPersonalDocumentsFolder(id)
        .then((response) => {
          documents.value = response.data.data;
          folders.value = documents.value.folder_result;
          files.value = documents.value.file_result;
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
          hide();
        })
        .finally(() => setLoading(false));
    };

    /**
     * Get shared document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const getSharedDocumentsFolder = async (id) => {
      setLoading(true);
      await sharedDocumentsApi
        .getSharedDocumentsFolder(id)
        .then((response) => {
          documents.value = response.data.data;
          folders.value = documents.value.folder_result;
          files.value = documents.value.file_result;
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
          hide();
        })
        .finally(() => setLoading(false));
    };

    /**
     * Go back to previous document
     *
     * @returns {void}
     */
    const handlePreviousDocument = () => {
      currentDocument.value.pop();
      if (props.document_type === ChatDocumentAttachmentType.PERSONAL) {
        if (currentDocument.value.length === 0) {
          getPersonalDocuments();
        } else {
          getPersonalDocumentsFolder(
            currentDocument.value[currentDocument.value.length - 1]
          );
        }
      } else if (props.document_type === ChatDocumentAttachmentType.SHARED) {
        if (currentDocument.value.length === 0) {
          getSharedDocuments();
        } else {
          getSharedDocumentsFolder(
            currentDocument.value[currentDocument.value.length - 1]
          );
        }
      }
    };

    /**
     * Get shared document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const handleNextFolderContent = (id) => {
      setCurrentDocument(id);
      if (props.document_type === ChatDocumentAttachmentType.PERSONAL) {
        getPersonalDocumentsFolder(id);
      } else {
        getSharedDocumentsFolder(id);
      }
    };

    /**
     * Trigger set document file and emit to parent component
     *
     * @param {Object} file
     * @returns {void}
     */
    const handleSelectedFile = (file) => {
      setLoading(true);
      emit('choose-document-file', file);
    };

    /**
     * Counter for folders that will appear on the list
     *
     * @returns {void}
     */
    const folderCounter = () => {
      folderCount.value += 1;
    };

    /**
     * Counter for files that will appear on the list
     *
     * @returns {void}
     */
    const fileCounter = () => {
      fileCount.value += 1;
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        documents.value = {};
        folders.value = {};
        files.value = {};
        setLoading(false);
      });
      modalRef.value.addEventListener('shown.bs.modal', () => {
        if (props.document_type === ChatDocumentAttachmentType.PERSONAL) {
          getPersonalDocuments();
        } else {
          getSharedDocuments();
        }
      });
    });

    return {
      ServiceSelectionTypesEnum,
      modalRef,
      modal,
      modalLoading,
      show,
      hide,
      setLoading,
      getPersonalDocuments,
      getSharedDocuments,
      documents,
      currentDocument,
      handleSelectedFile,
      handleNextFolderContent,
      handlePreviousDocument,
      folderCount,
      fileCount,
      folderCounter,
      folders,
      fileCounter,
      files,
    };
  },
};
</script>
