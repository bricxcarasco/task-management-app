<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Search modal -->
    <search-modal
      @set-Search="setSearch"
      :keyword="formData.keyword"
      :sales_category_list="salesCategoriesList"
      :sales_category="formData.salesCategory"
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
            <!-- Header and tabs section -->
            <header-tab-section :service="service" :activeTab="'search'" />

            <div v-if="!isFreeWordSearch">
              {{ $t('links.all_products_list') }}
            </div>
            <div v-else>
              {{ $t('labels.product_list', { keyword: formData.keyword }) }}
            </div>
            <div class="tab-content">
              <div class="tab-pane fade active show">
                <div
                  class="d-flex justify-content-between align-items-center mt-2"
                >
                  <p class="mb-0" style="flex: 1">
                    {{ totalProducts }}{{ $t('labels.cases') }}
                  </p>
                  <div>
                    <div class="form-check me-2 d-inline-block">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="hide-cancelled"
                        v-model="formData.is_accept"
                      />
                      <label class="form-check-label" for="hide-cancelled">
                        {{ $t('labels.hide_cancellations') }}
                      </label>
                    </div>

                    <button
                      type="button"
                      class="btn btn-link p-0 c-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#classified-search-modal"
                    >
                      {{ $t('links.filtered_search') }}
                    </button>
                  </div>
                </div>
                <div class="card p-0 shadow mt-2">
                  <div class="product__wrapper">
                    <ul
                      v-if="productsList.length > 0"
                      class="product__lists list-group list-group-flush"
                    >
                      <li
                        v-for="(product, index) in productsList"
                        :key="index"
                        class="
                          list-group-item
                          position-relative
                          list--white
                          hoverable
                          p-2
                        "
                        @click="redirectToViewPage($event, product.id)"
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
                            class="bg-secondary me-2"
                          >
                            <img
                              class="cover"
                              :src="product.main_photo"
                              @error="
                                Common.handleNotFoundImageException(
                                  $event,
                                  DefaultImageCategory.CLASSIFIED
                                )
                              "
                            />
                          </div>
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
import { defineComponent, ref, onMounted, watch } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import Pagination from '../../base/BasePagination.vue';
import HeaderTabSection from '../components/HeaderTabsSection.vue';
import FavoriteButton from '../components/FavoriteButton.vue';
import ClassifiedSalesApiService from '../../../api/classifieds/sales';
import SearchModal from '../components/SearchModal.vue';

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
    HeaderTabSection,
    FavoriteButton,
    SearchModal,
  },
  setup() {
    /**
     * Data properties
     */
    const classifiedSalesApiService = new ClassifiedSalesApiService();
    const productsList = ref([]);
    const paginationData = ref([]);
    const pageLoading = ref(false);
    const totalProducts = ref(0);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      page: 1,
      is_accept: null,
      keyword: null,
      salesCategory: null,
    });
    const isFreeWordSearch = ref(false);
    const salesCategoriesList = ref([]);

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
     * Get list of products
     *
     * @returns {void}
     */
    const getProducts = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        // Fetch product list
        const getListApi = await classifiedSalesApiService.getLists(
          formData.value
        );
        const getListResponseData = getListApi.data;

        productsList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
        totalProducts.value = paginationData.value.total || 0;
      } catch (error) {
        setAlert('failed');
        productsList.value = [];

        throw error;
      } finally {
        // End page loading
        setPageLoading(false);
      }
    };

    /**
     * Reset form filters/conditions
     *
     * @returns {void}
     */
    const resetFormData = () => {
      formData.value = {
        page: 1,
        is_accept: null,
        keyword: null,
        salesCategory: null,
      };
    };

    /**
     * Change page
     *
     * @param {Integer} page
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getProducts();
    };

    /**
     * Get and display fresh and unfiltered products list
     *
     * @returns {void}
     */
    const resetAndGetAllProducts = () => {
      resetFormData();
      getProducts();
    };

    /**
     * Redirect to view page
     *
     * @param {integer} id
     * @returns {void}
     */
    const redirectToViewPage = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/classifieds/${id}`;
    };

    /**
     * Set search keyword
     *
     * @returns {void}
     */
    const setSearch = (keyword, salesCategory) => {
      formData.value.keyword = keyword;
      formData.value.salesCategory = salesCategory;
      isFreeWordSearch.value =
        keyword !== '' && keyword !== undefined && keyword !== null;

      getProducts();
    };

    /**
     * Watch for filtered accessibility
     */
    watch(
      () => [formData.value.is_accept],
      () => {
        formData.value.page = 1;
        getProducts();
      }
    );

    /**
     * Get list of sales categories
     *
     * @returns {void}
     */
    const getSalesCategories = async () => {
      try {
        // Fetch product list
        const getSalesCategoriesListApi =
          await classifiedSalesApiService.getSalesCategories();
        const getSalesCategoriesListResponseData =
          getSalesCategoriesListApi.data;

        salesCategoriesList.value =
          getSalesCategoriesListResponseData?.data || [];
      } catch (error) {
        setAlert('failed');
        salesCategoriesList.value = [];

        throw error;
      }
    };

    /**
     * On mounted properties
     */
    onMounted(() => {
      getSalesCategories();
      getProducts();
    });

    return {
      Common,
      DefaultImageCategory,
      formData,
      resetAndGetAllProducts,
      productsList,
      totalProducts,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      paginationData,
      changePage,
      redirectToViewPage,
      getProducts,
      setSearch,
      isFreeWordSearch,
      getSalesCategories,
      salesCategoriesList,
    };
  },
});
</script>
