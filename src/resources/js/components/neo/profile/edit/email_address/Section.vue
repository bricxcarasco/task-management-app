<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■{{ $t('messages.neo.email_address_description') }}</p>

    <!-- Email List -->
    <email-item
      v-for="(email, index) in emails"
      :key="email.id"
      :item_number="index"
      :email="email"
    />

    <!-- Add Email Button -->
    <div v-if="!isExceededLimit" :class="`text-${hasEmail ? 'end' : 'center'}`">
      <base-button
        :title="this.$i18n.t('buttons.add_email_address')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#email-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import EmailItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'EmailSection',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
    neo: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseButton,
    EmailItem,
    SectionLoader,
  },
  setup(props) {
    const neoProfileApiService = new NeoProfileApiService();
    const emails = ref([]);
    const hasEmail = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);
    const editNeo = ref(props.neo);

    /**
     * Get list of emails
     *
     * @returns {void}
     */
    const getEmails = async () => {
      listLoading.value = true;
      const getEmailApi = await neoProfileApiService.getEmails(
        editNeo.value.id
      );
      const emailResponseData = getEmailApi.data;
      emails.value = emailResponseData.data || [];

      // Setup conditional parameters
      hasEmail.value = emailResponseData.data.length > 0;
      isExceededLimit.value = emailResponseData.data.length >= 3;
      listLoading.value = false;
    };

    // Initially fetch emails
    getEmails();

    return {
      emails,
      hasEmail,
      getEmails,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
