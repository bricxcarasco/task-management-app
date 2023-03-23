<template>
  <div
    class="modal fade"
    id="knowledge-update-confirmation"
    tabindex="-1"
    role="dialog"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitDelete" novalidate>
          <div class="modal-header">
            <h5 class="modal-title">
              {{ $t('headers.article_editing') }}
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" />

            <div>
              <p class="text-center mb-4">
                {{ $t('paragraphs.update_article_confirmation') }}
              </p>
            </div>
            <div class="text-center mt-4">
              <button
                type="button"
                @click="handleUpdate()"
                class="btn btn-primary"
              >
                {{ $t('buttons.update') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'KnowledgeUpdateConfirmationModal',
  props: {
    formData: {
      type: [Array, Object],
      required: true,
    },
    referenceUrlObject: {
      type: [Array, Object],
      required: false,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const knowledgeApiService = new KnowledgeApiService();
    const modalLoading = ref(false);
    const formData = ref(props.formData);
    const referenceUrlObject = ref(props.referenceUrlObject);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#knowledge-update-confirmation');
      modal.querySelector('.btn-close').click();
    };

    /**
     * Remove null from reference url object
     *
     * @returns {void}
     */
    const filterReferenceUrls = () => {
      Object.keys(referenceUrlObject.value).forEach((key) => {
        if (referenceUrlObject.value[key] === null) {
          delete referenceUrlObject.value[key];
        }
      });
    };

    /**
     * Event listener for knowledge update
     *
     * @returns {void}
     */
    const handleUpdate = () => {
      modalLoading.value = true;
      formData.value.updatePublishedArticle = true;
      filterReferenceUrls();
      formData.value = Object.assign(formData.value, referenceUrlObject.value);

      knowledgeApiService
        .updateArticle(formData.value, parseInt(formData.value.draft_id, 10))
        .then(() => {
          // redirect to show page with flash message
          window.location.replace(
            `/knowledges/articles/${formData.value.draft_id}`
          );
        })
        .catch((error) => {
          const responseData = error.response?.data;

          if (responseData?.status_code === 422) {
            emit('set-validation-errors', responseData.data);
          }

          emit('set-alert', 'failed', i18n.global.t('alerts.error'));
          modalLoading.value = false;
          resetModal();
        });
    };

    return {
      handleUpdate,
      resetModal,
      modalLoading,
    };
  },
});
</script>
