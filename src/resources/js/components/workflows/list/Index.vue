<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Create Workfow Modal -->
    <create-workflow-modal
      :file_names="uploadedFileNames"
      ref="createWorkflowModalRef"
      :service="service"
      :file_attaches="attachesFile"
      @open-attachment="openAttachmentMenuModal"
      @save-success="workflowSaved"
      @remove-uploaded-files="attachRemoveFilesListener"
      @remove-selected-file="removeSelectedFile"
      @clear-attaches="clearAttaches"
      @update-list="getCreatedWorkflows"
    />

    <attachment-menu-modal
      ref="attachmentMenuModalRef"
      @open-document-attachment="openDocumentAttachment"
      @open-file-browser="openFileBrowser"
      @remove-selected-file="removeSelectedFile"
    />

    <!-- Document List Modal -->
    <document-list-modal
      ref="documentListModalRef"
      :document_type="sendDocumentType"
      :service="service"
      @set-alert="setAlert"
      @choose-document-file="handleChooseDocumentFile"
    />

    <div
      class="container position-relative pb-4 mb-md-3 home pt-6 home--height"
    >
      <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
          <!-- Create worflow button -->
          <div
            class="d-flex align-items-center justify-content-end mb-0 mb-md-2"
          >
            <div class="mb-md-2">
              <button
                type="button"
                class="btn btn-link"
                @click="openCreateWorkflowModal"
              >
                <i class="ai-plus me-2"></i>
                {{ $t('messages.workflows.create_a_workflow') }}
              </button>
            </div>
          </div>

          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a
                href="#workFlowCreated"
                class="nav-link active"
                data-bs-toggle="tab"
                role="tab"
                @click="getCreatedWorkflows"
              >
                {{ $t('buttons.workflow_created') }}
                <span v-if="countReturned >= 1" class="badge bg-danger"
                  >{{ countReturned }}
                </span>
                <span v-else class="badge bg-danger"></span>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="#workFlowforYou"
                class="nav-link"
                data-bs-toggle="tab"
                role="tab"
                @click="getWorkflowForYou"
              >
                {{ $t('buttons.workflow_for_you') }}
                <span v-if="countPending >= 1" class="badge bg-danger"
                  >{{ countPending }}
                </span>
                <span v-else class="badge bg-danger"></span>
              </a>
            </li>
          </ul>
          <!-- Workflow List -->
          <ItemList
            ref="workflowList"
            @badge-count="getCount"
            @pending-count="getPendingCount"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import BaseAlert from '../../base/BaseAlert.vue';
import ItemList from './List.vue';
import CreateWorkflowModal from '../modals/CreateWorkflowModal.vue';
import AttachmentMenuModal from '../components/AttachmentMenuModal.vue';
import i18n from '../../../i18n';
import BpheroConfig from '../../../config/bphero';
import DocumentListModal from '../components/DocumentListModal.vue';
import ReactionTypes from '../../../enums/WorkflowsReactionTypes';
import StatusTypes from '../../../enums/WorkflowStatusTypes';

