<template>
  <div class="preview">
    <table
      class="
        preview__table preview__table--receipt preview__table--no-border
        table
      "
    >
      <tbody>
        <tr>
          <td
            colspan="4"
            class="
              preview__table-supplier
              preview__table-supplier--receipt
              preview__table--bottom-line
            "
          >
            {{ supplierName }}
          </td>
          <td colspan="4" class="preview__table-supplier text-left">
            {{ $t('preview.you') }}
          </td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr>
          <td colspan="8" class="preview__header preview__header--receipt">
            {{ $t('preview.receipt') }}
          </td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr>
          <td
            colspan="2"
            class="
              preview__table-amount preview__table--bottom-line
              text-center
            "
          >
            <span class="text-justify">{{ $t('preview.amount') }}</span>
          </td>
          <td
            colspan="4"
            class="preview__table-price preview__table--bottom-line text-left"
          >
            {{ $t('preview.yen')
            }}{{ moneyFormat(form_data.receipt_amount ?? form_data.price) }}
            {{ $t('preview.tax_included') }}
          </td>
          <td colspan="2" class="preview__table--bottom-line"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table-h20">
          <td colspan="2" class="preview__table-label text-center">
            <span class="text-justify">{{ $t('preview.however') }}</span>
            <span class="float-right">：</span>
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.title }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table-h20">
          <td colspan="2" class="preview__table-label text-center">
            <span class="text-justify">{{ $t('preview.reference') }}</span>
            <span class="float-right">：</span>
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.refer_receipt_no }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table-row--receipt">
          <td colspan="2" class="preview__table-date text-right">
            {{ $t('preview.date') }}
          </td>
          <td colspan="4" class="text-left">
            {{ $t('preview.mentioned_above_received') }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table--blank-row">
          <td colspan="8"></td>
        </tr>
        <tr class="preview__table-row--receipt">
          <td colspan="2" class="preview__table-data--sm text-right">
            {{ $t('preview.company_name') }}
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.issuer_name }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table-row--receipt">
          <td colspan="2" class="preview__table-data--sm text-right">
            {{ $t('preview.business_address') }}
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.issuer_address }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table-row--receipt">
          <td colspan="2" class="preview__table-data--sm text-right">
            {{ $t('preview.tel_no') }}
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.issuer_tel }}
          </td>
          <td colspan="2"></td>
        </tr>
        <tr class="preview__table-row--receipt">
          <td colspan="2" class="preview__table-data--sm text-right">
            {{ $t('preview.office_registration_number') }}
          </td>
          <td colspan="4" class="text-left">
            {{ form_data.issuer_business_number }}
          </td>
          <td colspan="2"></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';

export default defineComponent({
  name: 'PreviewContentReceiptComponent',
  props: {
    form_data: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props) {
    const formData = ref(props.form_data);

    /**
     * Get supplier name
     *
     * @returns {string}
     */
    const supplierName = computed(
      () =>
        formData.value.supplier_name || formData.value.connected_supplier_name
    );

    /**
     * Price format
     */
    const moneyFormat = (value) =>
      Math.round(value)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return {
      moneyFormat,
      supplierName,
    };
  },
});
</script>
