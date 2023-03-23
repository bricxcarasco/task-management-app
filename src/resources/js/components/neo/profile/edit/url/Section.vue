<template>
  <div class="position-relative">
    <section-loader :show="listLoading" />

    <p>■ {{ $t('messages.url_description') }}</p>

    <!-- Url List -->
    <url-item
      v-for="(url, index) in urls"
      :key="url.id"
      :item_number="index"
      :url="url"
    />

    <!-- Add Industry Button -->
    <div v-if="!isExceededLimit" :class="`text-${hasUrl ? 'end' : 'center'}`">
      <base-button
        :title="this.$i18n.t('buttons.add_url')"
        :buttonType="'light'"
        :loading="loading"
        :icon="'ai-plus me-2'"
        data-bs-toggle="modal"
        data-bs-target="#url-form-modal"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import UrlItem from './Item.vue';
import BaseButton from '../../../../base/BaseButton.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import NeoProfileApiService from '../../../../../api/neo/profile';

export default {
  name: 'UrlSection',
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
    UrlItem,
    SectionLoader,
  },
  setup(props) {
    const neoProfileApiService = new NeoProfileApiService();
    const neo = ref(props.neo);
    const urls = ref([]);
    const hasUrl = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);

    /**
     * Get list of urls
     *
     * @returns {void}
     */
    const getUrls = async () => {
      listLoading.value = true;
      const getUrlApi = await neoProfileApiService.getUrls(neo.value.id);
      const getUrlResponseData = getUrlApi.data;
      urls.value = getUrlResponseData.data || [];

      // Setup conditional parameters
      hasUrl.value = getUrlResponseData.data.length > 0;
      isExceededLimit.value = getUrlResponseData.data.length >= 20;
      listLoading.value = false;
    };

    // Fetch urls on created component
    getUrls(neo.value.id);

    return {
      urls,
      hasUrl,
      getUrls,
      isExceededLimit,
      listLoading,
    };
  },
};
</script>
