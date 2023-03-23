<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■{{ $t('messages.award_history_description') }}</p>

    <!-- Award List -->
    <award-item
      v-for="(award, index) in awards"
      :key="award.id"
      :item_number="index"
      :award="award"
    />

    <!-- Add Award Button -->
    <div v-if="!isExceededLimit" :class="`text-${hasAward ? 'end' : 'center'}`">
      <base-button
        :title="this.$i18n.t('buttons.add_award_history')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#award-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import AwardItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'AwardSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    AwardItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const awards = ref([]);
    const hasAward = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of awards
     *
     * @returns {void}
     */
    const getAwards = async () => {
      listLoading.value = true;
      const getAwardApi = await rioProfileApiService.getAwards();
      const awardResponseData = getAwardApi.data;
      awards.value = awardResponseData.data || [];

      // Setup conditional parameters
      hasAward.value = awardResponseData.data.length > 0;
      isExceededLimit.value = awardResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch awards
    getAwards();

    return {
      awards,
      hasAward,
      getAwards,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
