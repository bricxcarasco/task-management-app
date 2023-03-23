<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Document List Modal -->
    <document-list-modal
      ref="documentListModalRef"
      :service="service"
      @show-confirmation="pasteUrlConfirmation"
      @set-alert="setAlert"
    />

    <!-- File selection confirmation modal -->
    <file-selection-confirmation-modal
      :service="service"
      @paste-url="pasteUrl"
      @handle-back="handleBack"
    />
    <!-- Update published article modal -->
    <update-confirmation-modal
      :formData="formData"
      :referenceUrlObject="referenceUrlObject"
      @set-validation-errors="setValidationErrors"
      @set-alert="setAlert"
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
        <page-loader :show="pageLoading" />
        <div class="col-12 col-md-9 offset-md-3">
          <div
            class="
              d-flex
              align-items-center
              justify-content-between
              border-bottom
            "
          >
            <p class="text-center">
              {{ $t('headers.service_owned_knowledge', { name: serviceName }) }}
            </p>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <a :href="backUrl" class="btn btn-link">
              <i class="ai-arrow-left"></i> {{ $t('buttons.back_to_list') }}
            </a>
          </div>
          <!-- Article information -->
          <p class="mb-0 p-4 text-center">
            {{ $t('headers.create_knowledge_article') }}
          </p>
          <div class="p-4">
            <form action="" ref="formRef" novalidate>
              <div>
                <div class="mb-3">
                  <input
                    name="draft_id"
                    type="hidden"
                    v-model="formData.draft_id"
                  />
                  <label class="form-label"
                    >■{{ $t('labels.task_title')
                    }}<span class="text-danger">*</span></label
                  >
                  <input
                    name="task_title"
                    class="form-control"
                    type="text"
                    :class="errors?.task_title != null ? 'is-invalid' : ''"
                    v-model="formData.task_title"
                  />
                  <base-validation-error :errors="errors?.task_title" />
                </div>
                <div></div>
                <div class="mb-3">
                  <label class="form-label"
                    >■{{ $t('labels.body')
                    }}<span class="text-danger">*</span></label
                  >
                  <editor
                    v-model="formData.contents"
                    :api-key="BpheroConfig.TINYMCE_API_KEY"
                    :init="TinymceConfig"
                  />
                  <p class="text-sm text-danger">
                    {{ parseContentError(errors?.contents) }}
                  </p>
                </div>
                <div class="mb-3">
                  <label class="form-label"
                    >■{{ $t('labels.reference_url') }}</label
                  >
                  <div
                    v-for="(reference, index) in referenceURLFieldCount"
                    :key="index"
                  >
                    <input
                      name="urls"
                      class="form-control reference-url"
                      type="text"
                      :class="
                        referenceUrlErrors['reference_url_' + index] != null
                          ? 'is-invalid'
                          : ''
                      "
                      v-model="referenceUrlObject['reference_url_' + index]"
                    />
                    <base-validation-error
                      :errors="referenceUrlErrors['reference_url_' + index]"
                    />
                    <div class="d-flex mb-2 justify-content-end">
                      <button
                        type="button"
                        class="btn btn-link"
                        @click="showDocumentManagementModal(index)"
                      >
                        {{ $t('links.retrieve_from_document_management') }}
                      </button>
                    </div>
                  </div>
                </div>
                <div
                  class="d-flex align-items-center justify-content-around"
                  v-if="referenceURLFieldCount < 5"
                >
                  <button
                    @click.prevent="addReferenceField()"
                    class="btn btn-link"
                  >
                    + {{ $t('links.add_refrence_url') }}
                  </button>
                </div>
                <div v-if="!updatePublishedArticle" class="text-center p-4">
                  <button
                    type="button"
                    class="btn btn-primary d-block w-100"
                    :disabled="isSaveAsDraftButtonDisabled"
                    @click="handleSaveDraft()"
                  >
                    {{ $t('buttons.save_as_draft') }}
                  </button>
                  <div class="p-1"></div>
                  <button
                    type="button"
                    class="btn btn-primary d-block w-100"
                    :disabled="isPublishButtonDisabled"
                    @click="handleSubmitForm()"
                  >
                    {{ $t('buttons.publish') }}
                  </button>
                </div>
                <div v-if="updatePublishedArticle" class="text-center p-4">
                  <button
                    type="button"
                    class="btn btn-primary d-block w-100"
                    @click="handleUpdatePublishedArticle()"
                  >
                    {{ $t('buttons.update') }}
                  </button>
                  <div class="p-1"></div>
                </div>
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
import Editor from '@tinymce/tinymce-vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import BaseValidationError from '../../base/BaseValidationError.vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import ArticleTypes from '../../../enums/ArticleTypes';
import DocumentListModal from '../modals/DocumentListModal.vue';
import FileSelectionConfirmationModal from '../modals/FileSelectionConfirmationModal.vue';
import BpheroConfig from '../../../config/bphero';
import TinymceConfig from '../../../utils/tinymce-config';
import i18n from '../../../i18n';
import UpdateConfirmationModal from './UpdateConfirmationModal.vue';

