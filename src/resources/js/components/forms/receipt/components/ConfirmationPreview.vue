<template>
  <div>
    <!-- Preview modal -->
    <preview-modal :form_data="form" />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
        break-word
      "
    >
      <div class="row">
        <page-loader :show="pageLoading" />
        <div class="col-12 col-md-9 offset-md-3">
          <div
            class="
              d-flex
              align-items-center
              justify-content-between
              border-bottom
            "
          >
            <a href="#" @click="handleClickEdit" class="btn btn-link">
              <i class="ai-arrow-left"></i>
            </a>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <button
              type="button"
              class="btn btn-link"
              data-bs-toggle="modal"
              data-bs-target="#form-preview"
            >
              {{ $t('buttons.form_preview') }}
            </button>
          </div>
          <div class="d-flex align-items-center justify-content-between"></div>

          <!-- Form information -->
          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.basic_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>{{ $t('labels.receipt_title') }}</th>
                  <td>{{ formData.title }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.suppliers') }}</th>
                  <td>{{ formData.supplier_name }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.date_of_issue') }}</th>
                  <td>{{ formData.issue_date }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.receipt_date') }}</th>
                  <td>{{ formData.receipt_date }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.receipt_amount') }}</th>
                  <td>
                    {{ moneyFormat(formData.receipt_amount) }}
                    {{ $t('labels.yen') }}
                    {{ $t('labels.tax_included') }}
                  </td>
                </tr>
                <tr>
                  <th>{{ $t('labels.refer_receipt_number') }}</th>
                  <td>{{ formData.refer_receipt_no }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Issuer information -->
          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.issuer_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>{{ $t('labels.store_and_trade_name') }}</th>
                  <td>{{ form.issuer_name }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.invoice_address') }}</th>
                  <td>{{ form.issuer_address }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.tel') }}</th>
                  <td>{{ form.issuer_tel }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.business_number') }}</th>
                  <td>{{ form.issuer_business_number }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 text-center">
            <button
              type="button"
              class="btn btn-dark btnLeft"
              @click="handleClickEdit"
            >
              {{ $t('buttons.fix') }}
            </button>
            <button
              type="button"
              class="btn btn-primary btnRight"
              @click="handleClickSubmit"
            >
              {{ $t('buttons.next') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import ReceiptsApiService from '../../../../api/forms/receipts';
import FormOperation from '../../../../enums/FormOperationTypes';
import FormQuotationProductTaxClassification from '../../../../enums/FormQuotationProductTaxClassification';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import PreviewModal from '../../components/PreviewModal.vue';

export default defineComponent({
  name: 'ReceiptConfirmationPreview',
  props: {
    form: {
      type: [Array, Object],
    },
    form_basic_setting: {
      type: [Array, Object],
    },
    operation: {
      type: String,
    },
  },
  components: {
    PageLoader,
    PreviewModal,
  },
  setup(props, { emit }) {
    const formData = ref(props.form);
    const basicSetting = ref(props.form_basic_setting);
    const receiptsApiService = new ReceiptsApiService();
    const pageLoading = ref(false);

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Handle submit create receipt form
     *
     * @param {string} productId
     */
    const handleClickSubmit = async (event) => {
      event.preventDefault();

      const data = { ...formData.value };
      setPageLoading(true);

      if (props.operation === FormOperation.EDIT) {
        emit('update-receipt', data);
        return;
      }

      // Handle responses
      await receiptsApiService
        .confirmReceipt(data)
        .then(() => {
          window.location.href = `/forms/receipts`;
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Handle edit invoice form
     *
     * @param {string} productId
     */
    const handleClickEdit = (event) => {
      event.preventDefault();
      formData.value.mode = 'edit';
      emit('switch-receipt-create-form', formData.value);
    };

    /**
     * Price format
     */
    const moneyFormat = (value) =>
      Math.round(value)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return {
      FormQuotationProductTaxClassification,
      formData,
      handleClickSubmit,
      handleClickEdit,
      basicSetting,
      setPageLoading,
      pageLoading,
      moneyFormat,
    };
  },
});
</script>
