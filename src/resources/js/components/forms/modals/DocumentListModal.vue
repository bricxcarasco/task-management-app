<template>
  <div>
    <div
      class="modal fade"
      ref="modalRef"
      id="form-document-linkage"
      tabindex="-1"
      role="dialog"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <section-loader :show="modalLoading" />
          <form
            action=""
            @submit.prevent="handleUploadPdfForm"
            ref="formRef"
            novalidate
          >
            <div class="modal-header">
              <h4 class="modal-title">
                {{
                  $t('headers.personal_document_management', {
                    name: serviceName,
                  })
                }}
              </h4>
              <button
                class="btn-close"
                type="button"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="resetModal"
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
                <ul
                  class="list-group list-group-flush"
                  v-if="folders.length !== 0"
                >
                  <document-list-modal-item
                    v-for="(folder, index) in folders"
                    :key="`${folder.document_id}${index}`"
                    :document="folder"
                    :category="`folder`"
                    @open-folder="handleNextFolderContent"
                  />
                </ul>
                <p class="text-center p-2 mb-0" v-else>
                  {{ $t('paragraphs.there_are_no_folders') }}
                </p>
              </div>
              <p class="text-center p-2 mb-0" v-else>
                {{ $t('paragraphs.there_are_no_files') }}
              </p>
              <div class="text-center mt-4 py-2">
                <button
                  :disabled="disableSubmitButton"
                  type="submit"
                  class="btn btn-primary"
                >
                  {{ $t('buttons.upload_file_here') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import i18n from '../../../i18n';
import DocumentListModalItem from './DocumentListModalItem.vue';
import DefaultListApiService from '../../../api/document_management/default-list';
import FormsApiService from '../../../api/forms/forms';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import StorageTypes from '../../../enums/StorageTypes';

export default {
  name: 'DocumentListModal',
  components: {
    DocumentListModalItem,
    SectionLoader,
  },
  props: {
    service: {
      type: [Object, null],
      required: true,
    },

    form: {
      type: [Object, null],
      required: true,
    },
  },
  setup(props, { emit }) {
    const formsApiService = new FormsApiService();
    const personalDocumentsApi = new DefaultListApiService();
    const modalRef = ref(null);
    const modalLoading = ref(false);
    const documents = ref({});
    const errors = ref(null);

    const service = ref(props.service);
    const currentDocument = ref([]);
    const folderCount = ref(0);
    const folders = ref({});
    const fileCount = ref(0);
    const files = ref({});
    const formData = ref({});
    const disableSubmitButton = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show select document modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide select document modal
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
    const setCurrentDocument = (folder) => {
      currentDocument.value.push(folder);
    };

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case 'RIO':
          return data.full_name;
        case 'NEO':
          return data.organization_name;
        default:
          return `-`;
      }
    });

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
     * Get personal document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const getPersonalDocumentsFolder = async (folder) => {
      setLoading(true);
      formData.value.directory_id = folder.document_id;

      await personalDocumentsApi
        .getPersonalDocumentsFolder(folder.document_id)
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
      if (currentDocument.value.length === 0) {
        formData.value.directory_id = null;
        getPersonalDocuments();
      } else {
        getPersonalDocumentsFolder(
          currentDocument.value[currentDocument.value.length - 1]
        );
      }
    };

    /**
     * Get shared document list based on document id
     *
     * @param {int} id
     * @returns {void}
     */
    const handleNextFolderContent = (folder) => {
      setCurrentDocument(folder);
      getPersonalDocumentsFolder(folder);
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
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const handleUploadPdfForm = async () => {
      // Reinitialize state
      errors.value = null;
      emit('reset-alert');

      // Prepare initial data
      formData.value.storage_type_id = StorageTypes.HERO;
      formData.value.form_id = props.form.id;

      try {
        // Prepare for final upload process
        modalLoading.value = true;
        disableSubmitButton.value = true;
        // Handle responses
        formsApiService
          .saveFormPdfFile(formData.value)
          .then(() => {
            resetModal();
            setAlert(
              'success',
              i18n.global.t(
                'messages.document_management.sent_to_document_management'
              )
            );
            disableSubmitButton.value = false;
          })
          .catch(() => {
            resetModal();
            setAlert(
              'failed',
              i18n.global.t('messages.document_management.file_upload_failed')
            );
            disableSubmitButton.value = false;
          })
          .finally(() => {
            modalLoading.value = false;
          });
      } catch (error) {
        modalLoading.value = false;
        throw error;
      }
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
        getPersonalDocuments();
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
      disableSubmitButton,
      documents,
      currentDocument,
      handleSelectedFile,
      handleNextFolderContent,
      handlePreviousDocument,
      handleUploadPdfForm,
      folderCount,
      fileCount,
      folderCounter,
      folders,
      fileCounter,
      files,
      formData,
      serviceName,
    };
  },
};
</script>
