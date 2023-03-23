<template>
  <div
    class="modal fade"
    id="create-menu-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.create_upload') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="list-group list-group-flush">
            <a
              href="#"
              class="list-group-item"
              @click="openModal('#create-folder-modal')"
              >{{ $t('headers.create_new_folder') }}</a
            >
            <a
              href="#"
              class="list-group-item"
              @click="openModal('#upload-file-modal')"
              >{{ $t('headers.file_upload') }}</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'CreateMenuModal',
  components: {},
  setup(props, { emit }) {
    const modalRef = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const openModal = (modalId) => {
      modalRef.value.querySelector('.btn-close').click();
      emit('open-modal', modalId);
    };

    return {
      openModal,
      modalRef,
    };
  },
};
</script>
