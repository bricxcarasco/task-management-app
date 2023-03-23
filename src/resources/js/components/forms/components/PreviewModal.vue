<template>
  <div
    class="modal fade"
    id="form-preview"
    tabindex="-1"
    role="dialog"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.form_preview') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="m-3 text-center">
            <div id="frame">
              <!-- Preview content -->
              <preview-content-quotation
                v-if="form_data.type === FormTypes.QUOTATION"
                :form_data="form_data"
                :product_price_with_taxes="product_price_with_taxes"
                :logo="logo"
              />
              <preview-content-purchase-order
                v-if="form_data.type === FormTypes.PURCHASE_ORDER"
                :form_data="form_data"
                :product_price_with_taxes="product_price_with_taxes"
                :logo="logo"
              />
              <preview-content-delivery-slip
                v-if="form_data.type === FormTypes.DELIVERY_SLIP"
                :form_data="form_data"
                :product_price_with_taxes="product_price_with_taxes"
                :logo="logo"
              />
              <preview-content-invoice
                v-if="form_data.type === FormTypes.INVOICE"
                :form_data="form_data"
                :product_price_with_taxes="product_price_with_taxes"
                :logo="logo"
              />
              <preview-content-receipt
                v-if="form_data.type === FormTypes.RECEIPT"
                :form_data="form_data"
              />
            </div>

            <img src="#" class="js-form-preview-img d-none" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import PreviewContentQuotation from './PreviewContentQuotation.vue';
import PreviewContentPurchaseOrder from './PreviewContentPurchaseOrder.vue';
import PreviewContentDeliverySlip from './PreviewContentDeliverySlip.vue';
import PreviewContentInvoice from './PreviewContentInvoice.vue';
import PreviewContentReceipt from './PreviewContentReceipt.vue';
import FormTypes from '../../../enums/FormTypes';

export default defineComponent({
  name: 'PreviewModalComponent',
  props: {
    form_data: {
      type: [Array, Object],
      required: true,
    },
    product_price_with_taxes: {
      type: [Array, Object],
      required: false,
    },
    logo: {
      type: [String, null],
      required: false,
    },
  },
  components: {
    SectionLoader,
    PreviewContentQuotation,
    PreviewContentPurchaseOrder,
    PreviewContentDeliverySlip,
    PreviewContentInvoice,
    PreviewContentReceipt,
  },
  setup() {
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show form preview modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide form preview modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
      search.value = '';
    };

    /**
     * Reset form preview modal
     *
     * @returns {void}
     */
    const handleResetFormPreview = () => {
      const imgSelector = document.querySelector('.js-form-preview-img');
      const frameSelector = document.querySelector('#frame');

      frameSelector.classList.remove('d-none');
      imgSelector.setAttribute('src', '#');
      imgSelector.classList.add('d-none');
    };

    /**
     * On mounted methods
     */
    onMounted(() => {
      /**
       * Trigger on modal close
       */
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        handleResetFormPreview();
      });
    });

    return {
      modalRef,
      modal,
      show,
      hide,
      modalLoading,
      FormTypes,
    };
  },
});
</script>
