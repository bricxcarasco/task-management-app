<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
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
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div class="pb-4 pb-md-0">
            <button
              type="button"
              class="btn btn-link"
              @click="handleReturnClick"
            >
              <i class="ai-arrow-left"></i>
            </button>
            <p class="bg-gray mb-0 p-2">{{ $t('headers.basic_settings') }}</p>
            <form action="" ref="formRef">
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <th>
                      {{ $t('labels.business_name')
                      }}<span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.name"
                        class="form-control"
                        :class="errors?.name != null ? 'is-invalid' : ''"
                        type="text"
                        placeholder="田中太郎"
                      />
                      <base-validation-error :errors="errors?.name" />
                    </td>
                  </tr>
                  <tr>
                    <th>{{ $t('labels.department_name') }}</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.department_name"
                        class="form-control"
                        :class="
                          errors?.department_name != null ? 'is-invalid' : ''
                        "
                        type="text"
                        placeholder="部署名"
                      />
                      <base-validation-error
                        :errors="errors?.department_name"
                      />
                    </td>
                  </tr>
                  <tr>
                    <th>{{ $t('labels.business_address') }}</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.address"
                        class="form-control"
                        :class="errors?.address != null ? 'is-invalid' : ''"
                        type="text"
                        placeholder="東京都千代田区千代田1-1-1"
                      />
                      <base-validation-error :errors="errors?.address" />
                    </td>
                  </tr>
                  <tr>
                    <th>TEL</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.tel"
                        class="form-control"
                        :class="errors?.tel != null ? 'is-invalid' : ''"
                        type="text"
                        placeholder="+810123456789"
                      />
                      <base-validation-error :errors="errors?.tel" />
                    </td>
                  </tr>
                  <tr>
                    <th>FAX</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.fax"
                        class="form-control"
                        :class="errors?.fax != null ? 'is-invalid' : ''"
                        type="text"
                        placeholder="03-0000-0000"
                      />
                      <base-validation-error :errors="errors?.fax" />
                    </td>
                  </tr>
                  <tr>
                    <th>{{ $t('labels.business_number') }}</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.business_number"
                        class="form-control"
                        :class="
                          errors?.business_number != null ? 'is-invalid' : ''
                        "
                        type="text"
                        placeholder="A12345"
                      />
                      <base-validation-error
                        :errors="errors?.business_number"
                      />
                    </td>
                  </tr>
                  <tr>
                    <th>{{ $t('labels.payment_terms') }}</th>
                    <td class="text-end">
                      <input
                        v-model="basicSettings.payment_terms_one"
                        class="form-control"
                        :class="
                          errors?.payment_terms_one != null ? 'is-invalid' : ''
                        "
                        type="text"
                      />
                      <base-validation-error
                        :errors="errors?.payment_terms_one"
                      />
                      <br />
                      <input
                        v-model="basicSettings.payment_terms_two"
                        class="form-control"
                        :class="
                          errors?.payment_terms_two != null ? 'is-invalid' : ''
                        "
                        type="text"
                      />
                      <base-validation-error
                        :errors="errors?.payment_terms_two"
                      />
                      <br />
                      <input
                        v-model="basicSettings.payment_terms_three"
                        class="form-control"
                        :class="
                          errors?.payment_terms_three != null
                            ? 'is-invalid'
                            : ''
                        "
                        type="text"
                      />
                      <base-validation-error
                        :errors="errors?.payment_terms_three"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
              <p class="bg-gray mb-0 p-2">{{ $t('labels.issuer_logo') }}</p>
              <div
                class="
                  d-flex
                  align-items-center
                  justify-content-around
                  mt-2
                  border-bottom
                  pb-2
                "
              >
                <span class="w-25">{{ $t('labels.logo') }}</span>
                <div class="flex-1 mx-auto text-center">
                  <img
                    class="d-block mx-auto js-default-img"
                    :src="defaultImage"
                    alt="logo"
                    style="width: 150px"
                    @error="handleErrorImage"
                    v-if="isDefaultImage"
                  />
                  <div
                    class="file-uploader col-12 mx-auto"
                    v-show="hasUploadFile"
                  >
                    <input
                      type="file"
                      class="js-product-file-uploader"
                      name="image[]"
                      data-upload="/api/forms/basic-settings/images/process"
                      data-chunk="/api/forms/basic-settings/images/chunk"
                      data-revert="/api/forms/basic-settings/images/revert"
                      data-restore="/api/forms/basic-settings/images/restore"
                      data-load="/api/forms/basic-settings/images/load"
                      :data-max-file-size="
                        BpheroConfig.FORM_SERVICE_MAX_FILE_SIZE
                      "
                      :data-max-files="
                        BpheroConfig.FORM_SERVICE_MAX_FILES_COUNT
                      "
                    />
                  </div>

                  <div
                    class="btn-group mt-2"
                    role="group"
                    aria-label="Basic example"
                  >
                    <button
                      type="button"
                      class="btn btn-primary"
                      :disabled="isUploadDisabled"
                      @click="openFileBrowser"
                    >
                      {{ $t('buttons.add') }}
                    </button>
                    <button
                      type="button"
                      class="btn btn-danger"
                      @click="clearUploadedImage"
                    >
                      {{ $t('buttons.delete') }}
                    </button>
                  </div>
                </div>
              </div>
              <div class="text-center mt-4">
                <base-button
                  :title="this.$i18n.t('buttons.register')"
                  :buttonType="'primary'"
                  :loading="loading"
                  :disabled="isSubmitDisabled"
                  @handleClick="handleSaveBasicSettings"
                />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import BaseAlert from '../../base/BaseAlert.vue';
