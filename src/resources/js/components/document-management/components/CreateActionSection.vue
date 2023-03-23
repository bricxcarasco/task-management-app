<template>
  <div :class="isCreateActionAccessible() ? '' : 'mt-4'">
    <div v-if="isCreateActionAccessible()">
      <!-- Action Menu Modal -->
      <create-menu-modal @openModal="openModal" />

      <!-- Create Folder Modal -->
      <create-folder-modal
        :directory_id="directory_id"
        @reset-list="$emit('reset-list')"
        @set-alert="setAlert"
        @reset-alert="$emit('reset-alert')"
      />

      <!-- Upload  Modal -->
      <upload-file-modal
        :directory_id="directory_id"
        @reset-list="$emit('reset-list')"
        @set-alert="setAlert"
        @reset-alert="$emit('reset-alert')"
      />

      <!-- Create Action Button -->
      <div class="text-end mb-2">
        <a
          href="#"
          class="btn btn-link"
          data-bs-toggle="modal"
          data-bs-target="#create-menu-modal"
        >
          <i class="me-2 ai-plus"></i>
          {{ $t('buttons.create') }}・{{ $t('buttons.upload') }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import CreateFolderModal from './CreateFolderModal.vue';
import CreateMenuModal from './CreateMenuModal.vue';
import UploadFileModal from './UploadFileModal.vue';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default {
  name: 'CreateActionSection',
  props: {
    service: {
      type: [Object, null],
      required: true,
    },
    directory_id: {
      type: Number,
      default: null,
    },
  },
  components: {
    CreateFolderModal,
    CreateMenuModal,
    UploadFileModal,
  },
  setup(props, { emit }) {
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
     * Check if create actions are accessible
     *
     * @returns {void}
     */
    const isCreateActionAccessible = () => {
      const entity = props.service.data;

      if (props.service.type === ServiceSelectionTypes.NEO) {
        return entity.is_admin || entity.is_owner;
      }

      return true;
    };

    /**
     * Handle set alert emit
     *
     * @returns {void}
     */
    const setAlert = (status, message) => {
      emit('set-alert', status, message);
    };

    return {
      openModal,
      setAlert,
      isCreateActionAccessible,
    };
  },
};
</script>
