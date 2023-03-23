<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />
    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div class="position-relative">
              <h3 class="py-3 mb-0 text-center">
                {{
                  $t('headers.service_owned_classifieds', { name: serviceName })
                }}
              </h3>
            </div>
            <div class="position-relative text-center mb-4">
              <p class="mb-0">{{ $t('headers.favorite_list') }}</p>
            </div>
            <div class="position-relative my-3">
              <button
                class="btn btn-link btn--back"
                @click="redirectBackToListPage"
              >
                <i class="ai-arrow-left me-2"></i>
                {{ $t('buttons.redirect_to_product_list') }}
              </button>
            </div>
            <div class="tab-content">
              <div class="tab-pane fade active show">
                <div
                  class="d-flex justify-content-between align-items-center mt-2"
                >
                  <p class="mb-0" style="flex: 1">
                    {{ totalFavorites }}{{ $t('labels.cases') }}
                  </p>
                </div>
                <div class="card p-0 shadow mt-2">
                  <div class="product__wrapper">
                    <ul
                      v-if="favoritesList.length > 0"
                      class="product__lists list-group list-group-flush"
                    >
                      <li
                        v-for="(product, index) in favoritesList"
                        :key="index"
                        class="
                          list-group-item
                          position-relative
                          list--white
                          p-2
                        "
                      >
                        <span
                          class="product__exhibitionStatus c-white fs-xs p-1"
                          :class="
                            product.is_accept ? 'bg-blue' : 'bg-dark-gray'
                          "
                        >
                          {{ product.product_accessibility }}
                        </span>
                        <div class="position-relative text-end">
                          <span class="me-2">
                            {{ product.registered_date }}
                          </span>
                          <span class="me-2">
                            {{ product.sale_category_name }}
                          </span>
                        </div>
                        <div
                          class="
                            d-flex
                            align-items-center
                            justify-content-center
                            mt-2
                          "
                        >
                          <div
                            style="
                              height: 80px;
                              width: 80px;
                              background-size: cover;
                              background-position: center;
                            "
                            :style="
                              'background-image: url(' +
                              product.main_photo +
                              ');'
                            "
                            class="bg-secondary me-2"
                          ></div>
                          <div class="flex-1">
                            <span class="d-block ellipsis--product text-start">
                              {{ product.title }}
                            </span>
                            <span
                              v-if="product.price !== null"
                              class="text-danger"
                            >
                              {{ $t('labels.price', { price: product.price }) }}
                            </span>
                            <span v-else class="text-danger">
                              {{ $t('labels.individual_quote') }}
                            </span>
                          </div>

                          <!-- Favorite button -->
                          <favorite-button
                            :product="product"
                            :service="service"
                            :isFavorite="true"
                            @get-products="getFavorites"
                            @page-loading="setPageLoading"
                          />
                        </div>
                        <p class="text-end fs-sm mb-0">
                          {{ product.selling_name }}
                          <span v-if="product.is_connected" class="ms-2">
                            <i class="h6 m-0 c-primary ai-handshake-o"></i>
                          </span>
                        </p>
                      </li>
                    </ul>
                    <div v-else class="text-center">
                      <p class="my-4">
                        {{ $t('paragraphs.there_is_no_products') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-2 mb-3">
                <pagination :meta="paginationData" @changePage="changePage" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import Pagination from '../../base/BasePagination.vue';
import FavoriteButton from '../components/FavoriteButton.vue';
import ClassifiedFavoritesApiService from '../../../api/classifieds/favorites';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'AllProductListIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    Pagination,
    FavoriteButton,
  },
  setup(props) {
    /**
     * Data properties
     */
    const classifiedFavoritesApiService = new ClassifiedFavoritesApiService();
    const service = ref(props.service);
    const favoritesList = ref([]);
    const paginationData = ref([]);
    const pageLoading = ref(false);
    const totalFavorites = ref(0);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      page: 1,
    });

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case ServiceSelectionTypesEnum.RIO:
          return data.full_name;
        case ServiceSelectionTypesEnum.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Set alert messages
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
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Get list of favorites
     *
     * @returns {void}
     */
    const getFavorites = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        // Fetch favorite list
        const getListApi = await classifiedFavoritesApiService.getFavorites(
          formData.value
        );
        const getListResponseData = getListApi.data;

        favoritesList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
        totalFavorites.value = paginationData.value.total || 0;
      } catch (error) {
        setAlert('failed');
        favoritesList.value = [];

        throw error;
      } finally {
        // End page loading
        setPageLoading(false);
      }
    };

    /**
     * Change page
     *
     * @param {Integer} page
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getFavorites();
    };

    /**
     * Redirect back to All Product list page
     *
     * @returns {void}
     */
    const redirectBackToListPage = () => {
      window.location.href = `/classifieds`;
    };

    /**
     * On mounted properties
     */
    onMounted(() => {
      getFavorites();
    });

    return {
      favoritesList,
      totalFavorites,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      paginationData,
      changePage,
      serviceName,
      redirectBackToListPage,
      getFavorites,
    };
  },
});
</script>
