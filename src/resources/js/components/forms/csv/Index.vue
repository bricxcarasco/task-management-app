<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Quotation search modal -->
    <quotation-search-modal
      v-if="type == FormTypes.QUOTATION"
      @search-quotations="searchQuotations"
    />

    <!-- Purchase Order search modal -->
    <purchase-search-modal
      v-if="type == FormTypes.PURCHASE_ORDER"
      @search-purchase-order="searchPurchaseOrder"
    />

    <!-- Invoice search modal -->
    <invoice-search-modal
      v-if="type == FormTypes.INVOICE"
      @search-invoices="searchInvoices"
    />

    <!-- Delivery search modal -->
    <delivery-search-modal
      v-if="type == FormTypes.DELIVERY_SLIP"
      @search-deliveries="searchDeliverySlips"
    />

    <!-- Receipt search modal -->
    <receipt-search-modal
      v-if="type == FormTypes.RECEIPT"
      @search-receipts="searchReceipts"
    />

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div class="border-bottom">
            <a
              href="#"
              class="btn btn-link"
              @click="handleRedirectToListPage($event)"
            >
              <i class="ai-arrow-left"></i>
            </a>
          </div>

          <!-- Table header -->
          <p
            v-if="type == FormTypes.QUOTATION"
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
          >
            {{ $t('csv.quotation_csv_output') }}
          </p>
          <p
            v-if="type == FormTypes.PURCHASE_ORDER"
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
          >
            {{ $t('csv.purchase_order_csv_output') }}
          </p>
          <p
            v-if="type == FormTypes.DELIVERY_SLIP"
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
          >
            {{ $t('csv.delivery_slip_csv_output') }}
          </p>
          <p
            v-if="type == FormTypes.INVOICE"
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
          >
            {{ $t('csv.invoice_csv_output') }}
          </p>
          <p
            v-if="type == FormTypes.RECEIPT"
            class="mb-0 mt-4 bg-dark-gray p-2 c-white"
          >
            {{ $t('csv.receipt_csv_output') }}
          </p>

          <!-- CSV form -->
          <form method="POST" action="/forms/csv-export" novalidate>
            <input type="hidden" name="_token" :value="csrf" />
            <input type="hidden" name="type" :value="type" />

            <div class="d-flex align-items-center justify-content-between">
              <div class="form-check mb-0">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="csv-select-all"
                  @click="handleSelectUnselectAll"
                />
                <label class="form-check-label" for="csv-select-all">
                  {{ $t('buttons.select_all') }}
                </label>
              </div>
              <div>
                <button
                  type="button"
                  class="btn btn-link"
                  data-bs-toggle="modal"
                  :data-bs-target="searchModalId"
                >
                  {{ $t('buttons.search') }}
                </button>
                <button type="submit" class="btn btn-link">
                  {{ $t('csv.csv_output_of_selected_form') }}
                </button>
              </div>
            </div>

            <!-- CSV list -->
            <ul
              class="list-group list-group-flush mt-2 list--hover ps-4"
              v-if="formList.length > 0"
            >
              <li
                class="list-group-item p-0 form-check d-flex align-items-center"
                v-for="(form, index) in formList"
                :key="index"
              >
                <input
                  class="form-check-input me-2"
                  type="checkbox"
                  name="form_ids[]"
                  :value="form.id"
                />
                <div class="flex-1">
                  <a
                    href="#"
                    class="js-csv-block c-primary d-block p-3 hoverable"
                    @click="handleSelectCsv($event)"
                  >
                    <span
                      >{{ $t('labels.date_of_issue') }}：{{
                        form.issue_date
                      }}</span
                    >
                    <div
                      class="d-flex align-items-center justify-content-between"
                    >
                      <div>
                        <p class="ellipsis mb-0 text-start">{{ form.title }}</p>
                        <p class="c-light-gray fs-sm mb-0">{{ form.name }}</p>
                      </div>
                      <p class="fs-5">{{ form.price }}{{ $t('labels.yen') }}</p>
                    </div>

                    <p
                      v-if="type == FormTypes.QUOTATION"
                      class="c-light-gray fs-sm mb-0"
                    >
                      {{ $t('labels.term_of_validity') }}：{{
                        form.expiration_date
                      }}
                    </p>
                    <p
                      v-if="type == FormTypes.PURCHASE_ORDER"
                      class="c-light-gray fs-sm mb-0"
                    >
                      {{ $t('labels.delivery_deadline') }}：{{
                        form.delivery_date
                      }}
                    </p>
                    <p
                      v-if="type == FormTypes.DELIVERY_SLIP"
                      class="c-light-gray fs-sm mb-0"
                    >
                      {{ $t('labels.delivery_slip_date') }}：{{
                        form.delivery_deadline
                      }}
                    </p>
                    <p
                      v-if="type == FormTypes.INVOICE"
                      class="c-light-gray fs-sm mb-0"
                    >
                      {{ $t('labels.payment_date') }}：{{ form.payment_date }}
                    </p>
                    <p
                      v-if="type == FormTypes.RECEIPT"
                      class="c-light-gray fs-sm mb-0"
                    >
                      {{ $t('labels.receipt_date2') }}：{{ form.receipt_date }}
                    </p>
                  </a>
                </div>
              </li>
            </ul>
            <div v-else class="mt-2 p-2 d-flex justify-content-center mt-3">
              {{ $t('labels.no_search_result') }}
            </div>
          </form>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-2 mb-3">
            <pagination :meta="paginationData" @changePage="changePage" />
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
import QuotationSearchModal from '../components/QuotationSearchModal.vue';
import PurchaseSearchModal from '../components/PurchaseOrderSearchModal.vue';
import InvoiceSearchModal from '../components/InvoiceSearchModal.vue';
import DeliverySearchModal from '../components/DeliverySlipSearchModal.vue';
import ReceiptSearchModal from '../components/ReceiptSearchModal.vue';
import QuotationApiService from '../../../api/forms/quotations';
import InvoiceApiService from '../../../api/forms/invoices';
import DeliverySlipApiService from '../../../api/forms/delivery-slips';
import PurchaseOrderApiService from '../../../api/forms/purchase-orders';
import ReceiptApiService from '../../../api/forms/receipts';
import FormTypes from '../../../enums/FormTypes';
import Pagination from '../../base/BasePagination.vue';

