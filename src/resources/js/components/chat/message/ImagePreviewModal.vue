<template>
  <div
    class="modal fade"
    id="image-preview-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="resetModal"
          ></button>
        </div>
        <div class="modal-body text-center">
          <section-loader :show="modalLoading" />
          <img
            :src="getFilePreview()"
            @load="handleImageLoadSuccess"
            @error="handleImageLoadError"
            v-if="document_id !== null"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'ImagePreviewModal',
  props: {
    document_id: {
      type: Number,
      default: null,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const modalLoading = ref(true);
    const modalRef = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    /**
     * Get file preview link
     *
     * @param {integer} id
     * @returns {string}
     */
    const getFilePreview = () => {
      const previewUrl = '/api/document/file-preview';

      return `${previewUrl}/${props.document_id}?service=chat`;
    };

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadSuccess = () => {
      modalLoading.value = false;
    };

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadError = () => {
      resetModal();
      emit('reset-alert');
      emit('set-alert', 'failed');
    };

    /**
     * Reset modal loading when document id changed
     */
    watch(
      () => props.document_id,
      () => {
        modalLoading.value = true;
      }
    );

    return {
      modalLoading,
      modalRef,
      resetModal,
      getFilePreview,
      handleImageLoadSuccess,
      handleImageLoadError,
    };
  },
};
</script>
