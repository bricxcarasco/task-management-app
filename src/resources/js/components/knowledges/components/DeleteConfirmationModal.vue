<template>
  <div
    class="modal fade"
    id="knowledge-delete-confirmation"
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
            <h5 class="modal-title">{{ $t('headers.delete') }}</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" />

            <div v-if="type === KnowledgeTypes.FOLDER">
              <p class="text-center mb-4">
                {{ $t('paragraphs.delete_folder_confirmation') }}
              </p>
              <p class="text-center text-danger mb-0 px-4">
                {{ $t('paragraphs.delete_folder_additional_message') }}
              </p>
            </div>
            <div v-if="type === KnowledgeTypes.ARTICLE">
              <p class="text-center mb-4">
                {{ $t('paragraphs.delete_article_confirmation') }}
              </p>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-danger">
                {{ $t('buttons.delete') }}
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
import { objectifyForm } from '../../../utils/objectifyForm';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import KnowledgeTypes from '../../../enums/KnowledgeTypes';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'KnowledgeDeleteConfirmationModal',
  props: {
    type: {
      type: [Number, null],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const knowledgeApiService = new KnowledgeApiService();
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#knowledge-delete-confirmation');
      modal.querySelector('.btn-close').click();
    };

    /**
     * Event listener for knowledge delete
     *
     * @returns {void}
     */
    const submitDelete = async (event) => {
      const formData = objectifyForm(event.target);

      event.preventDefault();
      modalLoading.value = true;

      await knowledgeApiService
        .delete(formData.id)
        .then(() => {
          emit('reset-list');
        })
        .catch(() => {
          emit('set-alert', 'failed', i18n.global.t('alerts.error'));
          modalLoading.value = false;
          resetModal();
        });
    };

    return {
      submitDelete,
      resetModal,
      modalLoading,
      KnowledgeTypes,
    };
  },
});
</script>
