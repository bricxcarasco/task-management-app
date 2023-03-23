<template>
  <base-delete-modal
    :id="modalId"
    :modalLoading="modalLoading"
    :show="showModal"
    ref="modalRef"
    @confirm-delete="submitDeleteEmail"
  >
    <input type="hidden" name="id" value="1" />
  </base-delete-modal>
</template>

<script>
import { ref } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import BaseDeleteModal from '../components/BaseDeleteModal.vue';
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'DeleteEmailModal',
  components: {
    BaseDeleteModal,
  },
  setup(props, { emit }) {
    const neoProfileApiService = new NeoProfileApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const showModal = ref(false);
    const modalRef = ref(null);
    const modalId = 'delete-email-modal';

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector(`#${modalId}`);
      modal.querySelector('.btn-close').click();
      errors.value = null;
    };

    /**
     * Event listener for add email form submit
     *
     * @returns {void}
     */
    const submitDeleteEmail = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      errors.value = null;
      modalLoading.value = true;
      emit('reset-alert');
      emit('set-page-loading', true);

      // Execute API call
      neoProfileApiService
        .deleteEmail(formData.id)
        .then(() => {
          emit('set-alert', 'success');
          emit('get-emails');

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          emit('set-page-loading', false);
          modalLoading.value = false;
        });
    };

    return {
      submitDeleteEmail,
      modalLoading,
      showModal,
      modalRef,
      modalId,
    };
  },
};
</script>
