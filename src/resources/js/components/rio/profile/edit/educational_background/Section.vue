<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.rio.educational_description') }}</p>

    <!-- Educational background List -->
    <educational-background-item
      v-for="(educationalBackground, index) in educationalBackgrounds"
      :key="educationalBackground.id"
      :item_number="index"
      :educational_background="educationalBackground"
    />

    <!-- Add Educational background Button -->
    <div
      v-if="!isExceededLimit"
      :class="`text-${hasEducationalBackground ? 'end' : 'center'}`"
    >
      <base-button
        :title="this.$i18n.t('buttons.add_educational_background')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#educational-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import EducationalBackgroundItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'EducationalBackgroundSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    EducationalBackgroundItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const educationalBackgrounds = ref([]);
    const hasEducationalBackground = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of educational backgrounds
     *
     * @returns {void}
     */
    const getEducationalBackgrounds = async () => {
      listLoading.value = true;
      const getEducationalApi =
        await rioProfileApiService.getEducationalBackgrounds();
      const getEducationalResponseData = getEducationalApi.data;
      educationalBackgrounds.value = getEducationalResponseData.data || [];

      // Setup conditional parameters
      hasEducationalBackground.value =
        getEducationalResponseData.data.length > 0;
      isExceededLimit.value = getEducationalResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Fetch educational backgrounds on created component
    getEducationalBackgrounds();

    return {
      educationalBackgrounds,
      hasEducationalBackground,
      isExceededLimit,
      listLoading,
      getEducationalBackgrounds,
    };
  },
};
</script>
