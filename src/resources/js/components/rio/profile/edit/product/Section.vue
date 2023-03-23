<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■{{ $t('messages.product_description') }}</p>

    <!-- Product List -->
    <product-item
      v-for="(product, index) in products"
      :key="product.id"
      :item_number="index"
      :product="product"
      @open-reference-url="
        (referenceUrl) => $emit('open-reference-url', referenceUrl)
      "
    />

    <!-- Add Product Button -->
    <div
      v-if="!isExceededLimit"
      :class="`text-${hasProduct ? 'end' : 'center'}`"
    >
      <base-button
        :title="this.$i18n.t('buttons.add_product')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#product-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import ProductItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'ProductSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    ProductItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const products = ref([]);
    const hasProduct = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of products
     *
     * @returns {void}
     */
    const getProducts = async () => {
      listLoading.value = true;
      const getProductApi = await rioProfileApiService.getProducts();
      const productResponseData = getProductApi.data;
      products.value = productResponseData.data || [];

      // Setup conditional parameters
      hasProduct.value = productResponseData.data.length > 0;
      isExceededLimit.value = productResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch products
    getProducts();

    return {
      products,
      hasProduct,
      getProducts,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
