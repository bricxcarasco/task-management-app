<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Preview modal -->
    <preview-modal
      ref="formPreviewModalRef"
      :form_data="historyFormData"
      :product_price_with_taxes="productPriceWithTaxes"
      :logo="logoDisplay"
    />

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <!-- Content-->
        <div class="col-12 col-md-9 offset-md-3">
          <div class="border-bottom">
            <button
              type="button"
              class="btn btn-link"
              @click="returnFormListPage(selectedFormType)"
            >
              <i class="ai-arrow-left"></i>
            </button>
          </div>
          <p
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
            v-if="selectedFormType === 'quotations'"
          >
            {{ $t('labels.quotation') }} {{ $t('buttons.delete_history') }}
          </p>
          <p
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
            v-else-if="selectedFormType === 'purchase-orders'"
          >
            {{ $t('labels.purchase_order') }} {{ $t('buttons.delete_history') }}
          </p>
          <p
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
            v-else-if="selectedFormType === 'delivery-slips'"
          >
            {{ $t('labels.delivery_slip') }} {{ $t('buttons.delete_history') }}
          </p>
          <p
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
            v-else-if="selectedFormType === 'invoices'"
          >
            {{ $t('labels.request') }} {{ $t('buttons.delete_history') }}
          </p>
          <p
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
            v-else-if="selectedFormType === 'receipts'"
          >
            {{ $t('labels.receipt') }} {{ $t('buttons.delete_history') }}
          </p>
          <div class="card shadow mt-2">
            <div v-if="historyList.length > 0">
              <ul
                class="form__lists list-group list-group-flush mt-4 list--hover"
              >
                <li
                  v-for="(history, index) in historyList"
                  :key="index"
                  class="list-group-item p-0"
                >
                  <a
                    href="#"
                    class="c-primary d-block p-3"
                    @click="handleOpenFormPreview(history)"
                  >
                    <span>
                      {{ $t('labels.date_of_issue') }}：
                      {{ history.issue_date }}
                    </span>
                    <div
                      class="d-flex align-items-center justify-content-between"
                    >
                      <div>
                        <p class="ellipsis mb-0 text-start">
                          {{ history.title }}
                        </p>
                        <p class="c-light-gray fs-sm mb-0">
                          {{ history.name }}
                        </p>
                      </div>
                      <p class="fs-5">{{ history.price }}円</p>
                    </div>
                    <span></span>
                    <p class="c-light-gray fs-sm">
                      {{ $t('labels.term_of_validity') }}：
                      {{ history.expiration_date }}
                    </p>
                    <p class="text-danger mb-0">
                      {{ $t('labels.deletion_date') }}：
                      {{ history.deleted_at }}
                    </p>
                    <p class="text-danger mb-0">
                      {{ $t('labels.deletion_operator') }}：
                      {{ history.deleter_email }}
                    </p>
                  </a>
                </li>
              </ul>
              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-2 mb-3">
                <pagination :meta="paginationData" @changePage="changePage" />
              </div>
            </div>
            <div v-else class="p-3 text-center">
              <span>{{ $t('paragraphs.no_deleted_data') }}</span>
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
import FormApiService from '../../../api/forms/forms';
import BpheroConfig from '../../../config/bphero';
import FormTypes from '../../../enums/FormTypes';
import Pagination from '../../base/BasePagination.vue';
import PreviewModal from '../components/PreviewModal.vue';

export default defineComponent({
  name: 'DeleteHistory',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    form_type: {
      type: [String, null],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    Pagination,
    PreviewModal,
  },
  setup(props) {
    const selectedFormType = ref(props.form_type);
    const formApiService = new FormApiService();
    const formPreviewModalRef = ref(null);
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      form_type: props.form_type,
    });
    const service = ref(props.service);
    const paginationData = ref([]);
    const historyList = ref([]);
    const historyFormData = ref({});
    const productPriceWithTaxes = ref({});
    const logoDisplay = ref('');

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
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case 'RIO':
          return data.full_name;
        case 'NEO':
          return data.organization_name;
        default:
          return `-`;
      }
    });

    const getFormType = () => {
      switch (formData.value.form_type) {
        case 'quotations':
          return FormTypes.QUOTATION;
        case 'purchase-orders':
          return FormTypes.PURCHASE_ORDER;
        case 'delivery-slips':
          return FormTypes.DELIVERY_SLIP;
        case 'invoices':
          return FormTypes.INVOICE;
        case 'receipts':
          return FormTypes.RECEIPT;
        default:
          return '';
      }
    };

    /**
     * Get list of delete history based on current form type
     *
     * @returns {void}
     */
    const getDeleteHistoryLists = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await formApiService.getLists(formData.value);
        const getListResponseData = getListApi.data;
        historyList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
      } catch (error) {
        setAlert('failed');
        historyList.value = [];
        throw error;
      } finally {
        setPageLoading(false);
      }
    };

    const returnFormListPage = (currentFormType) => {
      window.location.href = `${window.location.protocol}//${window.location.host}/forms/${currentFormType}`;
    };

    /**
     * Calculate prices
     */
    const getProductPriceWithTaxes = () => {
      let tax10 = 0;
      let tax8 = 0;
      let total = 0;
      let overall = 0;

      historyFormData.value.products.forEach((product) => {
        switch (parseInt(product.tax_distinction, 3)) {
          case 1:
            tax10 += parseFloat(product.amount) * 0.1;
            break;
          case 2:
            tax8 += parseFloat(product.amount) * 0.08;
            break;
          default:
            break;
        }

        total += product.quantity * product.unit_price;
      });

      tax10 = Math.floor(parseFloat(tax10));
      tax8 = Math.floor(parseFloat(tax8));
      overall = total + tax10 + tax8 ?? 0;

      productPriceWithTaxes.value = {
        subTotal: total,
        totalWithGST: tax10,
        totalWitConsumptionTax: tax8,
        total: overall,
      };
    };

    /**
     * Issuer image display
     */
    const getLogoDisplay = () => {
      const issuerImage = historyFormData.value.issuer_image;
      let logoValue = `${BpheroConfig.DEFAULT_IMG}`;

      if (
        issuerImage &&
        (issuerImage.startsWith('https://') ||
          issuerImage.startsWith('http://'))
      ) {
        logoValue = issuerImage;
      } else {
        logoValue = issuerImage
          ? `/hero-storage/public/forms/issuer/profile_image/${issuerImage}`
          : `${BpheroConfig.DEFAULT_IMG}`;
      }

      logoDisplay.value = logoValue;
    };

    /**
     * Open form preview modal
     *
     * @returns {void}
     */
    const handleOpenFormPreview = async (form) => {
      setPageLoading(true);

      const data = {
        id: form.id,
        form_type: formData.value.form_type,
      };

      await formApiService
        .getFormHistory(data)
        .then((response) => {
          historyFormData.value = response.data.data;
          historyFormData.value.products =
            historyFormData.value.deleted_products;
        })
        .finally(() => setPageLoading(false));

      getProductPriceWithTaxes();
      getLogoDisplay();

      formPreviewModalRef.value.show();
    };

    /**
     * Change page
     *
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getDeleteHistoryLists();
    };

    onMounted(() => {
      formData.value.form_type = getFormType();
      getDeleteHistoryLists();
    });

    return {
      formPreviewModalRef,
      selectedFormType,
      serviceName,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      getDeleteHistoryLists,
      getFormType,
      historyList,
      formData,
      historyFormData,
      productPriceWithTaxes,
      logoDisplay,
      FormTypes,
      changePage,
      paginationData,
      returnFormListPage,
      handleOpenFormPreview,
    };
  },
});
</script>
