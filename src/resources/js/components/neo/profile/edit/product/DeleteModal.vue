<template>
  <div
    class="modal fade"
    id="delete-product-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitDeleteProduct" novalidate>
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
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'DeleteProductModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const neoProfileApiService = new NeoProfileApiService();
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#delete-product-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
    };

    /**
     * Event listener for add product form submit
     *
     * @returns {void}
     */
    const submitDeleteProduct = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      neoProfileApiService
        .deleteProduct(formData.id)
        .then(() => {
          emit('set-alert', 'success');
          emit('get-products');

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
      submitDeleteProduct,
      modalLoading,
    };
  },
};
</script>