export default defineComponent({
  name: 'CreateArticle',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    directory_id: {
      type: [Number, String],
      default: null,
    },
    knowledge: {
      type: [Array, Object],
      required: false,
    },
  },
  components: {
    BaseValidationError,
    PageLoader,
    BaseAlert,
    DocumentListModal,
    Editor,
    FileSelectionConfirmationModal,
    UpdateConfirmationModal,
  },
  setup(props) {
    const knowledgeApiService = new KnowledgeApiService();
    const formData = ref({});
    const errors = ref(null);
    const pageLoading = ref(false);
    const service = ref(props.service);
    const knowledge = ref(props.knowledge);
    const documentListModalRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });
    const referenceUrlObject = ref({});
    const referenceUrlErrors = ref({});
    const referenceURLFieldCount = ref(1);
    const currentReferenceURLFieldId = ref(null);
    const currentSelectedFile = ref({});

    /**
     * check if to disable save as draft button
     */
    const isSaveAsDraftButtonDisabled = computed(() => {
      if (formData.value.task_title) {
        return false;
      }

      return true;
    });

    /**
     * check if to disable publish button
     */
    const isPublishButtonDisabled = computed(() => {
      if (formData.value.task_title && formData.value.contents) {
        return false;
      }

      return true;
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
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case 'RIO':
          return data.full_name;
        case 'NEO':
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Set back url based on current directory
     *
     * @returns {string}
     */
    const backUrl = computed(() => {
      if (props.directory_id !== null) {
        return `/knowledges/${props.directory_id}`;
      }

      return '/knowledges';
    });

    /**
     * Check if to show/hide update button
     *
     * @returns {boolean}
     */
    const updatePublishedArticle = computed(() => {
      if (
        props.knowledge !== null &&
        props.knowledge.is_draft === ArticleTypes.PUBLIC
      ) {
        return true;
      }

      return false;
    });

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
     * Add URL reference field
     *
     * @returns {void}
     */
    const addReferenceField = () => {
      if (referenceURLFieldCount.value < 5) {
        referenceURLFieldCount.value += 1;
      }
    };

    /**
     * Add URL reference field
     *
     * @param {integer} id
     */
    const showDocumentManagementModal = (id) => {
      currentSelectedFile.value = null;
      currentReferenceURLFieldId.value = id;
      documentListModalRef.value.show();
    };

    /**
     * Handle back button
     *
     */
    const handleBack = () => {
      currentSelectedFile.value = null;
      documentListModalRef.value.show();
    };

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
     * Update published article
     *
     * @returns {void}
     */
    const handleUpdatePublishedArticle = () => {
      const modalId = '#knowledge-update-confirmation';

      openModal(modalId);
    };

    /**
     * Set validation errors from update modal
     *
     * @returns {String} errorMessage
     */
    const parseContentError = (contentError) => {
      let errorMessage = contentError;
      if (contentError) {
        errorMessage = contentError.toString().replace('', '');
      }

      return errorMessage;
    };

    /**
     * Set validation errors from update modal
     *
     */
    const setValidationErrors = (validationErrors) => {
      errors.value = validationErrors;
      referenceUrlErrors.value = validationErrors;
    };

    /**
     * Show confirmation modal
     *
     * @param {string} fileUrl
     */
    const pasteUrlConfirmation = (file) => {
      /* eslint no-undef: 0 */
      const confirmModalNode = document.getElementById(
        'url-reference-confirmation'
      );
      currentSelectedFile.value = file;
      const confirmModal = computed(
        () => new bootstrap.Modal(confirmModalNode)
      );
      confirmModal.value.show();
    };

    /**
     * Paster url to the specified field
     *
     * @returns {void}
     */
    const pasteUrl = () => {
      const url = `${window.location.origin}/document/files/${currentSelectedFile.value.document_id}`;
      switch (currentReferenceURLFieldId.value) {
        case 0:
          referenceUrlObject.value.reference_url_0 = url;
          break;
        case 1:
          referenceUrlObject.value.reference_url_1 = url;
          break;
        case 2:
          referenceUrlObject.value.reference_url_2 = url;
          break;
        case 3:
          referenceUrlObject.value.reference_url_3 = url;
          break;
        case 4:
          referenceUrlObject.value.reference_url_4 = url;
          break;
        default:
          break;
      }
    };

    /**
     * Filter form data
     *
     */
    const filterFormData = () => {
      formData.value = _.flow([
        Object.entries,
        (arr) =>
          arr.filter(([key, value]) => {
            if (key && typeof value === 'string') {
              return value.trim() !== '';
            }

            return true;
          }),
        Object.fromEntries,
      ])(formData.value);
    };

    /**
     * Handle save article
     *
     */
    const handleSubmitForm = () => {
      setPageLoading(true);
      errors.value = null;
      resetAlert();

      const draftId = formData.value.draft_id;
      formData.value.is_draft = ArticleTypes.PUBLIC;
      formData.value.directory_id = props.directory_id;
      formData.value = Object.assign(formData.value, referenceUrlObject.value);
      filterFormData();

      let sendRequest = {};

      if (draftId) {
        sendRequest = knowledgeApiService.updateArticle(
          formData.value,
          draftId
        );
      } else {
        sendRequest = knowledgeApiService.saveArticle(formData.value);
      }

      sendRequest
        .then(() => {
          if (formData.value.directory_id !== null) {
            window.location.href = `/knowledges/${formData.value.directory_id}`;
          } else {
            window.location.href = `/knowledges`;
          }
        })
        .catch((error) => {
          const responseData = error.response?.data;
          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
            referenceUrlErrors.value = responseData.data;
            return;
          }
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Handle save draft
     *
     */
    const handleSaveDraft = () => {
      setPageLoading(true);
      errors.value = null;
      resetAlert();

      formData.value.is_draft = ArticleTypes.DRAFT;
      formData.value.directory_id = props.directory_id;
      formData.value = Object.assign(formData.value, referenceUrlObject.value);
      filterFormData();

      let sendRequest = {};

      if (formData.value.draft_id) {
        const draftId = formData.value.draft_id;
        sendRequest = knowledgeApiService.updateArticleDraft(
          formData.value,
          draftId
        );
      } else {
        sendRequest = knowledgeApiService.saveArticleDraft(formData.value);
      }

      sendRequest
        .then((data) => {
          if (!formData.value.draft_id) {
            const returnData = data.data?.data;
            const draftId = returnData?.id;
            formData.value.draft_id = draftId;
          }
          const successMessage = i18n.global.t(
            'messages.knowledges.draft_save_successful'
          );
          setAlert('success', successMessage);
        })
        .catch((error) => {
          const responseData = error.response?.data;
          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
            referenceUrlErrors.value = responseData.data;
            return;
          }
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Populate reference URL fields
     *
     * @param {string} urls
     * @returns {void}
     */
    const populateReferenceUrls = (urls) => {
      const referenceUrls = JSON.parse(urls);
      const urlsObject = Object.entries(referenceUrls);
      const urlsObjectCount = urlsObject.length;
      if (urlsObjectCount > 1) {
        referenceURLFieldCount.value = urlsObjectCount;
      }
      urlsObject.forEach(([key, value]) => {
        const index = Number(key);
        switch (index) {
          case 0:
            referenceUrlObject.value.reference_url_0 = value;
            break;
          case 1:
            referenceUrlObject.value.reference_url_1 = value;
            break;
          case 2:
            referenceUrlObject.value.reference_url_2 = value;
            break;
          case 3:
            referenceUrlObject.value.reference_url_3 = value;
            break;
          case 4:
            referenceUrlObject.value.reference_url_4 = value;
            break;
          default:
            break;
        }
      });
    };

    /**
     * Populate form with draft data
     */
    const populateFormData = () => {
      // Check if has draft or article to edit
      if (knowledge.value) {
        formData.value.draft_id = knowledge.value.id;
        formData.value.owner_rio_id = knowledge.value.owner_rio_id;
        formData.value.owner_neo_id = knowledge.value.owner_neo_id;
        formData.value.created_rio_id = knowledge.value.created_rio_id;
        formData.value.directory_id = knowledge.value.directory_id;
        formData.value.type = knowledge.value.type;
        formData.value.task_title = knowledge.value.task_title;
        formData.value.contents = knowledge.value.contents;
        formData.value.is_draft = knowledge.value.is_draft;
        populateReferenceUrls(knowledge.value.urls);
      }
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      populateFormData();
    });

    return {
      formData,
      errors,
      alert,
      pasteUrl,
      pasteUrlConfirmation,
      handleBack,
      filterFormData,
      handleSubmitForm,
      handleSaveDraft,
      handleUpdatePublishedArticle,
      populateFormData,
      populateReferenceUrls,
      setPageLoading,
      setAlert,
      resetAlert,
      ArticleTypes,
      serviceName,
      pageLoading,
      referenceUrlErrors,
      referenceUrlObject,
      addReferenceField,
      documentListModalRef,
      referenceURLFieldCount,
      isPublishButtonDisabled,
      isSaveAsDraftButtonDisabled,
      showDocumentManagementModal,
      BpheroConfig,
      TinymceConfig,
      backUrl,
      updatePublishedArticle,
      setValidationErrors,
      parseContentError,
    };
  },
});
</script>
