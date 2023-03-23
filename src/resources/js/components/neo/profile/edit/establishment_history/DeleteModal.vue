<template>
  <base-delete-modal
    :id="modalId"
    :modalLoading="modalLoading"
    :show="showModal"
    ref="modalRef"
    @confirm-delete="submitDeleteHistory"
  >
    <input type="hidden" name="id" value="" />
  </base-delete-modal>
</template>

<script>
import { ref } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import BaseDeleteModal from '../components/BaseDeleteModal.vue';
import ApiService from '../../../../../api/neo/profile';

export default {
  name: 'DeleteAwardModal',
  components: {
    BaseDeleteModal,
  },
  setup(props, { emit }) {
    const apiService = new ApiService();
    const modalLoading = ref(false);
    const showModal = ref(false);
    const modalRef = ref(null);
    const modalId = 'delete-history-modal';

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector(`#${modalId}`);
      modal.querySelector('.btn-close').click();
    };

    /**
     * Event listener for add award form submit
     *
     * @returns {void}
     */
    const submitDeleteHistory = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      apiService
        .deleteHistory(formData.id)
        .then(() => {
          emit('set-alert', 'success');
          emit('get-histories');

          resetModal();
        })
        .catch(() => {
          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitDeleteHistory,
      modalLoading,
      showModal,
      modalRef,
      modalId,
    };
  },
};
</script>
