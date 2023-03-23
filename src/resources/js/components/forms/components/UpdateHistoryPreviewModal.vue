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
          <input type="hidden" id="history_id" />
        </div>
        <div class="modal-body">
          <div class="m-3 text-center">
            <div id="frame">
              <!-- Preview content -->
              <preview-content-quotation
                v-if="formHistory.type === FormTypes.QUOTATION"
                :form_data="formHistory"
                :product_price_with_taxes="productPriceWithTaxes"
                :logo="logoDisplay"
              />
              <preview-content-purchase-order
                v-if="formHistory.type === FormTypes.PURCHASE_ORDER"
                :form_data="formHistory"
                :product_price_with_taxes="productPriceWithTaxes"
                :logo="logoDisplay"
              />
              <preview-content-delivery-slip
                v-if="formHistory.type === FormTypes.DELIVERY_SLIP"
                :form_data="formHistory"
                :product_price_with_taxes="productPriceWithTaxes"
                :logo="logoDisplay"
              />
              <preview-content-invoice
                v-if="formHistory.type === FormTypes.INVOICE"
                :form_data="formHistory"
                :product_price_with_taxes="productPriceWithTaxes"
                :logo="logoDisplay"
              />
              <preview-content-receipt
                v-if="formHistory.type === FormTypes.RECEIPT"
                :form_data="formHistory"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import PreviewContentQuotation from './PreviewContentQuotation.vue';
import PreviewContentPurchaseOrder from './PreviewContentPurchaseOrder.vue';
import PreviewContentDeliverySlip from './PreviewContentDeliverySlip.vue';
import PreviewContentInvoice from './PreviewContentInvoice.vue';
import PreviewContentReceipt from './PreviewContentReceipt.vue';
import FormTypes from '../../../enums/FormTypes';
import FormApiService from '../../../api/forms/forms';

export default defineComponent({
  name: 'UpdateHistoryPreviewModalComponent',
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
    const formHistory = ref([]);
    const productPriceWithTaxes = ref([]);
    const logoDisplay = ref(null);
    const formApiService = new FormApiService();

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * Reset form preview modal
     *
     * @returns {void}
     */
    const handleResetFormPreview = () => {
      const frameSelector = document.querySelector('#frame');
      frameSelector.classList.remove('d-none');
    };

    /**
     * Calculate and set prices
     */
    const calculation = (products) => {
      let tax10 = 0;
      let tax8 = 0;
      let total = 0;
      let overall = 0;

      products.forEach((product) => {
        switch (parseInt(product.tax_distinction, 3)) {
          case 1:
            tax10 += parseFloat(product.amount) * 0.1;
            break;
          case 2:
            tax8 += parseFloat(product.amount) * 0.08;
            break;
          default:
            break;
        }

        total += product.quantity * product.unit_price;
      });

      tax10 = Math.floor(parseFloat(tax10));
      tax8 = Math.floor(parseFloat(tax8));
      overall = total + tax10 + tax8 ?? 0;

      productPriceWithTaxes.value = {
        subTotal: total,
        totalWithGST: tax10,
        totalWitConsumptionTax: tax8,
        total: overall,
      };
    };

    const getUpdateHistoryDetails = async () => {
      setLoading(true);
      const id = document.getElementById('history_id').value;

      try {
        const getDetailsApi = await formApiService.getUpdateHistoryDetails(id);
        const getDetailsResponseData = getDetailsApi.data;
        formHistory.value = getDetailsResponseData
          ? getDetailsResponseData.formHistory
          : [];
        calculation(getDetailsResponseData.formHistory.products);
        logoDisplay.value =
          getDetailsResponseData &&
          getDetailsResponseData.formHistory.issuer_image
            ? getDetailsResponseData.formHistory.issuer_image
            : null;
      } catch (error) {
        formHistory.value = [];
        productPriceWithTaxes.value = [];
        logoDisplay.value = null;

        throw error;
      } finally {
        setLoading(false);
      }
    };

    /**
     * On mounted methods
     */
    onMounted(() => {
      /**
       * Trigger on modal open
       */
      modalRef.value.addEventListener('shown.bs.modal', () => {
        getUpdateHistoryDetails();
      });

      /**
       * Trigger on modal close
       */
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        handleResetFormPreview();
      });
    });

    return {
      modalRef,
      modalLoading,
      FormTypes,
      formHistory,
      productPriceWithTaxes,
      logoDisplay,
      getUpdateHistoryDetails,
    };
  },
});
</script>
