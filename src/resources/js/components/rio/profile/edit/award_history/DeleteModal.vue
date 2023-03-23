<template>
  <base-delete-modal
    :id="modalId"
    :modalLoading="modalLoading"
    :show="showModal"
    ref="modalRef"
    @confirm-delete="submitDeleteAward"
  >
    <input type="hidden" name="id" value="1" />
  </base-delete-modal>
</template>

<script>
import { ref } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import BaseDeleteModal from '../components/BaseDeleteModal.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'DeleteAwardModal',
  components: {
    BaseDeleteModal,
  },
  setup(props, { emit }) {
    const rioProfileApiService = new RioProfileApiService();
    const modalLoading = ref(false);
    const showModal = ref(false);
    const modalRef = ref(null);
    const modalId = 'delete-award-modal';

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
    const submitDeleteAward = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      rioProfileApiService
        .deleteAwardHistory(formData.id)
        .then(() => {
          emit('set-alert', 'success');
          emit('get-awards');

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
      submitDeleteAward,
      modalLoading,
      showModal,
      modalRef,
      modalId,
    };
  },
};
</script>
