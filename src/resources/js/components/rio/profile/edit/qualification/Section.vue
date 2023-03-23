<template>
  <div class="position-relative py-2 p-md-3 mt-2">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.qualification_description') }}</p>

    <!-- Qualification List -->
    <qualification-item
      v-for="qualification in qualifications"
      :key="qualification.id"
      :qualification="qualification"
    />

    <!-- Add Qualification Button -->
    <div
      v-if="!isExceededLimit"
      :class="`text-${hasQualification ? 'end' : 'center'}`"
    >
      <base-button
        :title="this.$i18n.t('buttons.add_qualification')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#qualification-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import QualificationItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'QualificationSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    QualificationItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const qualifications = ref([]);
    const hasQualification = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of qualifications
     *
     * @returns {void}
     */
    const getQualifications = async () => {
      listLoading.value = true;
      const getQualificationApi =
        await rioProfileApiService.getQualifications();
      const qualificationResponseData = getQualificationApi.data;
      qualifications.value = qualificationResponseData.data || [];

      // Setup conditional parameters
      hasQualification.value = qualificationResponseData.data.length > 0;
      isExceededLimit.value = qualificationResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch qualifications
    getQualifications();

    return {
      qualifications,
      hasQualification,
      getQualifications,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