export default defineComponent({
  name: 'WorkflowListIndex',
  components: {
    BaseAlert,
    ItemList,
    CreateWorkflowModal,
    AttachmentMenuModal,
    DocumentListModal,
  },
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props) {
    const countReturned = ref();
    const countPending = ref();
    const attachmentMenuModalRef = ref(null);
    const workflowList = ref(null);
    const createWorkflowModalRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });
    const fileUploader = ref({});
    const uploadedFileNames = ref([]);

    const sendDocumentType = ref(0);
    const documentListModalRef = ref(null);
    const attachesFile = ref([]);

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
     * Get workflow list
     *
     * @returns {void}
     */
    const getCreatedWorkflows = () => {
      workflowList.value.getCreatedWorkflows();
    };

    /**
     * Get workflow list for you
     *
     * @returns {void}
     */
    const getWorkflowForYou = () => {
      workflowList.value.getWorkflowForYou();
    };

    /**
     * Count returned status in created workflow tab
     */
    const getCount = (value) => {
      countReturned.value = value;
    };

    /**
     * Filter action get current approver with status 1
     *
     * @param {object} actions
     * @returns {integer}
     */
    const getCurrentApprover = (actions) => {
      const approvers = actions.filter(
        (el) => el.reaction <= ReactionTypes.PENDING
      );

      return approvers.length > 0 ? approvers[0].rio_id : 0;
    };

    /**
     * Count pending status in workflow for you tab
     */
    const getPendingCount = (value) => {
      const forYouWorkFlow = [];

      // loop list and remove cancelled
      const workflows = value.filter((el) => el.status < StatusTypes.REJECTED);

      // get all workflows that current user is the approver and is not rejected
      workflows.forEach((workflow) => {
        if (
          getCurrentApprover(workflow.actions) ===
          props.service.data.neo_belongs.rio_id
        ) {
          forYouWorkFlow.push(workflow);
        }
      });

      countPending.value = forYouWorkFlow.length;
    };

    /**
     * Open create workflow modal
     */
    const openCreateWorkflowModal = () => {
      createWorkflowModalRef.value.show();
    };

    /**
     * Open connection list modal
     */
    const workflowSaved = (message) => {
      fileUploader.value.clearFiles();
      createWorkflowModalRef.value.modal.hide();
      setAlert('success', message);
    };

    /**
     * Initialize file uploader for created workflow
     */
    const initializeFileuploader = () => {
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-workflow-file-uploader',
        maxFileSize: BpheroConfig.WORKFLOW_SERVICE_MAX_FILE_SIZE,
        maxFiles: BpheroConfig.WORKFLOW_SERVICE_MAX_FILES_COUNT,
        allowMultiple: true,
        chunkUploads: true,
        instantUpload: true,
        allowReorder: true,
        allowProcess: false,
        labelIdle: '',
      });
    };

    /**
     * Open file browser
     */
    const openFileBrowser = () => {
      fileUploader.value.pond.browse();
    };

    /**
     * Attach file uploader remove files event listener
     */
    const attachRemoveFilesListener = () => {
      if (uploadedFileNames.value.length > 0) {
        fileUploader.value.pond.removeFiles({ revert: true });
      }
    };

    /**
     * Attach file uploader update file event listener
     */
    const attachUpdateFilesListener = () => {
      fileUploader.value.pond.on('updatefiles', (files) => {
        if (files.length > 0 === false) {
          createWorkflowModalRef.value.showFiles(false);
        }
      });
    };

    /**
     * Attach file uploader remove a file event listener
     */
    const attachRemoveFileListener = () => {
      fileUploader.value.pond.on('removefile', (error) => {
        // Get uploaded file names
        if (!error) {
          const fileName = fileUploader.value.pond.getFiles();
          // Empty array
          uploadedFileNames.value.splice(0);
          // Push filename to array
          /* eslint no-plusplus: ["error", { "allowForLoopAfterthoughts": true }] */
          for (let index = 0; index < fileName.length; index++) {
            uploadedFileNames.value.push(
              fileUploader.value.pond.getFile(fileName[index]).filename
            );
          }
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
     * Attach file uploader process file event listener
     */
    const attachProcessFileListener = () => {
      fileUploader.value.pond.on('processfiles', () => {
        createWorkflowModalRef.value.enableButton(true);
        // Empty array
        uploadedFileNames.value.splice(0);
        // Get file names of uploaded files
        const fileName = fileUploader.value.pond.getFiles();
        /* eslint no-plusplus: ["error", { "allowForLoopAfterthoughts": true }] */
        for (let index = 0; index < fileName.length; index++) {
          uploadedFileNames.value.push(
            fileUploader.value.pond.getFile(fileName[index]).filename
          );
        }
      });
    };

    /**
     * Attach file uploader add file event listener
     */
    const attachAddFileListener = () => {
      fileUploader.value.pond.on('addfile', (error, file) => {
        if (error) {
          fileUploader.value.pond.removeFile(file.id);
          setAlert('failed', error.sub);
        } else {
          createWorkflowModalRef.value.showFiles(true);
          createWorkflowModalRef.value.enableButton(false);
        }
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
      attachAddFileListener();
      attachRemoveFileListener();
      attachRemoveFilesListener();
      createWorkflowModalRef.value.enableButton(true);
    });

    const openAttachmentMenuModal = () => {
      attachmentMenuModalRef.value.show();
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
     * Set value for document type
     *
     * @param {int} value
     */
    const setDocumentType = (value) => {
      sendDocumentType.value = value;
    };

    /**
     * Open document list modal depends o ndocument type (sharing or personal)
     *
     * @param {int} id
     */
    const openDocumentAttachment = (id) => {
      setDocumentType(id);
      documentListModalRef.value.show();
    };

    /**
     * Display Selected file for workflow
     *
     * @param {object} chosenFile
     */
    const handleChooseDocumentFile = (chosenFile) => {
      // check if the attachfile is duplicated. if not chosen file added to the attachesFile
      if (
        !attachesFile.value.some(
          (attachment) =>
            Number(attachment.document_id) === chosenFile.document_id
        )
      ) {
        attachesFile.value.push(chosenFile);
      }
      closeDocumentList();
    };

    /**
     * clear selected attaches
     *
     */
    const clearAttaches = () => {
      attachesFile.value = [];
    };

    /**
     * Delete selected file in the file attachment modal
     * @param {int} id
     */
    const removeSelectedFile = (id) => {
      attachesFile.value = attachesFile.value.filter(
        (attachment) => attachment.document_id !== id
      );
    };

    return {
      alert,
      setAlert,
      resetAlert,
      workflowList,
      getCreatedWorkflows,
      getWorkflowForYou,
      createWorkflowModalRef,
      openCreateWorkflowModal,
      workflowSaved,
      getCount,
      countReturned,
      getPendingCount,
      countPending,
      fileUploader,
      openFileBrowser,
      uploadedFileNames,
      attachRemoveFilesListener,
      attachmentMenuModalRef,
      openAttachmentMenuModal,
      openDocumentAttachment,
      handleChooseDocumentFile,
      documentListModalRef,
      sendDocumentType,
      attachesFile,
      clearAttaches,
      removeSelectedFile,
    };
  },
});
</script>
