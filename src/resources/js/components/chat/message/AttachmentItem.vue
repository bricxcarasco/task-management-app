<template>
  <div class="chat__box p-3 mt-2 position-relative" v-if="attachment !== null">
    <section-loader :show="attachmentLoading" />

    <!-- Other Content -->
    <slot />

    <!-- Image Preview -->
    <img
      class="chat__image hoverable"
      :src="getFilePreview()"
      @load="handleImageLoadSuccess"
      @error="handleImageLoadError"
      @click="$emit('preview-full-image', attachment.id)"
      v-if="isImageLoaded"
    />

    <!-- Generic File -->
    <p class="chat__content fs-md m-0 fw-semibold" v-else>
      <span class="hoverable" @click="previewFile">
        <i class="h5 m-0 me-2 ai-file-text hoverable text-reset"></i>
        {{ attachment.document_name }}
      </span>
      <i
        class="h5 m-0 ms-2 ai-download hoverable text-reset"
        @click="downloadFile"
      ></i>
    </p>
  </div>
  <div class="chat__box p-3 mt-2 position-relative" v-else>
    <!-- Other Content -->
    <slot />

    <!-- Unavailable Attachmemnt -->
    <p
      class="
        chat__content
        fs-md
        m-0
        fw-bold
        border border-radius border-2
        rounded
        py-2
        px-3
      "
    >
      <i class="h5 m-0 me-2 ai-alert-circle text-reset"></i>
      {{ $t('messages.document_management.unavailable_attachment') }}
    </p>
  </div>
</template>

<script>
import { ref } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import BpheroConfig from '../../../config/bphero';

export default {
  name: 'AttachmentItem',
  props: {
    attachment: {
      type: [Object, null],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props) {
    const attachmentLoading = ref(true);
    const isImageLoaded = ref(true);

    /**
     * Get file preview link
     *
     * @param {integer} id
     * @returns {string}
     */
    const getFilePreview = () => {
      const previewUrl = '/api/document/file-preview';

      return `${previewUrl}/${props.attachment.id}?service=chat`;
    };

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadSuccess = () => {
      isImageLoaded.value = true;
      attachmentLoading.value = false;
    };

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadError = () => {
      isImageLoaded.value = false;
      attachmentLoading.value = false;
    };

    /**
     * Check if entity is an image
     *
     * @param {string} mimetype
     * @returns {bool}
     */
    const isImage = (mimetype) => {
      const isImageMimeType =
        BpheroConfig.CHAT_SERVICE_PREVIEW_IMAGE_MIMETYPES.includes(mimetype);

      return isImageMimeType;
    };

    /**
     * Check if entity is a pdf
     *
     * @returns {bool}
     */
    const isPdf = () => {
      const pdfMimeTypes = ['application/pdf', 'application/x-pdf'];

      return pdfMimeTypes.includes(props.attachment.mime_type);
    };

    /**
     * Download file
     *
     * @returns {void}
     */
    const downloadFile = () => {
      const url = `/api/document/download/${props.attachment.id}`;
      window.open(url, '_blank');
    };

    /**
     * Display preview for certain files (PDF)
     *
     * @returns {void}
     */
    const previewFile = () => {
      if (isPdf()) {
        const previewUrl = getFilePreview();
        window.open(previewUrl, '_blank');

        return;
      }

      downloadFile();
    };

    if (props.attachment !== null) {
      // Check if image to identify attachment view
      if (!isImage(props.attachment.mime_type)) {
        isImageLoaded.value = false;
        attachmentLoading.value = false;
      }
    }

    return {
      isImageLoaded,
      attachmentLoading,
      getFilePreview,
      handleImageLoadError,
      handleImageLoadSuccess,
      isImage,
      downloadFile,
      previewFile,
    };
  },
};
</script>
