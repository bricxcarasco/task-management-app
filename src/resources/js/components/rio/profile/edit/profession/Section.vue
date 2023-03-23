<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.rio.profession_description') }}</p>

    <!-- Profession List -->
    <profession-item
      v-for="profession in professions"
      :key="profession.id"
      :profession="profession"
    />

    <!-- Add Profession Button -->
    <div
      v-if="!isExceededLimit"
      :class="`text-${hasProfession ? 'end' : 'center'}`"
    >
      <base-button
        :title="this.$i18n.t('buttons.add_profession')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#profession-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import ProfessionItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'ProfessionSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    ProfessionItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const professions = ref([]);
    const hasProfession = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of professions
     *
     * @returns {void}
     */
    const getProfessions = async () => {
      listLoading.value = true;
      const getProfessionApi = await rioProfileApiService.getProfessions();
      const professionResponseData = getProfessionApi.data;
      professions.value = professionResponseData.data || [];

      // Setup conditional parameters
      hasProfession.value = professionResponseData.data.length > 0;
      isExceededLimit.value = professionResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch professions
    getProfessions();

    return {
      professions,
      hasProfession,
      getProfessions,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
