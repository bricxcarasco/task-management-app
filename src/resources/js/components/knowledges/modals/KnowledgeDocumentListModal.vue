<template>
  <div>
    <div
      class="modal fade"
      ref="modalRef"
      id="knowledge-document-linkage"
      tabindex="-1"
      role="dialog"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <loader :show="loading" />
          <div class="modal-header">
            <i
              v-if="currentFolder.length !== 0"
              class="ai-arrow-left hoverable"
              @click="handlePreviousDocument"
            ></i>
            <h4 class="modal-title">
              {{ $t('headers.move') }}
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
            <div>
              <p class="bg-dark c-white p-2 mb-0">
                {{ folderName }}
              </p>

              <ul class="list-group list-group-flush">
                <div v-if="folderList.length > 0">
                  <li
                    v-for="(folder, index) in folderList"
                    :key="index"
                    class="
                      list-group-item
                      py-2
                      position-relative
                      list--white
                      px-2
                      hoverable hoverable--background
                    "
                  >
                    <div
                      v-if="folder.is_owned"
                      class="mr-40"
                      @click.stop="handleChangeFolder(folder)"
                    >
                      <i class="h2 m-0 ai-folder"></i>
                      <span class="fs-xs c-primary ms-2">
                        {{ folder.folder_name }}
                      </span>
                    </div>
                  </li>
                </div>

                <div v-else>
                  <li
                    class="
                      list-group-item
                      px-0
                      py-2
                      d-flex
                      justify-content-center
                      c-primary
                    "
                  >
                    {{ $t('paragraphs.there_are_no_folders') }}
                  </li>
                </div>
              </ul>
              <div class="justify-content-right text-center mt-2 py-2">
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="handleSelectedFolder"
                >
                  {{ $t('buttons.move_here') }}
                </button>
              </div>
              <input type="hidden" id="movingKnowledgeSourceId" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import Loader from '../../base/BaseSectionLoader.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'KnowledgeDocumentListModal',
  props: {},
  components: {
    Loader,
  },
  setup(props, { emit }) {
    const knowledgeApiService = new KnowledgeApiService();
    const loading = ref(false);
    const folderList = ref({});
    const modalRef = ref(null);
    const currentFolder = ref([]);
    const currentFolderId = ref(null);
    const currentFolderName = ref(null);
    const formData = ref({});
    const errors = ref({});
    const movingKnowledgeSourceId = ref(null);
    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
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
     * Set loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setLoading = (state) => {
      loading.value = state;
    };

    /**
     * Get all folders within current directory
     *
     * @returns {void}
     */
    const getFolders = (folder) => {
      setLoading(true);
      let folderId = null;
      currentFolderName.value = null;
      if (folder) {
        folderId = folder.id;
        currentFolderId.value = folderId;
        currentFolderName.value = folder.folder_name;
      }

      knowledgeApiService
        .getFolders(folderId)
        .then((response) => {
          // remove folder to be moved from list
          const filteredList = response.data.data.filter(
            (list) => list.id !== Number(movingKnowledgeSourceId.value)
          );
          folderList.value = filteredList;
        })
        .catch((error) => {
          folderList.value = [];
          emit('set-alert', 'failed');

          throw error;
        })
        .finally(() => {
          setLoading(false);
        });
    };

    /**
     * Go back to previous document
     *
     * @returns {void}
     */
    const handlePreviousDocument = () => {
      currentFolder.value.pop();
      getFolders(currentFolder.value[currentFolder.value.length - 1]);
    };

    /**
     * Change folder directory
     *
     * @param {int} folderId
     * @returns {void}
     */
    const handleChangeFolder = (folder) => {
      folderList.value = [];
      currentFolder.value.push(folder);
      getFolders(folder);
    };

    /**
     * Set folder name
     *
     * @returns {string}
     */
    const folderName = computed(() => {
      if (currentFolderName.value) {
        return currentFolderName.value;
      }

      return i18n.global.t('headers.home_folder');
    });

    /**
     * Handle selected folder
     *
     * @returns {void}
     */
    const handleSelectedFolder = () => {
      formData.value.directory_id = currentFolderId;
      // call api service to move knowledge
      knowledgeApiService
        .moveKnowledge(movingKnowledgeSourceId.value, formData.value)
        .then(() => {
          if (formData.value.directory_id !== null) {
            window.location.href = `/knowledges/${formData.value.directory_id}`;
          } else {
            window.location.href = `/knowledges`;
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = parseValidationErrors(responseData.data);
            return;
          }

          resetModal();
          emit('set-alert', 'failed');
        });
    };

    /**
     * On mounted property
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
      });
      modalRef.value.addEventListener('shown.bs.modal', () => {
        movingKnowledgeSourceId.value = document.getElementById(
          'movingKnowledgeSourceId'
        ).value;
        getFolders(null);
      });
    });

    return {
      setLoading,
      folderList,
      folderName,
      modalRef,
      show,
      hide,
      modal,
      loading,
      handleChangeFolder,
      handlePreviousDocument,
      currentFolder,
      handleSelectedFolder,
      movingKnowledgeSourceId,
    };
  },
});
</script>
