<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Action Menu Modal -->
    <create-menu-modal :directory_id="knowledgeId" @openModal="openModal" />

    <!-- Create Folder Modal -->
    <create-folder-modal
      :directory_id="knowledgeId"
      @reset-list="resetList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Rename Folder Modal -->
    <rename-folder-modal
      :directory_id="knowledgeId"
      @reset-list="resetList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Document List Modal -->
    <knowledge-document-list-modal ref="documentListModalRef" />

    <!-- Delete confirmation Modal -->
    <delete-confirmation-modal
      :type="knowledgeType"
      @reset-list="resetList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <page-loader :show="pageLoading" />
        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div class="position-relative">
              <h3 class="py-1 mb-0 text-center">
                {{
                  $t('headers.service_owned_knowledge', { name: serviceName })
                }}
              </h3>
              <div class="text-center position-relative p-2 mb-2">
                <a v-if="knowledgeId !== null" :href="backUrl">
                  <i class="ai-arrow-left message__back"></i>
                </a>
                <h4 class="py-3 px-5 mb-0 text-center">{{ folderName }}</h4>
              </div>
            </div>
            <div class="input-group">
              <input
                name="keyword"
                class="form-control"
                type="text"
                v-model="formData.keyword"
              />
              <button
                class="btn btn-translucent-dark"
                type="button"
                @click="redirectToSearchList"
              >
                {{ $t('buttons.search') }}
              </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <div>
                <button
                  class="btn btn-link"
                  type="button"
                  @click="redirectToDraftList"
                >
                  {{ $t('buttons.draft_list') }}
                </button>
              </div>
              <div class="text-end">
                <button
                  class="btn btn-link"
                  type="button"
                  data-bs-toggle="modal"
                  data-bs-target="#create-menu-modal"
                >
                  <i class="me-2 ai-plus"></i>
                  {{ $t('buttons.create_new') }}
                </button>
              </div>
            </div>
            <div class="tab-content mt-2">
              <div class="tab-pane fade active show">
                <!-- Folder list section -->
                <folders-section
                  :id="knowledgeId"
                  @set-alert="setAlert"
                  @move-folder="handleMoveKnowledge"
                  @handle-delete="handleDelete"
                />

                <!-- Articles list section -->
                <articles-section
                  :id="knowledgeId"
                  @set-alert="setAlert"
                  @move-article="handleMoveKnowledge"
                  @handle-delete="handleDelete"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import BaseAlert from '../../base/BaseAlert.vue';
import FoldersSection from '../components/FoldersSection.vue';
import ArticlesSection from '../components/ArticlesSection.vue';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';
import i18n from '../../../i18n';
import CreateFolderModal from '../components/CreateFolderModal.vue';
import CreateMenuModal from '../components/CreateMenuModal.vue';
import RenameFolderModal from '../components/RenameFolderModal.vue';
import KnowledgeDocumentListModal from '../modals/KnowledgeDocumentListModal.vue';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import PageLoader from '../../base/BaseSectionLoader.vue';

export default defineComponent({
  name: 'KnowlegeListIndex',
  props: {
    knowledge: {
      type: [Array, Object],
      required: false,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAlert,
    FoldersSection,
    ArticlesSection,
    CreateFolderModal,
    CreateMenuModal,
    RenameFolderModal,
    KnowledgeDocumentListModal,
    DeleteConfirmationModal,
    PageLoader,
  },
  setup(props) {
    const knowledge = ref(props.knowledge);
    const service = ref(props.service);
    const documentListModalRef = ref(null);
    const knowledgeType = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({});
    const pageLoading = ref(false);
    const knowledgeApiService = new KnowledgeApiService();
    const searchKeyword = ref(null);

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Open specified modal
     *
     * @returns {void}
     */
    const openModal = (modalId) => {
      const targetModal = document.querySelector(modalId);
      /* eslint no-undef: 0 */
      const modal = bootstrap.Modal.getOrCreateInstance(targetModal);
      modal.show();
    };

    /**
     * Redirect to article draft list page
     *
     * @returns {void}
     */
    const redirectToDraftList = () => {
      window.location.replace(`/knowledges/articles/draft`);
    };

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
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case ServiceSelectionTypes.RIO:
          return data.full_name;
        case ServiceSelectionTypes.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Set knowledge ID
     *
     * @returns {int}
     */
    const knowledgeId = computed(() => {
      if (knowledge.value) {
        return knowledge.value.id;
      }

      return null;
    });

    /**
     * Set back url based on current directory
     *
     * @returns {string}
     */
    const backUrl = computed(() => {
      if (knowledge.value && knowledge.value.directory_id !== null) {
        return `/knowledges/${knowledge.value.directory_id}`;
      }

      return '/knowledges';
    });

    /**
     * Set folder name
     *
     * @returns {string}
     */
    const folderName = computed(() => {
      if (knowledge.value) {
        return knowledge.value.task_title;
      }

      return i18n.global.t('headers.home_folder');
    });

    /**
     * Reload page
     *
     * @returns {void}
     */
    const resetList = () => {
      if (knowledge.value !== null) {
        window.location.href = `/knowledges/${knowledge.value.id}`;
      } else {
        window.location.href = `/knowledges`;
      }
    };

    /**
     * Show modal to select folder
     *
     * @param {int} knowledgeSourceId
     */
    const handleMoveKnowledge = (knowledgeSourceId) => {
      document.querySelector('#movingKnowledgeSourceId').value =
        knowledgeSourceId;
      documentListModalRef.value.show();
    };

    /**
     * Delete folder/article handler
     *
     * @param {int} id
     * @param {int} type Defaults to Folder (1)
     * @returns {void}
     */
    const handleDelete = (id, type = 1) => {
      const modalId = '#knowledge-delete-confirmation';
      const modal = document.querySelector(modalId);

      modal.querySelector('input[name=id]').value = id;
      knowledgeType.value = type;

      openModal(modalId);
    };

    /**
     * Redirect to article search list page
     *
     * @returns {void}s
     */
    const redirectToSearchList = () => {
      setPageLoading(true);
      resetAlert();
      searchKeyword.value = formData.value.keyword;

      if (!formData.value.keyword || formData.value.keyword.trim() === '') {
        searchKeyword.value = null;
      }

      knowledgeApiService
        .saveSearchToSession(searchKeyword.value)
        .then(() => {
          window.location.href = `/knowledges/articles/search`;
        })
        .catch(() => {
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    return {
      knowledgeId,
      knowledgeType,
      serviceName,
      folderName,
      backUrl,
      alert,
      setAlert,
      resetAlert,
      openModal,
      resetList,
      handleDelete,
      redirectToDraftList,
      documentListModalRef,
      handleMoveKnowledge,
      formData,
      redirectToSearchList,
      setPageLoading,
      pageLoading,
    };
  },
});
</script>