import BaseButton from '../../base/BaseButton.vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import Common from '../../../common';
import { objectifyForm } from '../../../utils/objectifyForm';
import BpheroConfig from '../../../config/bphero';
import FormsApiService from '../../../api/forms/forms';
import PageLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'BasicSettings',
  props: {
    basic_settings: {
      type: [Array, Object],
      required: true,
    },
    user_image: {
      type: [String],
      required: true,
    },
  },
  components: {
    BaseAlert,
    BaseButton,
    BaseValidationError,
    PageLoader,
  },
  setup(props) {
    const alert = ref({
      success: false,
      failed: false,
    });
    const basicSettings = ref(props.basic_settings);
    const errors = ref(null);
    const formsApiService = new FormsApiService();
    const loading = ref(false);
    const pageLoading = ref(false);
    const formRef = ref({});
    const deleteProfileModalRef = ref(null);
    const fileUploader = ref({});
    const hasUploadFile = ref(false);
    const isSubmitDisabled = ref(false);
    const isUploadDisabled = ref(false);
    const isDefaultImage = ref(true);
    const deleteExistingImage = ref(false);

    /**
     * Default image
     */
    const defaultImage = computed(() => {
      const displayImage =
        basicSettings.value.image ||
        props.user_image ||
        BpheroConfig.DEFAULT_IMG;

      return `${displayImage}?${new Date().getTime()}`;
    });

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
     * Set loading status
     *
     * @param {string} status
     * @returns {void}
     */
    const setLoading = (status) => {
      loading.value = status;
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
     * Update RIO profile name
     */
    const handleSaveBasicSettings = async () => {
      const data = {
        name: basicSettings.value.name,
        department_name: basicSettings.value.department_name,
        address: basicSettings.value.address,
        tel: basicSettings.value.tel,
        fax: basicSettings.value.fax,
        business_number: basicSettings.value.business_number,
        payment_terms_one: basicSettings.value.payment_terms_one,
        payment_terms_two: basicSettings.value.payment_terms_two,
        payment_terms_three: basicSettings.value.payment_terms_three,
        delete_existing: false,
      };

      // Handle file upload
      if (hasUploadFile.value) {
        // Start uploading all files
        await fileUploader.value.uploadFiles();

        // Inject server codes to input data
        const formObject = objectifyForm(formRef.value);
        const [image] = formObject.image;
        data.image = image;
        deleteExistingImage.value = false;
        data.delete_existing = false;
      }

      if (deleteExistingImage.value) {
        data.image = null;
        data.delete_existing = deleteExistingImage.value;
      }

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await formsApiService
        .saveBasicSettings(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Remove errors & show success alert
            errors.value = null;

            // redirect to list page with success message
            window.location.href = '/forms/basic-settings/success';
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = Common.constructValidationErrors(responseData.data);

            // End loading
            setPageLoading(false);
            setLoading(false);

            return;
          }

          setAlert('failed');
        });
    };

    /**
     * Redirect to forms list page
     */
    const handleReturnClick = async () => {
      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      // Redirect back to forms list page
      window.location.href = '/forms/quotations';
    };

    /**
     * Change disabled state of form submit button
     */
    const toggleProductSubmit = (state) => {
      isSubmitDisabled.value = state;
    };

    /**
     * Attach file uploader update file event listener
     */
    const attachUpdateFilesListener = () => {
      fileUploader.value.pond.on('updatefiles', (files) => {
        const hasFiles = files.length > 0;

        // Display file uploader UI and hide default image
        hasUploadFile.value = hasFiles;
        isUploadDisabled.value = hasFiles;
        isDefaultImage.value = !hasFiles;

        // Enable when no file
        if (!hasFiles) {
          toggleProductSubmit(false);

          return;
        }

        // Enable submit button when all file process is complete
        const isProcessComplete = fileUploader.value.isProcessComplete();
        toggleProductSubmit(!isProcessComplete);
      });
    };

    /**
     * Attach file uploader process file event listener
     */
    const attachFileProcessingListener = () => {
      fileUploader.value.pond.on('processfile', () => {
        // Enable submit button when all file process is complete
        const isProcessComplete = fileUploader.value.isProcessComplete();
        toggleProductSubmit(!isProcessComplete);
      });
    };

    /**
     * Attach file uploader warning event listener
     */
    const attachUploadWarningListener = () => {
      fileUploader.value.pond.on('warning', (error) => {
        if (error.body === 'Max files') {
          const errorMessage = i18n.global.t(
            'messages.chat.max_upload_files_reached'
          );
          setAlert('failed', errorMessage);

          return;
        }

        setAlert('failed', error.body);
      });
    };

    /**
     * Open file browser
     */
    const openFileBrowser = () => {
      fileUploader.value.pond.browse();
    };

    /**
     * Handle image render error
     */
    const handleErrorImage = () => {
      const defaultImgSelector = document.querySelector('.js-default-img');
      defaultImgSelector.src = `${
        BpheroConfig.DEFAULT_IMG
      }?${new Date().getTime()}`;
    };

    /**
     * Clear uploaded image
     */
    const clearUploadedImage = async () => {
      if (!hasUploadFile.value) {
        deleteExistingImage.value = true;
        basicSettings.value.image = null;
      }

      fileUploader.value.clearFiles();
      isUploadDisabled.value = false;
    };

    /**
     * Initialize file uploader for chat service
     */
    const initializeFileuploader = () => {
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-product-file-uploader',
        codesSelector: '.js-product-file-uploader-temp-files',
        pathsSelector: '.js-product-file-uploader-local-files',
        hasImagePreview: true,
        allowMultiple: false,
        chunkUploads: true,
        instantUpload: true,
        allowReorder: true,
        allowProcess: false,
        styleItemPanelAspectRatio: '0.75',
        labelIdle: '',
        allowFileTypeValidation: true,
        maxFileSize: BpheroConfig.FORM_SERVICE_MAX_FILE_SIZE,
        maxFiles: BpheroConfig.FORM_SERVICE_MAX_FILES_COUNT,
        acceptedFileTypes: BpheroConfig.FORM_SERVICE_ALLOWED_TYPES,
        fileValidateTypeLabelExpectedTypesMap: {
          'image/jpeg': '.jpeg',
          'image/jpg': '.jpg',
          'image/pjpeg': null,
          'image/png': '.png',
        },
      });
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      initializeFileuploader();
      attachUpdateFilesListener();
      attachFileProcessingListener();
      attachUploadWarningListener();
    });

    return {
      alert,
      basicSettings,
      errors,
      handleReturnClick,
      handleSaveBasicSettings,
      loading,
      pageLoading,
      resetAlert,
      setAlert,
      setLoading,
      setPageLoading,
      deleteProfileModalRef,
      formRef,
      openFileBrowser,
      clearUploadedImage,
      hasUploadFile,
      BpheroConfig,
      defaultImage,
      isDefaultImage,
      isSubmitDisabled,
      isUploadDisabled,
      handleErrorImage,
    };
  },
});
</script>
