<template>
  <div>
    <!-- Receiving account not set modal -->
    <receiving-account-not-set-modal />

    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Registered Prodcut Delete Modal -->
    <delete-registered-product-modal
      ref="deleteRegisteredProductModalRef"
      :product_id="productID"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      @get-products="getProducts"
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
            <header-tab-section
              :service="service"
              :activeTab="'product_management'"
            />

            <div class="d-flex align-items-center justify-content-between">
              <span>{{ $t('headers.registered_lists') }}</span>
              <button
                v-if="has_payment"
                class="btn btn-link"
                @click="redirectToRegisterPage"
              >
                <i class="ai-plus me-1"></i>
                {{ $t('buttons.product_register') }}
              </button>
              <button
                v-else
                class="btn btn-link"
                data-bs-toggle="modal"
                data-bs-target="#receiving-account-not-set"
              >
                <i class="ai-plus me-1"></i>
                {{ $t('buttons.product_register') }}
              </button>
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
                        @click="redirectToEditPage($event, product.id)"
                      >
                        <div
                          class="
                            d-flex
                            align-items-center
                            justify-content-between
                          "
                        >
                          <span>
                            {{ $t('labels.registration_date') }}：
                            {{ product.registered_date }}
                          </span>
                          <div>
                            <button
                              type="button"
                              class="btn btn-link p-0 me-2 c-gray"
                              v-if="!product.is_public"
                            >
                              <i class="ai-eye-off"></i>
                            </button>
                            <button
                              type="button"
                              class="btn btn-link p-0 me-2"
                              disabled
                            >
                              {{ product.product_accessibility }}
                            </button>
                            <button
                              type="button"
                              class="btn btn-link p-0"
                              @click="changeAccessibility($event, product)"
                            >
                              {{ $t('buttons.change2') }}
                            </button>
                          </div>
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
                          </div>
                          <span v-if="product.price !== null">
                            {{ $t('labels.price', { price: product.price }) }}
                          </span>
                          <span v-else>
                            {{ $t('labels.individual_quote') }}
                          </span>
                        </div>
                        <div class="text-end">
                          <button
                            type="button"
                            class="btn btn-link p-0 me-1"
                            @click.stop="onDelete(product.id)"
                          >
                            <i class="c-primary ai-trash-2"></i>
                          </button>
                        </div>
                      </li>
                    </ul>
                    <div v-else class="text-center">
                      <p class="my-4">
                        {{ $t('paragraphs.there_is_no_products') }}
                      </p>
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
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import Pagination from '../../base/BasePagination.vue';
import HeaderTabSection from '../components/HeaderTabsSection.vue';
import ReceivingAccountNotSetModal from '../components/ReceivingAccountNotSetModal.vue';
import DeleteRegisteredProductModal from '../components/DeleteRegisteredProductModal.vue';
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
    has_payment: {
      type: Boolean,
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    Pagination,
    HeaderTabSection,
    ReceivingAccountNotSetModal,
    DeleteRegisteredProductModal,
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
      keyword: null,
      salesCategory: null,
    });
    const deleteRegisteredProductModalRef = ref(null);
    const productID = ref(null);
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
     * Get list of registered products
     *
     * @returns {void}
     */
    const getProducts = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        // Fetch product list
        const getListApi = await classifiedSalesApiService.getRegisteredLists(
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
     * Redirect to register page
     *
     * @returns {void}
     */
    const redirectToRegisterPage = () => {
      window.location.href = `/classifieds/create`;
    };

    /**
     * Redirect to edit page
     *
     * @param {integer} id
     * @returns {void}
     */
    const redirectToEditPage = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/classifieds/${id}/edit`;
    };

    /**
     * Change product accessibility
     *
     * @param {Object} product
     * @returns {void}
     */
    const changeAccessibility = async (event, product) => {
      event.preventDefault();
      event.stopPropagation();

      // Start page loading
      setPageLoading(true);

      await classifiedSalesApiService
        .updateAccessibility(product.id, { is_accept: !product.is_accept })
        .finally(() => {
          getProducts();
        });
    };

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const onDelete = (id) => {
      productID.value = id;
      deleteRegisteredProductModalRef.value.modal.show();
    };

    /**
     * Set search keyword
     *
     * @returns {void}
     */
    const setSearch = (keyword, salesCategory) => {
      formData.value.keyword = keyword;
      formData.value.salesCategory = salesCategory;

      getProducts();
    };

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
      productsList,
      totalProducts,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      paginationData,
      changePage,
      redirectToRegisterPage,
      redirectToEditPage,
      changeAccessibility,
      getProducts,
      onDelete,
      productID,
      deleteRegisteredProductModalRef,
      setSearch,
      getSalesCategories,
      salesCategoriesList,
    };
  },
});
</script>
