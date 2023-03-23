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
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'IndustrySection',
  props: {
    neo: {
      type: [Array, Object],
      required: true,
    },
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
  setup(props) {
    const neoProfileApiService = new NeoProfileApiService();
    const neo = ref(props.neo);
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
      const getIndustryApi = await neoProfileApiService.getIndustries(
        neo.value.id
      );
      const getIndustryResponseData = getIndustryApi.data;
      industries.value = getIndustryResponseData.data || [];

      // Setup conditional parameters
      hasIndustry.value = getIndustryResponseData.data.length > 0;
      isExceededLimit.value = getIndustryResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Fetch industries on created component
    getIndustries(neo.value.id);

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
