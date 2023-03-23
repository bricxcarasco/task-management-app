<template>
  <div class="preview">
    <h3 class="preview__header">{{ $t('preview.delivery_slip') }}</h3>

    <div class="preview__content">
      <div class="preview__section position-relative">
        <table class="preview__table table preview__table--no-border">
          <tbody>
            <tr>
              <td colspan="9" class="text-left">
                {{ form_data.zipcode }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-left">
                {{ form_data.address }}
              </td>
              <td colspan="3" class="text-left">
                {{ $t('preview.delivery_slip_no') }}：
              </td>
              <td colspan="4" class="text-left">{{ form_data.form_no }}</td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="16"></td>
            </tr>
            <tr>
              <td
                colspan="7"
                rowspan="2"
                class="preview__table-supplier preview__table--bottom-line p-0"
              >
                {{ supplierName }}
              </td>
              <td
                colspan="2"
                rowspan="2"
                class="
                  preview__table-supplier preview__table--bottom-line
                  p-0
                  nowrap
                "
              >
                {{ $t('preview.you') }}
              </td>
              <td colspan="3" class="text-left v-align-top">
                {{ form_data.issuer_name }}
              </td>
              <td colspan="4" rowspan="3">
                <img
                  class="preview__table-image"
                  v-if="logo"
                  :src="logo"
                  @onerror="handleErrorImage"
                />
              </td>
            </tr>
            <tr>
              <td colspan="5" class="text-left v-align-top">
                {{ form_data.issuer_department_name }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">
                {{ $t('preview.purchase_order_message') }}
              </td>
              <td colspan="7" class="text-left v-align-top">
                {{ form_data.issuer_address }}
              </td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="16"></td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="16"></td>
            </tr>
            <tr>
              <td
                colspan="3"
                class="preview__table--bottom-line p-0 text-center"
              >
                <span class="text-justify nowrap">{{
                  $t('preview.subject')
                }}</span>
                <span class="float-right">：</span>
              </td>
              <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                {{ form_data.title }}
              </td>
              <td colspan="3" class="text-left nowrap">ＴＥＬ：</td>
              <td colspan="4" class="text-left">{{ form_data.issuer_tel }}</td>
            </tr>
            <tr>
              <td
                colspan="3"
                class="preview__table--bottom-line p-0 text-center"
              >
                <span class="text-justify nowrap">
                  {{ $t('preview.offer_amount') }}
                </span>
                <span class="float-right">：</span>
              </td>
              <td
                colspan="6"
                class="preview__table--bottom-line p-0 text-center font-bold"
              >
                {{ $t('preview.yen') }}
                {{ moneyFormat(product_price_with_taxes.total) }}
                {{ $t('preview.tax_included') }}
              </td>
              <td colspan="3" class="text-left nowrap">ＦＡＸ：</td>
              <td colspan="4" class="text-left">{{ form_data.issuer_fax }}</td>
            </tr>
            <tr>
              <td
                colspan="3"
                class="preview__table--bottom-line p-0 text-center"
              >
                <span class="text-justify nowrap">{{
                  $t('preview.delivery_location')
                }}</span>
                <span class="float-right">：</span>
              </td>
              <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                {{ form_data.delivery_address }}
              </td>
              <td colspan="3" class="text-left nowrap">
                {{ $t('preview.office_registration_number') }}：
              </td>
              <td colspan="4" class="text-left">
                {{ form_data.issuer_business_number }}
              </td>
            </tr>
            <tr>
              <td
                colspan="3"
                class="preview__table--bottom-line p-0 text-center"
              >
                <span class="text-justify nowrap">{{
                  $t('preview.delivery_deadline')
                }}</span>
                <span class="float-right">：</span>
              </td>
              <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                {{ form_data.delivery_date }}
              </td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="16"></td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="16"></td>
            </tr>
          </tbody>
        </table>

        <div class="preview__stamp">
          <div class="preview__stamp-box"></div>
          <div class="preview__stamp-box" style="right: 2px"></div>
          <div class="preview__stamp-box" style="right: 4px"></div>
        </div>
      </div>

      <hr />

      <div class="preview__section">
        <table class="preview__table preview__table--border table">
          <thead class="preview__table-th">
            <tr>
              <td class="w-10">NO.</td>
              <td class="w-25">{{ $t('preview.product_service_name') }}</td>
              <td>{{ $t('preview.unit_price') }}</td>
              <td>{{ $t('preview.quantity') }}</td>
              <td>{{ $t('preview.amount') }}</td>
              <td>{{ $t('preview.tax') }}</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(product, key) in form_data.products" :key="key">
              <td class="text-center">
                {{ key + 1 }}
              </td>
              <td class="text-left">
                {{ product.name }}
              </td>
              <td class="text-right">
                {{ $t('preview.yen') }}
                {{ moneyFormat(product.unit_price) }}
              </td>
              <td class="text-center">
                {{ product.quantity }}
              </td>
              <td class="text-right">
                {{ $t('preview.yen') }}
                {{ getAmount(product) }}
              </td>
              <td class="text-center">
                {{ FormTaxDistinction[product.tax_distinction] }}
              </td>
            </tr>

            <tr>
              <td class="border-none"></td>
              <td colspan="2" class="preview__table--border-double text-center">
                {{ $t('preview.total') }}
              </td>
              <td class="text-center preview__table--border-double"></td>
              <td colspan="2" class="preview__table--border-double text-center">
                {{ $t('preview.yen') }}
                {{ moneyFormat(product_price_with_taxes.subTotal) }}
              </td>
            </tr>
            <tr>
              <td colspan="2" class="border-none"></td>
              <td colspan="2" class="left-border-none text-center">
                {{ $t('preview.10_consumption_tax') }}
              </td>
              <td colspan="2" class="text-center">
                {{ $t('preview.yen') }}
                {{ moneyFormat(product_price_with_taxes.totalWithGST) }}
              </td>
            </tr>
            <tr>
              <td colspan="2" class="border-none"></td>
              <td colspan="2" class="left-border-none text-center">
                {{ $t('preview.8_consumption_tax') }}
              </td>
              <td colspan="2" class="text-center">
                {{ $t('preview.yen') }}
                {{
                  moneyFormat(product_price_with_taxes.totalWitConsumptionTax)
                }}
              </td>
            </tr>
            <tr>
              <td class="border-none"></td>
              <td
                colspan="1"
                class="preview__table-remarks border-none text-left p-0"
              >
                【 {{ $t('preview.delivery_address') }} 】
              </td>
              <td
                colspan="2"
                class="
                  left-border-none
                  preview__table--border-double
                  text-center
                "
              >
                {{ $t('preview.grand_total') }}
              </td>
              <td colspan="2" class="preview__table--border-double text-center">
                {{ $t('preview.yen') }}
                {{ moneyFormat(product_price_with_taxes.total) }}
              </td>
            </tr>

            <tr class="preview__table--blank-row">
              <td colspan="6" class="border-none"></td>
            </tr>
            <tr>
              <td colspan="6" class="preview__table-paragraph border-none">
                {{ form_data.delivery_address }}
              </td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="6" class="border-none"></td>
            </tr>
            <tr>
              <td class="border-none"></td>
              <td
                colspan="1"
                class="preview__table-remarks border-none text-left p-0"
              >
                【 {{ $t('preview.remarks') }} 】
              </td>
            </tr>
            <tr class="preview__table--blank-row">
              <td colspan="6" class="border-none"></td>
            </tr>
            <tr>
              <td colspan="6" class="preview__table-paragraph border-none">
                {{ form_data.remarks }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import FormTaxDistinction from '../../../enums/FormQuotationProductTaxClassification';
import FormTypes from '../../../enums/FormTypes';
import BpheroConfig from '../../../config/bphero';

export default defineComponent({
  name: 'PreviewContentDeliverySlipComponent',
  props: {
    form_data: {
      type: [Array, Object],
      required: true,
    },
    product_price_with_taxes: {
      type: [Array, Object],
      required: true,
    },
    logo: {
      type: [String, null],
      required: false,
    },
  },
  setup(props) {
    const formData = ref(props.form_data);

    /**
     * Get supplier name
     *
     * @returns {string}
     */
    const supplierName = computed(() => formData.value.supplier_name);

    /**
     * Price format
     */
    const moneyFormat = (value) =>
      Math.round(value)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    /**
     * Get product amount
     *
     * @returns {Object} product
     */
    const getAmount = (product) => {
      const price = product.total || product.amount;

      return moneyFormat(price);
    };

    /**
     * Handle image render error
     */
    const handleErrorImage = () => {
      const defaultImgSelector = document.querySelector(
        '.preview__table-image'
      );
      defaultImgSelector.src = `${
        BpheroConfig.DEFAULT_IMG
      }?${new Date().getTime()}`;
    };

    return {
      FormTaxDistinction,
      FormTypes,
      moneyFormat,
      handleErrorImage,
      supplierName,
      getAmount,
    };
  },
});
</script>
