<template>
  <!-- eslint-disable -->
  <tr v-for="(product, index) in products" :key="index">
    <th>
      {{ product.name }}
    </th>
    <td class="text-end">
      <span v-if="product.unit_price !== null">
        {{
          parseFloat(product.unit_price)
            .toFixed()
            .replace(/\d(?=(\d{3})+$)/g, '$&,')
        }}
      </span>
      <span v-else>0</span>
      {{ $t('labels.quotation_circle') }}
      <button
        type="button"
        class="btn btn--link p-0"
        @click="$emit('delete-quotation-product', index)"
      >
        <i class="ai-trash-2"></i>
      </button>
      <button
        type="button"
        v-if="products.length < 10"
        class="btn btn--link p-0"
        @click="$emit('duplicate-quotation-product', product)"
      >
        <i class="ai-copy"></i>
      </button>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'QuotationProductList',
  emits: ['delete-quotation-product', 'duplicate-quotation-product'],
  props: {
    products: {
      type: [Array, Object],
      required: true,
    },
  },
};
</script>
