<template>
  <div
    class="modal fade"
    id="delete-registered-product-modal"
    aria-hidden="true"
    tabindex="-1"
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
              <div class="col-12 text-center">
                {{ $t('headers.delete_target_product') }}
              </div>
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
            <div class="row">
              <div class="col-12 text-center">
                {{ $t('messages.classifieds.delete_product_confirm') }}
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.confirm_delete_product') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import ClassifiedSalesApiService from '../../../api/classifieds/sales';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';

export default {
  name: 'DeleteRegisteredProductModal',
  props: {
    product_id: {
      type: [Number, null],
      default: null,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const classifiedSalesApiService = new ClassifiedSalesApiService();
    const modalLoading = ref(false);
    const modalRef = ref(null);
    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    /**
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      // Handle responses
      classifiedSalesApiService
        .deleteRegisteredProduct(props.product_id)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.classifieds.deleted_the_prodcut')
          );
          emit('get-products');

          resetModal();
        })
        .catch(() => {
          emit('set-alert', 'failed');
          emit('get-products');

          resetModal();
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitForm,
      resetModal,
      modalLoading,
      modalRef,
      modal,
    };
  },
};
</script>
