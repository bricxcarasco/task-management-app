<template>
  <div
    class="modal fade"
    id="form-delete-confirmation"
    tabindex="-1"
    role="dialog"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitDeleteForm" novalidate>
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('labels.delete_form') }}</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-center mb-0">
              {{ $t('labels.do_you_want_to_delete_from_this_form') }}
            </p>
            <div class="text-center mt-4">
              <input type="hidden" name="id" />
              <button type="submit" class="btn btn-danger">
                {{ $t('buttons.delete') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import QuotationApiService from '../../../api/forms/quotations';
import InvoiceApiService from '../../../api/forms/invoices';
import DeliverySlipApiService from '../../../api/forms/delivery-slips';
import PurchaseOrderApiService from '../../../api/forms/purchase-orders';
import ReceiptApiService from '../../../api/forms/receipts';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import i18n from '../../../i18n';
import FormTypes from '../../../enums/FormTypes';

export default defineComponent({
  name: 'FormDeleteConfirmationModal',
  props: {
    type: {
      type: [String, Number, null],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const quotationApiService = new QuotationApiService();
    const invoiceApiService = new InvoiceApiService();
    const deliverySlipApiService = new DeliverySlipApiService();
    const purchaseOrderApiService = new PurchaseOrderApiService();
    const receiptApiService = new ReceiptApiService();
    const modalLoading = ref(false);
    const formType = ref(props.type);
    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#form-delete-confirmation');
      modal.querySelector('.btn-close').click();
    };

    const deleteApi = computed(() => {
      switch (formType.value) {
        case FormTypes.QUOTATION:
          return quotationApiService;
        case FormTypes.PURCHASE_ORDER:
          return purchaseOrderApiService;
        case FormTypes.DELIVERY_SLIP:
          return deliverySlipApiService;
        case FormTypes.INVOICE:
          return invoiceApiService;
        case FormTypes.RECEIPT:
          return receiptApiService;
        default:
          return quotationApiService;
      }
    });

    /**
     * Event listener for form delete
     *
     * @returns {void}
     */
    const submitDeleteForm = async (event) => {
      event.preventDefault();
      modalLoading.value = true;
      const formData = objectifyForm(event.target);

      await deleteApi.value
        .deleteForms(formData.id)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('alerts.form_has_been_deleted')
          );
          emit('get-lists');

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response;

          if (responseData.data.status_code === 404) {
            emit('set-alert', 'failed', i18n.global.t('alerts.not_found'));
          }

          if (responseData.status === 403) {
            emit(
              'set-alert',
              'failed',
              i18n.global.t('alerts.authentication_required')
            );
          }

          emit('page-loading', false);
          resetModal();
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitDeleteForm,
      resetModal,
      modalLoading,
      formType,
    };
  },
});
</script>
