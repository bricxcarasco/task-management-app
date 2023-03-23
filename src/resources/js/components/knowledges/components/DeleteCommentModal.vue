<template>
  <div
    class="modal fade"
    id="comment-delete-confirmation"
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
            <h5 class="modal-title">{{ $t('headers.delete_comment') }}</h5>
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
                {{ $t('paragraphs.delete_comment_confirmation') }}
              </p>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-danger">
                {{ $t('buttons.delete_comment') }}
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
import CommentsApiService from '../../../api/knowledges/comments';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'DeleteCommentModal',
  props: {},
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const commentsApiService = new CommentsApiService();
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#comment-delete-confirmation');
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

      await commentsApiService
        .deleteComment(formData.id)
        .then(() => {
          emit('set-alert', 'success', i18n.global.t('alerts.deleted_comment'));
          resetModal();
          emit('refresh');
        })
        .catch(() => {
          emit('set-alert', 'failed', i18n.global.t('alerts.error'));
          resetModal();
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      modalLoading,
      resetModal,
      submitDelete,
    };
  },
});
</script>