export default defineComponent({
  name: 'FormCsvListIndex',
  props: {
    csrf: {
      type: [String, Number],
      required: true,
    },
    type: {
      type: [String, Number],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    QuotationSearchModal,
    PurchaseSearchModal,
    InvoiceSearchModal,
    ReceiptSearchModal,
    DeliverySearchModal,
    Pagination,
  },
  setup(props) {
    const quotationApiService = new QuotationApiService();
    const invoiceApiService = new InvoiceApiService();
    const deliverySlipApiService = new DeliverySlipApiService();
    const purchaseOrderApiService = new PurchaseOrderApiService();
    const receiptApiService = new ReceiptApiService();
    const type = ref(props.type);
    const pageLoading = ref(false);
    const paginationData = ref([]);
    const formList = ref([]);
    const isSearch = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      sort_by: 0,
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
     * Get search modal ID based on form type
     *
     * @returns {string}
     */
    const searchModalId = computed(() => {
      const formType = parseInt(type.value, 10);

      switch (formType) {
        case FormTypes.QUOTATION:
          return '#quotation-search-modal';
        case FormTypes.PURCHASE_ORDER:
          return '#purchase-search-modal';
        case FormTypes.DELIVERY_SLIP:
          return '#delivery-search-modal';
        case FormTypes.INVOICE:
          return '#invoice-search-modal';
        case FormTypes.RECEIPT:
          return '#receipt-search-modal';
        default:
          return '#quotation-search-modal';
      }
    });

    /**
     * Get list based on form type
     *
     * @returns {void}
     */
    const getLists = async () => {
      const formType = parseInt(type.value, 10);
      let getListApi = null;

      // Start page loading
      setPageLoading(true);

      switch (formType) {
        case FormTypes.QUOTATION:
          getListApi = await quotationApiService.getLists(formData.value);
          break;
        case FormTypes.PURCHASE_ORDER:
          getListApi = await purchaseOrderApiService.getLists(formData.value);
          break;
        case FormTypes.DELIVERY_SLIP:
          getListApi = await deliverySlipApiService.getLists(formData.value);
          break;
        case FormTypes.INVOICE:
          getListApi = await invoiceApiService.getLists(formData.value);
          break;
        case FormTypes.RECEIPT:
          getListApi = await receiptApiService.getLists(formData.value);
          break;
        default:
          getListApi = await quotationApiService.getLists(formData.value);
          break;
      }

      try {
        const getListResponseData = getListApi.data;

        formList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
      } catch (error) {
        setAlert('failed');
        formList.value = [];

        throw error;
      } finally {
        setPageLoading(false);
      }
    };

    /**
     * Search quotations
     *
     * @returns {void}
     */
    const searchQuotations = (data) => {
      formData.value.free_word = data.free_word || null;
      formData.value.issue_start_date = data.issue_start_date || null;
      formData.value.issue_end_date = data.issue_end_date || null;
      formData.value.expiration_start_date = data.expiration_start_date || null;
      formData.value.expiration_end_date = data.expiration_end_date || null;
      formData.value.amount_min = data.amount_min || null;
      formData.value.amount_max = data.amount_max || null;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search purchase orders
     *
     * @returns {void}
     */
    const searchPurchaseOrder = (data) => {
      formData.value.free_word = data.free_word || null;
      formData.value.issue_start_date = data.issue_start_date || null;
      formData.value.issue_end_date = data.issue_end_date || null;
      formData.value.delivery_start_date = data.delivery_start_date || null;
      formData.value.delivery_end_date = data.delivery_end_date || null;
      formData.value.amount_min = data.amount_min || null;
      formData.value.amount_max = data.amount_max || null;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search invoices
     *
     * @returns {void}
     */
    const searchInvoices = (data) => {
      formData.value.free_word = data.free_word || null;
      formData.value.issue_start_date = data.issue_start_date || null;
      formData.value.issue_end_date = data.issue_end_date || null;
      formData.value.payment_start_date = data.payment_start_date || null;
      formData.value.payment_end_date = data.payment_end_date || null;
      formData.value.amount_min = data.amount_min || null;
      formData.value.amount_max = data.amount_max || null;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search delivery slips
     *
     * @returns {void}
     */
    const searchDeliverySlips = (data) => {
      formData.value.free_word = data.free_word || null;
      formData.value.issue_start_date = data.issue_start_date || null;
      formData.value.issue_end_date = data.issue_end_date || null;
      formData.value.delivery_deadline_start_date =
        data.delivery_deadline_start_date || null;
      formData.value.delivery_deadline_end_date =
        data.delivery_deadline_end_date || null;
      formData.value.amount_min = data.amount_min || null;
      formData.value.amount_max = data.amount_max || null;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search receipts
     *
     * @returns {void}
     */
    const searchReceipts = (data) => {
      formData.value.free_word = data.free_word || null;
      formData.value.issue_start_date = data.issue_start_date || null;
      formData.value.issue_end_date = data.issue_end_date || null;
      formData.value.receipt_start_date = data.receipt_start_date || null;
      formData.value.receipt_end_date = data.receipt_end_date || null;
      formData.value.amount_min = data.amount_min || null;
      formData.value.amount_max = data.amount_max || null;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Select/Unselect all checkboxes
     *
     * @returns {void}
     */
    const handleSelectUnselectAll = () => {
      const checkboxes = document.querySelectorAll('input[type="checkbox"]');

      for (let i = 1; i < checkboxes.length; i += 1) {
        checkboxes[i].checked = checkboxes[0].checked;
      }
    };

    /**
     * Select individual CSV
     *
     * @returns {void}
     */
    const handleSelectCsv = (event) => {
      event.preventDefault();
      event.stopPropagation();

      const divSelector = event.target.closest('.js-csv-block');
      const checkboxSelector = divSelector.parentElement.previousSibling;
      checkboxSelector.checked = !checkboxSelector.checked;
    };

    /**
     * Change page
     *
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getLists();
    };

    /**
     * Redirect to list page
     *
     * @returns {void}
     */
    const handleRedirectToListPage = (event) => {
      event.preventDefault();
      event.stopPropagation();

      const formType = parseInt(type.value, 10);

      switch (formType) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts`;
          break;
        default:
          window.location.href = `/forms/quotations`;
          break;
      }
    };

    /**
     * On mounted property
     */
    onMounted(() => {
      getLists();
    });

    return {
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      formList,
      formData,
      FormTypes,
      changePage,
      paginationData,
      handleRedirectToListPage,
      searchQuotations,
      searchPurchaseOrder,
      searchInvoices,
      searchDeliverySlips,
      searchReceipts,
      isSearch,
      searchModalId,
      handleSelectUnselectAll,
      handleSelectCsv,
      getLists,
    };
  },
});
</script>
