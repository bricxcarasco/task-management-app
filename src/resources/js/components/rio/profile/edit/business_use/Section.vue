<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <ul class="list-group list-group-flush">
      <!-- Affiliate List -->
      <affiliate-item
        v-for="affiliate in affiliates"
        :key="affiliate.id"
        :affiliate="affiliate"
      />
    </ul>
  </div>
</template>

<script>
import { ref } from 'vue';
import AffiliateItem from './Item.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'AffiliateSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    AffiliateItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const affiliates = ref([]);
    const listLoading = ref(false);

    /**
     * Get list of affiliates
     *
     * @returns {void}
     */
    const getAffiliates = async () => {
      listLoading.value = true;
      const getAffiliateApi = await rioProfileApiService.getAffiliates();
      const affiliateResponseData = getAffiliateApi.data;
      affiliates.value = affiliateResponseData.data || [];

      // Setup conditional parameters
      listLoading.value = false;
    };

    // Initially fetch affiliates
    getAffiliates();

    return {
      affiliates,
      getAffiliates,
      listLoading,
    };
  },
};
</script>
