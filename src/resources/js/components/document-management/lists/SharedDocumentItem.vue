<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <div class="document-management-file-name">
      <div v-if="parent == 'shared-files'">
        <div v-if="request.file_type.split('/')[0] === 'image'">
          <div @click.stop="accessFile(request)">
            <i class="h2 m-0 ai-image"></i>
            <span class="fs-xs c-primary ms-2 document-list-file-name">
              {{ request.name }}
            </span>
          </div>
        </div>
        <div v-else-if="request.file_type.split('/')[1] === 'pdf'">
          <div @click.stop="accessFile(request)">
            <i class="h2 m-0 ai-file-text"></i>
            <span class="fs-xs c-primary ms-2 document-list-file-name">
              {{ request.name }}
            </span>
          </div>
        </div>
        <div v-else>
          <div @click="downloadFile()">
            <i
              v-if="request.file_type.search('zip') === -1"
              class="h2 m-0 ai-file-text"
            ></i>
            <i v-else class="h2 m-0 ai-file"></i>
            <span class="fs-xs c-primary ms-2 document-list-file-name">
              {{ request.name }}
            </span>
          </div>
        </div>
      </div>
      <div v-else>
        <div @click.stop="accessFolder(request)">
          <i class="h2 m-0 ai-folder"></i>
          <span class="fs-xs c-primary ms-2 document-list-file-name">
            {{ request.name }}
          </span>
        </div>
      </div>
    </div>
    <div class="vertical-right hasDropdown dropstart">
      <!-- File menu modal -->
      <button
        type="button"
        class="btn btn-link px-3"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <i class="color-primary ai-more-vertical"></i>
      </button>
      <div class="dropdown-menu">
        <a
          class="dropdown-item"
          @click.prevent="getSharedLink(request.document_id)"
        >
          {{ $t('links.get_shared_link') }}
        </a>
        <div
          v-if="
            (request.owner_rio_id === currentService.data.id &&
              currentService.type === 'RIO') ||
            (request.owner_neo_id === currentService.data.id &&
              currentService.type === 'NEO' &&
              !currentService.data.is_member)
          "
        >
          <a class="dropdown-item" @click.prevent="shareSetting(request)">
            {{ $t('links.sharing_settings') }}
          </a>
          <div v-if="request.document_type == documentType.FOLDER">
            <a
              class="dropdown-item"
              @click.prevent="deleteTargetFolderDocument(request.document_id)"
            >
              {{ $t('buttons.delete') }}
            </a>
            <a
              class="dropdown-item"
              @click.prevent="renameTargetFolderDocument(request)"
            >
              {{ $t('links.change_name') }}
            </a>
          </div>
          <div v-else>
            <a
              class="dropdown-item"
              @click.prevent="deleteTargetFileDocument(request.document_id)"
            >
              {{ $t('buttons.delete') }}
            </a>
            <a
              class="dropdown-item"
              @click.prevent="renameTargetFileDocument(request)"
            >
              {{ $t('links.change_name') }}
            </a>
          </div>
        </div>
        <div v-if="request.document_type == documentType.FOLDER">
          <button
            class="dropdown-item"
            @click="downloadFile()"
            :disabled="isFolderHaveFile"
          >
            {{ $t('links.download') }}
          </button>
        </div>
        <div v-else>
          <a class="dropdown-item" @click="downloadFile()">
            {{ $t('links.download') }}
          </a>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </li>
</template>

<script>
import { ref, computed } from 'vue';
import Common from '../../../common';
import DocumentType from '../../../enums/DocumentType';
import DocumentApiService from '../../../api/document_management/document-option';
import i18n from '../../../i18n';

