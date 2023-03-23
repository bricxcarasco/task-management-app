<template>
  <!-- eslint-disable -->
  <tr v-for="(product, index) in products" :key="index">
    <th>
      {{ product.name }}
    </th>
    <td class="text-end">
      {{
        parseFloat(product.unit_price ? product.unit_price : 0)
          .toFixed()
          .replace(/\d(?=(\d{3})+\.)/g, '$&,')
      }}{{ $t('labels.quotation_circle') }}
      <button
        type="button"
        class="btn btn--link p-0"
        @click="$emit('delete-purchase-order-product', index)"
      >
        <i class="ai-trash-2"></i>
      </button>
      <button
        type="button"
        v-if="products.length < 10"
        class="btn btn--link p-0"
        @click="$emit('duplicate-purchase-order-product', product)"
      >
        <i class="ai-copy"></i>
      </button>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'InvoiceProducts',
  emits: ['delete-purchase-order-product', 'duplicate-purchase-order-product'],
  props: {
    products: {
      type: [Array, Object],
      required: true,
    },
  },
};
</script>
