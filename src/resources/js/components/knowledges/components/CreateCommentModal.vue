<template>
  <div
    class="modal fade"
    id="create-comment-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit.prevent="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">
              {{ headerText }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" v-model="formData.id" />
            <div class="row">
              <!-- Comment -->
              <div class="mb-3">
                <textarea
                  rows="8"
                  class="form-control"
                  :class="errors?.comment != null ? 'is-invalid' : ''"
                  name="comment"
                  v-model="formData.comment"
                ></textarea>
                <base-validation-error :errors="errors?.comment" />
                <div class="d-flex justify-content-between">
                  <div></div>
                  <div>
                    <label class="form-label text-right"
                      >{{ wordCount }}/500</label
                    >
                  </div>
                </div>
              </div>
              <div class="text-right"></div>
            </div>
            <!-- Modal Actions -->
            <div class="text-center mt-4">
              <button class="btn btn-success btn-shadow btn-sm" type="submit">
                {{ buttonText }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import CommentsApiService from '../../../api/knowledges/comments';
import BaseValidationError from '../../base/BaseValidationError.vue';
import i18n from '../../../i18n';

export default {
  name: 'CreateFolderModal',
  props: {
    article: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const apiService = new CommentsApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const buttonText = ref(null);
    const headerText = ref(null);
    const flashText = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      errors.value = null;
      formData.value = {};
    };

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Get word count
     *
     * @returns {void}
     */
    const wordCount = computed(() => {
      if (formData.value.comment) {
        return formData.value.comment.length;
      }

      return 0;
    });

    /**
     * Show select document modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide select document modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
      currentDocument.value.length = 0;
    };

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * Check if modal is in edit state
     *
     * @returns {bool}
     */
    const isEdit = () =>
      formData.value.id !== '' &&
      formData.value.id !== null &&
      formData.value.id !== undefined;

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Update modal title depending on status
     *
     * @returns {void}
     */
    const updateModal = () => {
      headerText.value = isEdit()
        ? i18n.global.t('headers.edit_comment')
        : i18n.global.t('headers.comment');
      buttonText.value = isEdit()
        ? i18n.global.t('buttons.save_comment')
        : i18n.global.t('buttons.post_a_comment');
      flashText.value = isEdit()
        ? i18n.global.t('messages.knowledges.comment_saved')
        : i18n.global.t('messages.knowledges.comment_posted');
    };

    /**
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      formData.value.knowledge_id = props.article.id;
      const commentAction = !isEdit()
        ? apiService.saveComment(formData.value)
        : apiService.editComment(formData.value.id, formData.value);
      emit('reset-alert');

      // Handle responses
      commentAction
        .then(() => {
          emit('refresh');

          emit('set-alert', 'success', flashText.value);

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response?.data;
          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          setAlert('failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        updateModel();
        updateModal();
      });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
    });

    return {
      formData,
      formRef,
      show,
      hide,
      submitForm,
      resetModal,
      setLoading,
      errors,
      modalLoading,
      modalRef,
      wordCount,
      buttonText,
      headerText,
    };
  },
};
</script>
