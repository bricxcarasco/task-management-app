<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.industry_description') }}</p>

    <!-- Industry List -->
    <industry-item
      v-for="(industry, index) in industries"
      :key="industry.id"
      :item_number="index"
      :industry="industry"
    />

    <!-- Add Industry Button -->
    <div
      v-if="!isExceededLimit"
      :class="`text-${hasIndustry ? 'end' : 'center'}`"
    >
      <base-button
        :title="this.$i18n.t('buttons.add_industry')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#industry-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import IndustryItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'IndustrySection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    IndustryItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const industries = ref([]);
    const hasIndustry = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of industries
     *
     * @returns {void}
     */
    const getIndustries = async () => {
      listLoading.value = true;
      const getIndustryApi = await rioProfileApiService.getIndustries();
      const getIndustryResponseData = getIndustryApi.data;
      industries.value = getIndustryResponseData.data || [];

      // Setup conditional parameters
      hasIndustry.value = getIndustryResponseData.data.length > 0;
      isExceededLimit.value = getIndustryResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Fetch professions on created component
    getIndustries();

    return {
      industries,
      hasIndustry,
      getIndustries,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
