<template>
  <div
    class="modal fade modal-file"
    id="attachment-menu-modal"
    data-bs-backdrop="static"
    ref="modalRef"
  >
    <div class="modal-dialog modal-sm my-0" role="document">
      <div class="modal-content">
        <div class="modal-body pb-0">
          <ul class="list-group">
            <li class="list-group-item hoverable" @click="openFileBrowser">
              {{ $t('links.select_local_files') }}
            </li>
            <li
              class="list-group-item sharedDocumentBtn hoverable"
              @click="openDocumentAttachment(DocumentListType.SHARED)"
            >
              {{
                $t('paragraphs.select_a_shared_file_from_document_management')
              }}
            </li>
            <li
              class="list-group-item sharedDocumentBtn hoverable"
              @click="openDocumentAttachment(DocumentListType.PERSONAL)"
            >
              {{
                $t('paragraphs.select_a_personal_file_from_document_management')
              }}
            </li>
          </ul>
        </div>
        <div class="modal-footer justify-content-start">
          <button
            class="btn btn-link c-primary w-100 text-start js-btn-close"
            type="button"
            data-bs-dismiss="modal"
          >
            {{ $t('buttons.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import DocumentListType from '../../../enums/DocumentListType';

export default {
  name: 'AttachmentMenuModal',
  components: {},
  setup(props, { emit }) {
    const modalRef = ref(null);

    /**
     * Open file upload browser
     *
     * @returns {void}
     */
    const openFileBrowser = () => {
      modalRef.value.querySelector('.js-btn-close').click();
      emit('open-file-browser');
    };

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
      modalRef.value.addEventListener('shown.bs.modal', () => {
        document.querySelector('#attachment-menu-modal').style.zIndex = '1062';
        document.querySelector(
          '.modal-backdrop.show:nth-of-type(2n)'
        ).style.zIndex = '1061';
      });
    };

    /**
     * Open document attachment list
     *
     * @returns {void}
     */
    const openDocumentAttachment = (id) => {
      modalRef.value.querySelector('.js-btn-close').click();
      emit('open-document-attachment', id);
    };

    return {
      modalRef,
      openFileBrowser,
      show,
      openDocumentAttachment,
      DocumentListType,
    };
  },
};
</script>