export default {
  name: 'DefaultDocumentItem',
  props: {
    request: {
      type: [Object, null],
      required: true,
    },
    currentService: {
      type: [Object, null],
      required: true,
    },
    parent: {
      type: [String, null],
      required: true,
    },
  },
  setup(props, { emit }) {
    const documentApi = new DocumentApiService();
    const isFolderHaveFile = ref(false);
    const documentType = DocumentType;
    const targetDocument = ref(null);
    const url = ref(null);
    const generatedSharedLink = ref(null);

    /* eslint no-undef: 0 */
    const renameFolderModalNode = document.getElementById(
      'rename-folder-modal'
    );
    const documentRenameFolderModal = computed(
      () => new bootstrap.Modal(renameFolderModalNode)
    );
    const renameFileModalNode = document.getElementById('rename-file-modal');
    const documentRenameFileModal = computed(
      () => new bootstrap.Modal(renameFileModalNode)
    );
    const deleteFolderModalNode = document.getElementById(
      'delete-folder-modal'
    );
    const documentDeleteFolderModal = computed(
      () => new bootstrap.Modal(deleteFolderModalNode)
    );
    const deleteFileModalNode = document.getElementById('delete-file-modal');
    const documentDeleteFileModal = computed(
      () => new bootstrap.Modal(deleteFileModalNode)
    );
    const shareSettingModalNode = document.getElementById(
      'share-setting-modal'
    );
    const shareSettingModal = computed(
      () => new bootstrap.Modal(shareSettingModalNode)
    );
    // Initialize shared link modal component
    const sharedLinkModalNode = document.getElementById('shared-link-modal');
    const sharedLinkModal = computed(
      () => new bootstrap.Modal(sharedLinkModalNode)
    );

    /**
     * Access a file
     *
     * @param {object} request
     * @returns {void}
     */
    const accessFile = (request) => {
      url.value = '/document/shared/files/:id';
      url.value = url.value.replace(':id', request.document_id);
      window.open(url.value, '_blank');
    };

    /**
     * Access a folder
     *
     * @param {object} request
     * @returns {void}
     */
    const accessFolder = (request) => {
      url.value = '/document/shared/folders/:id';
      url.value = url.value.replace(':id', request.document_id);
      window.open(url.value, '_self');
    };

    /**
     * Download a file/folder
     *
     * @returns {void}
     */
    const downloadFile = () => {
      url.value = '/api/document/download/:id';
      url.value = url.value.replace(':id', props.request.document_id);
      window.open(url.value, '_blank');
    };

    /**
     * Inject record and open modal (Shared Link) when both copy-to-clipboard function fails
     *
     * @param {int} id
     * @returns {void}
     */
    const copySharedLink = () => {
      const form = sharedLinkModalNode.querySelector('form');
      const formValues = {
        sharedLink: generatedSharedLink.value,
      };
      Common.fillForm(form, formValues);
      sharedLinkModal.value.show();
    };

    /**
     * Handle copy-to-clipboart fallback
     *
     * @param {string} text
     * @returns {void}
     */
    const copyToClipboardFallback = (text) => {
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();

      // Call document.execCommand('copy') to fetch text from created element
      try {
        document.execCommand('copy');
        emit(
          'set-alert',
          'success',
          i18n.global.t('messages.document_management.copied_shared_link')
        );
      } catch {
        emit(
          'set-alert',
          'failed',
          i18n.global.t('messages.document_management.failed_to_copy')
        );

        // Open shared link modal to copy link manually
        generatedSharedLink.value = text;
        copySharedLink();
      } finally {
        document.body.removeChild(textArea);
      }
    };

    /**
     * Fetch shared link
     *
     * @returns {void}
     */
    const getSharedLink = async (id) => {
      await documentApi
        .sharedLink(id)
        .then((response) => {
          if (response.data.status_code === 200) {
            const text = response.data.data;

            // Execute navigator.clipboard API
            try {
              navigator.clipboard.writeText(text).then(() => {
                emit(
                  'set-alert',
                  'success',
                  i18n.global.t(
                    'messages.document_management.copied_shared_link'
                  )
                );
              });
            } catch {
              // Execute document.execCommand('copy')
              copyToClipboardFallback(text);
            }
          }
        })
        .catch(() => {
          emit(
            'set-alert',
            'failed',
            i18n.global.t('messages.document_management.failed_to_generate')
          );
        });
    };

    /**
     * Inject record and open modal
     *
     * @returns {void}
     */
    const renameTargetFileDocument = () => {
      const form = renameFileModalNode.querySelector('form');
      const formValues = props.request;
      formValues.temp_name = formValues.name;
      if (formValues.document_type !== DocumentType.FOLDER) {
        const fileName = formValues.temp_name;
        const tempRev = fileName.split('').reverse().join('');
        const fileSplit = tempRev.split('.').slice(1).join('.');
        formValues.temp_name = fileSplit.split('').reverse().join('');
      }
      Common.fillForm(form, formValues);
      targetDocument.value = formValues;
      documentRenameFileModal.value.show();
    };

    /**
     * Inject record and open modal
     *
     * @returns {void}
     */
    const renameTargetFolderDocument = () => {
      const form = renameFolderModalNode.querySelector('form');
      const formValues = props.request;
      formValues.temp_name = formValues.name;
      Common.fillForm(form, formValues);
      targetDocument.value = formValues;
      documentRenameFolderModal.value.show();
    };

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const deleteTargetFileDocument = (id) => {
      // Inject record id to modal
      const field = deleteFileModalNode.querySelector('input[name=id]');
      field.value = id;
      documentDeleteFileModal.value.show();
    };

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const deleteTargetFolderDocument = (id) => {
      // Inject record id to modal
      const field = deleteFolderModalNode.querySelector('input[name=id]');
      field.value = id;
      documentDeleteFolderModal.value.show();
    };

    /**
     * Get list of personal folders
     *
     * @returns {void}
     */
    const setDownloadOption = async () => {
      try {
        const getDocumentsApi = await documentApi.checkFolderContent(
          props.request.document_id
        );
        isFolderHaveFile.value = getDocumentsApi.data !== 1;
      } catch (error) {
        resetAlert();
        setAlert('failed');
        throw error;
      }
    };

    /**
     * Inject record and open modal (Share Setting)
     *
     * @param {int} id
     * @returns {void}
     */
    const shareSetting = () => {
      const form = shareSettingModalNode.querySelector('form');
      const formValues = props.request;
      Common.fillForm(form, formValues);
      shareSettingModal.value.show();
    };

    setDownloadOption();

    return {
      accessFile,
      accessFolder,
      downloadFile,
      renameTargetFileDocument,
      renameTargetFolderDocument,
      deleteTargetFileDocument,
      deleteTargetFolderDocument,
      targetDocument,
      documentType,
      setDownloadOption,
      isFolderHaveFile,
      shareSetting,
      getSharedLink,
      copySharedLink,
    };
  },
};
</script>
