<template>
  <div>
    <p class="bg-dark c-white p-2 mb-0">
      {{ $t('labels.folder') }}
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
          <div class="mr-40" @click.stop="handleChangeFolder(folder.id)">
            <i class="h2 m-0 ai-folder"></i>
            <span class="fs-xs c-primary ms-2">
              {{ folder.folder_name }}
            </span>
          </div>
          <div
            v-if="folder.is_owned"
            class="vertical-right hasDropdown dropstart"
          >
            <button
              class="btn btn-link"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="color-primary ai-more-vertical"></i>
            </button>
            <div class="dropdown-menu my-1">
              <button
                @click="handleDeleteFolder(folder.id)"
                class="dropdown-item"
              >
                {{ $t('buttons.delete') }}
              </button>
              <a
                @click="handleRenameFolder($event, folder)"
                class="dropdown-item"
              >
                {{ $t('links.change_name') }}
              </a>
              <a
                href="#"
                @click.stop="handleMoveFolder(folder.id)"
                class="dropdown-item"
              >
                {{ $t('buttons.move') }}
              </a>
            </div>
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
          <!-- Loader -->
          <loader :show="loading" />

          {{ $t('paragraphs.there_are_no_folders') }}
        </li>
      </div>
    </ul>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import Loader from '../../base/BaseSectionLoader.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import KnowledgeTypes from '../../../enums/KnowledgeTypes';
import Common from '../../../common';

export default defineComponent({
  name: 'FoldersSection',
  props: {
    id: {
      type: [String, Number, null],
      required: false,
    },
  },
  components: {
    Loader,
  },
  setup(props, { emit }) {
    const knowledgeApiService = new KnowledgeApiService();
    const loading = ref(false);
    const folderList = ref({});
    const id = ref(props.id);

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
     * Construct form data
     *
     * @param {Object} data
     * @returns {Object}
     */
    const constructFormData = (data) => ({
      id: data.id,
      task_title: data.folder_name,
    });

    /**
     * Rename folder
     *
     * @returns {void}
     */
    const handleRenameFolder = (event, folder) => {
      event.preventDefault();
      event.stopPropagation();

      /* eslint no-undef: 0 */
      const renameFolderModalNode = document.getElementById(
        'rename-folder-modal'
      );
      const renameFolderModal = computed(
        () => new bootstrap.Modal(renameFolderModalNode)
      );
      const form = renameFolderModalNode.querySelector('form');
      const data = constructFormData(folder);
      Common.fillForm(form, data);
      renameFolderModal.value.show();
    };

    /**
     * Get all folders within current directory
     *
     * @returns {void}
     */
    const getFolders = () => {
      setLoading(true);

      knowledgeApiService
        .getFolders(id.value)
        .then((response) => {
          folderList.value = response.data.data;
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
     * Delete folder directory
     *
     * @param {int} folderId
     * @returns {void}
     */
    const handleDeleteFolder = (folderId) => {
      emit('handle-delete', folderId, KnowledgeTypes.FOLDER);
    };

    /**
     * Change folder directory
     *
     * @param {int} folderId
     * @returns {void}
     */
    const handleChangeFolder = (folderId) => {
      window.location.href = `/knowledges/${folderId}`;
    };

    /**
     * Move folder
     *
     * @param {int} folderId
     */
    const handleMoveFolder = (folderId) => {
      emit('move-folder', folderId);
    };

    /**
     * On mounted property
     */
    onMounted(() => {
      getFolders();
    });

    return {
      loading,
      setLoading,
      folderList,
      handleMoveFolder,
      handleDeleteFolder,
      handleChangeFolder,
      handleRenameFolder,
    };
  },
});
</script>
