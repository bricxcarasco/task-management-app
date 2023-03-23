<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <p class="mb-0 pe-2">{{ totalResults }}{{ $t('links.total_suffix') }}</p>
      <div class="text-end" style="flex: 1">
        <div class="form-check">
          <input
            class="form-check-input float-none"
            type="checkbox"
            id="exclude"
            @change.stop="check"
          />
          <label class="form-check-label" for="exclude"
            >{{ $t('paragraphs.exclude_connected_rio_neo') }}
          </label>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="card p-4 shadow mt-2" id="connection-requests-list-items">
      <section-loader :show="listLoading" />
      <div class="connection__wrapper">
        <base-alert
          :success="alert.success"
          :danger="alert.failed"
          :message="alert.message"
          @closeAlert="resetAlert"
        />
        <ul
          v-if="searchResults.length > 0"
          class="connection__lists list-group list-group-flush mt-2"
        >
          <li
            class="
              list-group-item
              px-0
              py-2
              position-relative
              list--white
              px-2
              connection__result
            "
            v-for="(request, index) in searchResults"
            :key="`${request.id}${index}`"
            @click.stop="redirectIntroduction(request)"
          >
            <img
              class="rounded-circle me-2 d-inline-block img--profile_image_sm"
              :src="request.profile_image ?? image_path"
              @error="
                Common.handleNotFoundImageException(
                  $event,
                  DefaultImageCategory.RIO_NEO
                )
              "
              width="40"
            />
            <span class="fs-xs c-primary ms-2">
              {{ request.name }}
            </span>
          </li>
        </ul>
        <div v-else class="d-flex justify-content-center mt-3">
          {{ $t('paragraphs.no_search_result') }}
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-1 mb-3">
          <pagination :meta="paginationData" @changePage="changePage" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import ConnectionSearch from '../../../api/connection/connection_search';
import BaseAlert from '../../base/BaseAlert.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'SearchResultsSection',
  props: {
    image_path: {
      type: String,
      required: true,
    },
    request_data: {
      type: [Object, null],
      required: true,
    },
  },
  components: {
    BaseAlert,
    Pagination,
    SectionLoader,
  },
  setup(props) {
    const searchResultApi = new ConnectionSearch();
    const alert = ref({
      success: false,
      failed: false,
    });
    const resultData = ref({});
    const data = ref(props.request_data);
    const searchResults = ref([]);
    const listLoading = ref(false);
    const paginationData = ref([]);
    const totalResults = ref(0);
    const url = ref(null);

    /**
     * Reset alert values
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status = 'success', message = null) => {
      const statusValue = status === 'success' ? 'success' : 'failed';
      alert.value[statusValue] = true;
      alert.value.message = message;
    };

    /**
     * Get list of applications
     *
     * @returns {void}
     */
    const getResults = async () => {
      listLoading.value = true;
      data.value.page = resultData.value.page;

      try {
        const getResultsApi = await searchResultApi.getSearchResults(
          data.value
        );
        const getUserResponseData = getResultsApi.data;

        searchResults.value = getUserResponseData?.data || [];
        paginationData.value = getUserResponseData?.meta || [];
        totalResults.value = getUserResponseData?.meta?.total || 0;

        // Handle out of bounds page
        if (searchResults.value.length === 0 && totalResults.value > 0) {
          resultData.value.page = null;
          getResults();
        }
      } catch (error) {
        resetAlert();
        setAlert('failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    /**
     * Redirect to rio/neo introduction
     *
     * @param {object} request
     * @returns {void}
     */
    const redirectIntroduction = (request) => {
      if (request.service === 'rio') {
        url.value = '/rio/profile/introduction/:id';
      }
      if (request.service === 'neo') {
        url.value = '/neo/profile/introduction/:id';
      }
      url.value = url.value.replace(':id', request.id);
      window.open(url.value, '_blank');
    };

    /**
     * Redirect to rio/neo introduction
     *
     * @param {object} request
     * @returns {void}
     */
    const check = (event) => {
      data.value.exclude_connected = 0;
      if (event.target.checked) {
        data.value.exclude_connected = 1;
      }
      getResults();
    };

    /**
     * Watch on alert changes
     */
    watch(alert.value, () => {
      if (alert.value.success || alert.value.failed) {
        setTimeout(() => {
          resetAlert();
        }, 3000);
      }
    });

    /**
     * Update list on page change
     *
     * @returns {void}
     */
    const changePage = (page) => {
      resultData.value.page = page;
      getResults();
    };

    getResults();

    return {
      alert,
      resultData,
      changePage,
      searchResults,
      getResults,
      listLoading,
      paginationData,
      redirectIntroduction,
      resetAlert,
      totalResults,
      check,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
