<template>
  <div class="position-relative py-2 p-md-3 mt-2">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.skill_description') }}</p>

    <!-- Skill List -->
    <skill-item v-for="skill in skills" :key="skill.id" :skill="skill" />

    <!-- Add Skill Button -->
    <div v-if="!isExceededLimit" :class="`text-${hasSkill ? 'end' : 'center'}`">
      <base-button
        :title="this.$i18n.t('buttons.add_skill')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#skill-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import SkillItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';

export default {
  name: 'SkillSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    BaseButton,
    SkillItem,
    SectionLoader,
  },
  setup() {
    const rioProfileApiService = new RioProfileApiService();
    const skills = ref([]);
    const hasSkill = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of skills
     *
     * @returns {void}
     */
    const getSkills = async () => {
      listLoading.value = true;
      const getSkillApi = await rioProfileApiService.getSkills();
      const skillResponseData = getSkillApi.data;
      skills.value = skillResponseData.data || [];

      // Setup conditional parameters
      hasSkill.value = skillResponseData.data.length > 0;
      isExceededLimit.value = skillResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch skills
    getSkills();

    return {
      skills,
      hasSkill,
      getSkills,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
