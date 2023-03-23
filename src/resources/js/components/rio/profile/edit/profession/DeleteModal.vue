<template>
  <div
    class="modal fade"
    id="delete-profession-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitDeleteProfession" novalidate>
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12 text-center">
                {{ $t('messages.rio.delete_confirmation') }}
                <input type="hidden" name="id" />
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.delete') }}
            </button>
            <button
              class="btn btn-secondary btn-shadow btn-sm"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            >
              {{ $t('buttons.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'DeleteProfessionModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const rioProfileApiService = new RioProfileApiService();
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#delete-profession-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
    };

    /**
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const submitDeleteProfession = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      rioProfileApiService
        .deleteProfession(formData.id)
        .then(() => {
          emit('set-alert', 'success');
          emit('get-professions');

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
      submitDeleteProfession,
      modalLoading,
    };
  },
};
</script>
