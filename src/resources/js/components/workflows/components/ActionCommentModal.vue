<template>
  <div
    class="modal fade"
    id="workflow-action-comment-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            ■ {{ $t('labels.workflow_action_comment') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="closeModal"
          ></button>
        </div>
        <div class="modal-body">
          <div id="comment-div" class="mb-3 text-center"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'TaskFormModal',
  setup() {
    const modalLoading = ref(false);
    const modalRef = ref(null);

    /**
     * updating comment modal
     */
    const updateCommentModal = (comment) => {
      const commentDiv = document.querySelector('#comment-div');
      commentDiv.innerHTML = comment;
    };

    /**
     * Closes modal
     *
     * @returns {void}
     */
    const closeModal = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    return {
      modalLoading,
      closeModal,
      modalRef,
      updateCommentModal,
    };
  },
});
</script>
