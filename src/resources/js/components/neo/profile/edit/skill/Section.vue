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
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'SkillSection',
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
    SkillItem,
    SectionLoader,
  },
  setup(props) {
    const neoProfileApiService = new NeoProfileApiService();
    const neo = ref(props.neo);
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
      const getSkillApi = await neoProfileApiService.getSkills(neo.value.id);
      const skillResponseData = getSkillApi.data;
      skills.value = skillResponseData.data || [];

      // Setup conditional parameters
      hasSkill.value = skillResponseData.data.length > 0;
      isExceededLimit.value = skillResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Initially fetch skills
    getSkills(neo.value.id);

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
