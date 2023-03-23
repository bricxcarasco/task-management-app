<template>
  <file-pond
    name="profile_image"
    ref="pond"
    :label-idle="$t('paragraphs.select_profile_image')"
    :labelInvalidField="$t('filepond.labelInvalidField')"
    :labelFileWaitingForSize="$t('filepond.labelFileWaitingForSize')"
    :labelFileSizeNotAvailable="$t('filepond.labelFileSizeNotAvailable')"
    :labelFileLoading="$t('filepond.labelFileLoading')"
    :labelFileLoadError="$t('filepond.labelFileLoadError')"
    :labelFileProcessing="$t('filepond.labelFileProcessing')"
    :labelFileProcessingComplete="$t('filepond.labelFileProcessingComplete')"
    :labelFileProcessingAborted="$t('filepond.labelFileProcessingAborted')"
    :labelFileProcessingError="$t('filepond.labelFileProcessingError')"
    :labelFileProcessingRevertError="
      $t('filepond.labelFileProcessingRevertError')
    "
    :labelFileRemoveError="$t('filepond.labelFileRemoveError')"
    :labelTapToCancel="$t('filepond.labelTapToCancel')"
    :labelTapToRetry="$t('filepond.labelTapToRetry')"
    :labelTapToUndo="$t('filepond.labelTapToUndo')"
    :labelButtonRemoveItem="$t('filepond.labelButtonRemoveItem')"
    :labelButtonAbortItemLoad="$t('filepond.labelButtonAbortItemLoad')"
    :labelButtonRetryItemLoad="$t('filepond.labelButtonRetryItemLoad')"
    :labelButtonAbortItemProcessing="
      $t('filepond.labelButtonAbortItemProcessing')
    "
    :labelButtonUndoItemProcessing="
      $t('filepond.labelButtonUndoItemProcessing')
    "
    :labelButtonRetryItemProcessing="
      $t('filepond.labelButtonRetryItemProcessing')
    "
    :labelButtonProcessItem="$t('filepond.labelButtonProcessItem')"
    :labelMaxFileSizeExceeded="$t('filepond.labelMaxFileSizeExceeded')"
    :labelMaxFileSize="$t('filepond.labelMaxFileSize')"
    :labelMaxTotalFileSizeExceeded="
      $t('filepond.labelMaxTotalFileSizeExceeded')
    "
    :labelMaxTotalFileSize="$t('filepond.labelMaxTotalFileSize')"
    :labelFileTypeNotAllowed="$t('filepond.labelFileTypeNotAllowed')"
    :fileValidateTypeLabelExpectedTypes="
      $t('filepond.fileValidateTypeLabelExpectedTypes')
    "
    :imageValidateSizeLabelFormatError="
      $t('filepond.imageValidateSizeLabelFormatError')
    "
    :imageValidateSizeLabelImageSizeTooSmall="
      $t('filepond.imageValidateSizeLabelImageSizeTooSmall')
    "
    :imageValidateSizeLabelImageSizeTooBig="
      $t('filepond.imageValidateSizeLabelImageSizeTooBig')
    "
    :imageValidateSizeLabelExpectedMinSize="
      $t('filepond.imageValidateSizeLabelExpectedMinSize')
    "
    :imageValidateSizeLabelExpectedMaxSize="
      $t('filepond.imageValidateSizeLabelExpectedMaxSize')
    "
    :imageValidateSizeLabelImageResolutionTooLow="
      $t('filepond.imageValidateSizeLabelImageResolutionTooLow')
    "
    :imageValidateSizeLabelImageResolutionTooHigh="
      $t('filepond.imageValidateSizeLabelImageResolutionTooHigh')
    "
    :imageValidateSizeLabelExpectedMinResolution="
      $t('filepond.imageValidateSizeLabelExpectedMinResolution')
    "
    :imageValidateSizeLabelExpectedMaxResolution="
      $t('filepond.imageValidateSizeLabelExpectedMaxResolution')
    "
    :allow-multiple="false"
    maxFileSize="300KB"
    :maxFiles="BpheroConfig.PROFILE_IMAGE_MAX_FILES_COUNT"
    accepted-file-types="image/jpeg, image/png"
    server="/api/image/process"
    :files="myFiles"
    credits="false"
  />
</template>

<script>
// Import Vue FilePond
import vueFilePond from 'vue-filepond';

// Import FilePond styles
import 'filepond/dist/filepond.min.css';

// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import { ref } from 'vue';

import BpheroConfig from '../../config/bphero';

// Create component
const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize
);

export default {
  name: 'app',
  components: {
    FilePond,
  },
  setup() {
    const pond = ref(null);
    const myFiles = ref([]);

    return {
      BpheroConfig,
      pond,
      myFiles,
    };
  },
};
</script>
