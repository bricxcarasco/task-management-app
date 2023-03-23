<template>
  <div>
    <div class="card" id="connection-requests-list-items">
      <section-loader :show="fileLoading" />
      <div class="connection__wrapper">
        <base-alert
          :success="alert.success"
          :danger="alert.failed"
          :message="alert.message"
          @closeAlert="resetAlert"
        />
        <div v-if="fileDetails !== null">
          <div class="text-end">
            <a
              class="btn btn-link"
              type="button"
              @click.prevent="downloadFile()"
            >
              {{ $t('links.download') }}
            </a>
          </div>
          <div class="tab-content mt-2">
            <div class="tab-pane fade active show">
              <!-- Image Display -->
              <div v-if="isImage()">
                <img
                  class="d-block img-thumbnail rounded-0 mx-auto"
                  :src="fileUrl"
                  @error="
                    Common.handleNotFoundImageException(
                      $event,
                      DefaultImageCategory.DOCUMENT_MANAGEMENT
                    )
                  "
                />
              </div>
              <!-- PDF Display -->
              <div v-else-if="isPdf()">
                <iframe class="file-preview-pdf" :src="fileUrl"> </iframe>
              </div>
              <!-- No preview available -->
              <div class="p-3" v-else>
                <p class="border mb-2 text-center p-3">
                  {{ $t('messages.document_management.no_preview_found') }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="d-flex justify-content-center mt-3 pb-3">
          {{ $t('paragraphs.file_not_found') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import Common from '../../../common';
import DocumentApiService from '../../../api/document_management/document-option';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import BaseAlert from '../../base/BaseAlert.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'FilePreview',
  props: {
    service: {
      type: [Object, null],
      required: true,
    },
    file_data: {
      type: [Object, null],
      required: true,
    },
    id: {
      type: [String, null],
    },
  },
  components: {
    BaseAlert,
    SectionLoader,
  },
  setup(props) {
    const url = ref(null);
    const fileUrl = ref(null);
    const documentApi = new DocumentApiService();
    const alert = ref({
      success: false,
      failed: false,
    });
    const pdfConfig = ref(null);
    const fileDetails = ref([]);
    const fileLoading = ref(true);
    const targetDocument = ref(null);
    const requestData = ref(props.request_data);
    const currentService = ref(props.service);
    const fileId = ref(props.id);

    /**
     * Reset alert values
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
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
     * Download a file/folder
     *
     * @returns {void}
     */
    const downloadFile = () => {
      url.value = '/api/document/download/:id';
      url.value = url.value.replace(':id', props.id);
      window.open(url.value, '_blank');
    };

    /**
     * Check if file is image
     *
     * @returns {void}
     */
    const isImage = () => props.file_data.mime_type.split('/')[0] === `image`;

    /**
     * Check if file is pdf
     *
     * @returns {void}
     */
    const isPdf = () => props.file_data.mime_type.includes('pdf');

    /**
     * Get list of personal folders
     *
     * @returns {void}
     */
    const getFileDetails = async () => {
      fileLoading.value = true;

      try {
        const getDocumentsApi = await documentApi.getFileDocument(fileId.value);
        pdfConfig.value =
          getDocumentsApi.headers['content-type'] === 'application/pdf'
            ? '#toolbar=0'
            : null;
        fileUrl.value = `${window.location.protocol}//${window.location.host}/${getDocumentsApi.config.url}${pdfConfig.value}`;
      } catch (error) {
        resetAlert();
        setAlert('failed');
        throw error;
      } finally {
        fileLoading.value = false;
      }
    };

    /**
     * Watch on alert changes
     */
    watch(alert.value, () => {
      if (alert.value.success || alert.value.failed) {
        setTimeout(() => {
          resetAlert();
        }, 3000);
      }
    });

    getFileDetails();

    return {
      alert,
      Common,
      DefaultImageCategory,
      setAlert,
      resetAlert,
      fileLoading,
      fileDetails,
      fileUrl,
      requestData,
      currentService,
      getFileDetails,
      targetDocument,
      downloadFile,
      isImage,
      isPdf,
    };
  },
};
</script>
