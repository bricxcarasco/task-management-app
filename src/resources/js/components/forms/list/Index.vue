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
      v-if="type === FormTypes.QUOTATION"
      @search-quotations="searchQuotations"
    />

    <!-- Purchase Order search modal -->
    <purchase-search-modal
      v-if="type === FormTypes.PURCHASE_ORDER"
      @search-purchase-order="searchPurchaseOrder"
    />

    <!-- Invoice search modal -->
    <invoice-search-modal
      v-if="type === FormTypes.INVOICE"
      @search-invoices="searchInvoices"
    />

    <!-- Delivery search modal -->
    <delivery-search-modal
      v-if="type === FormTypes.DELIVERY_SLIP"
      @search-deliveries="searchDeliverySlips"
    />

    <!-- Receipt search modal -->
    <receipt-search-modal
      v-if="type === FormTypes.RECEIPT"
      @search-receipts="searchReceipts"
    />

    <!-- Form delete confirm modal -->
    <form-delete-confirmation-modal
      :type="type"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      @get-lists="getLists"
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
            <h3 class="py-3 mb-0 text-center">
              {{ $t('headers.service_owned_forms', { name: serviceName }) }}
            </h3>
            <div class="d-flex align-items-center justify-content-end">
              <button
                type="button"
                class="btn btn-link"
                @click="handleRedirectToBasicSettings"
              >
                {{ $t('buttons.form_basic_setting') }}
              </button>
              <!-- <button type="button" class="btn btn-link">
                <i class="ai-file-text me-1"></i>
                {{ $t('buttons.user_guide') }}
              </button> -->
            </div>

            <!-- Form tabs -->
            <form-tabs-component :activeTab="type" :service="service" />

            <div>
              <div
                class="d-flex justify-content-between align-items-center mt-2"
              >
                <select
                  class="form-select form-select-sm w-auto w-md-25"
                  id="select-input"
                  v-model="formData.sort_by"
                >
                  <option :value="FormSortTypes.NEWEST_ISSUE_DATE">
                    {{ $t('buttons.newest_issue_date') }}
                  </option>
                  <option :value="FormSortTypes.OLDEST_ISSUE_DATE">
                    {{ $t('buttons.oldest_issue_date') }}
                  </option>
                </select>
                <div class="flex-1 text-end">
                  <button
                    type="button"
                    class="btn btn-link p-0 me-2"
                    data-bs-toggle="modal"
                    :data-bs-target="searchModalId"
                  >
                    {{ $t('buttons.search') }}
                  </button>
                  <a
                    class="btn btn-link p-0 me-2"
                    @click.prevent="handleRedirectToCreatePage($event)"
                  >
                    {{ $t('buttons.form_creation') }}
                  </a>
                </div>
                <div class="btn-group dropstart form__lists">
                  <button
                    type="button"
                    class="btn btn-link c-primary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="ai-more-horizontal"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end.my-1">
                    <a
                      @click="openDeleteHistory()"
                      class="dropdown-item hoverable"
                    >
                      {{ $t('buttons.delete_history') }}
                    </a>
                    <a
                      @click="handleRedirectToCsvDownload($event)"
                      class="dropdown-item hoverable"
                    >
                      {{ $t('buttons.csv_output') }}
                    </a>
                  </div>
                </div>
              </div>
              <ul class="p-0 list-style-none list-horizontal">
                <li class="m-0" v-if="isSearch">
                  <button
                    class="btn btn-link p-0 me-1"
                    @click="handleClickDefault"
                  >
                    <i class="ai-x"></i>
                    <span class="c-link">{{ $t('labels.narrow_down') }}</span>
                  </button>
                </li>
              </ul>
              <div class="card shadow mt-2">
                <ul
                  class="form__lists list-group list-group-flush"
                  v-if="formList.length > 0"
                >
                  <li
                    v-for="(form, index) in formList"
                    :key="index"
                    class="
                      list-group-item
                      position-relative
                      list--white
                      p-3
                      hoverable
                    "
                    @click.prevent="handleRedirectToViewPage($event, form.id)"
                  >
                    <div
                      class="d-flex align-items-center justify-content-between"
                    >
                      <span class="me-2"
                        >{{ $t('labels.date_of_issue') }}：{{
                          form.issue_date
                        }}</span
                      >
                      <div>
                        <button
                          class="btn btn-link p-0 c-primary m-2"
                          @click.prevent="handleDuplicateForm($event, form.id)"
                        >
                          <i class="ai-copy"></i>
                        </button>
                        <button
                          class="btn btn-link p-0 c-primary m-2"
                          @click.prevent="handleEditForm($event, form.id)"
                        >
                          <i class="ai-edit-2"></i>
                        </button>
                        <button
                          class="btn btn-link p-0 c-primary"
                          @click.prevent="handleDeleteForm($event, form.id)"
                        >
                          <i class="ai-trash-2"></i>
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
                      <div class="flex-1">
                        <span class="d-block fs-5 ellipsis ellipsis--forms">
                          {{ form.title }}
                        </span>
                        <span class="c-light-gray">
                          {{ $t('labels.customer_name') }}：
                          {{ form.name }}
                        </span>
                      </div>
                      <span>
                        {{ $t('labels.price', { price: form.price }) }}
                      </span>
                    </div>
                    <div
                      class="d-flex align-items-center justify-content-between"
                    >
                      <span v-if="type === FormTypes.QUOTATION">
                        {{ $t('labels.term_of_validity') }}：
                        {{ form.expiration_date }}
                      </span>
                      <span v-else-if="type === FormTypes.INVOICE">
                        {{ $t('labels.payment_date') }}：
                        {{ form.payment_date }}
                      </span>
                      <span v-else-if="type === FormTypes.DELIVERY_SLIP">
                        {{ $t('labels.delivery_date') }}：
                        {{ form.delivery_deadline }}
                      </span>
                      <span v-else-if="type === FormTypes.PURCHASE_ORDER">
                        {{ $t('labels.delivery_deadline') }}：
                        {{ form.delivery_date }}
                      </span>
                      <span v-else-if="type === FormTypes.RECEIPT">
                        {{ $t('labels.receipt_date') }}：
                        {{ form.receipt_date }}
                      </span>
                      <div
                        v-if="type === FormTypes.QUOTATION"
                        class="btn-group dropdown"
                      >
                        <button
                          type="button"
                          class="btn btn-link dropdown-toggle"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                          @click.prevent="handleCreateFromMenu($event)"
                        >
                          {{ $t('buttons.creating_takeover_form') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end.my-1">
                          <a
                            href="#"
                            @click.prevent="
                              handleCreatePurchaseOrder($event, form.id)
                            "
                            class="dropdown-item"
                          >
                            {{ $t('buttons.create_from_purchase_order') }}
                          </a>
                          <a
                            href="#"
                            @click.prevent="
                              handleCreateInvoice($event, form.id)
                            "
                            class="dropdown-item"
                          >
                            {{ $t('buttons.create_from_invoice') }}
                          </a>
                          <a
                            href="#"
                            @click.prevent="
                              handleCreateDeliveryNote($event, form.id)
                            "
                            class="dropdown-item"
                          >
                            {{ $t('buttons.create_from_delivery_note') }}
                          </a>
                        </div>
                      </div>
                      <div v-if="type === FormTypes.INVOICE">
                        <a
                          href="#"
                          class="btn btn-link"
                          @click.prevent="handleCreateReceipt($event, form.id)"
                        >
                          {{ $t('buttons.create_receipt') }}
                        </a>
                      </div>
                    </div>
                  </li>
                </ul>
                <div v-else class="mt-2 p-2 d-flex justify-content-center mt-3">
                  {{ $t('labels.no_search_result') }}
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
import { defineComponent, ref, onMounted, computed, watch } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import QuotationSearchModal from '../components/QuotationSearchModal.vue';
import PurchaseSearchModal from '../components/PurchaseOrderSearchModal.vue';
import InvoiceSearchModal from '../components/InvoiceSearchModal.vue';
import DeliverySearchModal from '../components/DeliverySlipSearchModal.vue';
import ReceiptSearchModal from '../components/ReceiptSearchModal.vue';
import FormDeleteConfirmationModal from '../components/FormDeleteConfirmationModal.vue';
import FormTabsComponent from '../components/FormTabsComponent.vue';
import QuotationApiService from '../../../api/forms/quotations';
import InvoiceApiService from '../../../api/forms/invoices';
import DeliverySlipApiService from '../../../api/forms/delivery-slips';
import PurchaseOrderApiService from '../../../api/forms/purchase-orders';
import ReceiptApiService from '../../../api/forms/receipts';
import FormTypes from '../../../enums/FormTypes';
import FormSortTypes from '../../../enums/FormSortTypes';
import Pagination from '../../base/BasePagination.vue';

export default defineComponent({
  name: 'FormListIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    type: {
      type: [String, Number, null],
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
    FormDeleteConfirmationModal,
    FormTabsComponent,
    Pagination,
  },
  setup(props) {
    const quotationApiService = new QuotationApiService();
    const invoiceApiService = new InvoiceApiService();
    const deliverySlipApiService = new DeliverySlipApiService();
    const purchaseOrderApiService = new PurchaseOrderApiService();
    const receiptApiService = new ReceiptApiService();
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const service = ref(props.service);
    const type = ref(props.type);
    const formData = ref({
      sort_by: 0,
    });
    const paginationData = ref([]);
    const formList = ref([]);
    const isSearch = ref(false);

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

    /**
     * Get search modal ID based on form type
     *
     * @returns {string}
     */
    const searchModalId = computed(() => {
      switch (type.value) {
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
     * Get list of quotation
     *
     * @returns {void}
     */
    const getQuotations = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await quotationApiService.getLists(formData.value);
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
     * Get list of purchase order
     *
     * @returns {void}
     */
    const getPurchaseOrders = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await purchaseOrderApiService.getLists(
          formData.value
        );
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
     * Get list of invoices
     *
     * @returns {void}
     */
    const getInvoices = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await invoiceApiService.getLists(formData.value);
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
     * Get list of delivery slips
     *
     * @returns {void}
     */
    const getDeliverySlips = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await deliverySlipApiService.getLists(
          formData.value
        );
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
     * Get list of receipts
     *
     * @returns {void}
     */
    const getReceipts = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await receiptApiService.getLists(formData.value);
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
     * Get list based on form type
     *
     * @returns {void}
     */
    const getLists = async () => {
      switch (type.value) {
        case FormTypes.QUOTATION:
          getQuotations();
          break;
        case FormTypes.PURCHASE_ORDER:
          getPurchaseOrders();
          break;
        case FormTypes.DELIVERY_SLIP:
          getDeliverySlips();
          break;
        case FormTypes.INVOICE:
          getInvoices();
          break;
        case FormTypes.RECEIPT:
          getReceipts();
          break;
        default:
          getQuotations();
          break;
      }
    };

    /**
     * Redirect to Delete history page
     */
    const openDeleteHistory = () => {
      window.location.href = `${window.location.protocol}//${window.location.host}${window.location.pathname}/delete-history`;
    };

    /**
     * Search quotations
     *
     * @returns {void}
     */
    const searchQuotations = (data) => {
      formData.value.free_word = data.free_word;
      formData.value.issue_start_date = data.issue_start_date;
      formData.value.issue_end_date = data.issue_end_date;
      formData.value.expiration_start_date = data.expiration_start_date;
      formData.value.expiration_end_date = data.expiration_end_date;
      formData.value.amount_min = data.amount_min;
      formData.value.amount_max = data.amount_max;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search purchase orders
     *
     * @returns {void}
     */
    const searchPurchaseOrder = (data) => {
      formData.value.free_word = data.free_word;
      formData.value.issue_start_date = data.issue_start_date;
      formData.value.issue_end_date = data.issue_end_date;
      formData.value.delivery_start_date = data.delivery_start_date;
      formData.value.delivery_end_date = data.delivery_end_date;
      formData.value.amount_min = data.amount_min;
      formData.value.amount_max = data.amount_max;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search invoices
     *
     * @returns {void}
     */
    const searchInvoices = (data) => {
      formData.value.free_word = data.free_word;
      formData.value.issue_start_date = data.issue_start_date;
      formData.value.issue_end_date = data.issue_end_date;
      formData.value.payment_start_date = data.payment_start_date;
      formData.value.payment_end_date = data.payment_end_date;
      formData.value.amount_min = data.amount_min;
      formData.value.amount_max = data.amount_max;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Search delivery slips
     *
     * @returns {void}
     */
    const searchDeliverySlips = (data) => {
      formData.value.free_word = data.free_word;
      formData.value.issue_start_date = data.issue_start_date;
      formData.value.issue_end_date = data.issue_end_date;
      formData.value.delivery_deadline_start_date =
        data.delivery_deadline_start_date;
      formData.value.delivery_deadline_end_date =
        data.delivery_deadline_end_date;
      formData.value.amount_min = data.amount_min;
      formData.value.amount_max = data.amount_max;
      isSearch.value = data.is_search;

      getLists();
    };
    /**
     * Search receipts
     *
     * @returns {void}
     */
    const searchReceipts = (data) => {
      formData.value.free_word = data.free_word;
      formData.value.issue_start_date = data.issue_start_date;
      formData.value.issue_end_date = data.issue_end_date;
      formData.value.receipt_start_date = data.receipt_start_date;
      formData.value.receipt_end_date = data.receipt_end_date;
      formData.value.amount_min = data.amount_min;
      formData.value.amount_max = data.amount_max;
      isSearch.value = data.is_search;

      getLists();
    };

    /**
     * Remove search
     *
     * @returns {void}
     */
    const handleClickDefault = () => {
      const sort = formData.value.sort_by;
      formData.value = {};
      formData.value.sort_by = sort;
      isSearch.value = false;

      getLists();
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
     * Redirect to view page
     *
     * @param {integer} id
     * @returns {void}
     */
    const handleRedirectToViewPage = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      switch (type.value) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations/${id}`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders/${id}`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips/${id}`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices/${id}`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts/${id}`;
          break;
        default:
          window.location.href = `/forms/quotations/${id}`;
          break;
      }
    };

    /**
     * Redirect to basic settings page
     */
    const handleRedirectToBasicSettings = () => {
      window.location.href = `/forms/basic-settings`;
    };

    /**
     * Redirect to view page
     *
     * @returns {void}
     */
    const handleRedirectToCreatePage = (event) => {
      event.preventDefault();
      event.stopPropagation();

      switch (type.value) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations/create`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders/create`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips/create`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices/create`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts/create`;
          break;
        default:
          window.location.href = `/forms/quotations/create`;
          break;
      }
    };

    /**
     * Redirect to CSV download page
     *
     * @returns {void}
     */
    const handleRedirectToCsvDownload = (event) => {
      event.preventDefault();
      event.stopPropagation();

      switch (type.value) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations/csv-download`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders/csv-download`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips/csv-download`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices/csv-download`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts/csv-download`;
          break;
        default:
          window.location.href = `/forms/quotations/csv-download`;
          break;
      }
    };

    /**
     * Duplicate selected form
     *
     * @returns {void}
     */
    const handleDuplicateForm = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      switch (type.value) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations/${id}/duplicate`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders/${id}/duplicate`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips/${id}/duplicate`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices/${id}/duplicate`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts/${id}/duplicate`;
          break;
        default:
          window.location.href = `/forms/quotations/${id}/duplicate`;
          break;
      }
    };

    /**
     * Edit selected form
     *
     * @returns {void}
     */
    const handleEditForm = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      switch (type.value) {
        case FormTypes.QUOTATION:
          window.location.replace(`/forms/quotations/${id}/edit`);
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.replace(`/forms/purchase-orders/${id}/edit`);
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.replace(`/forms/delivery-slips/${id}/edit`);
          break;
        case FormTypes.INVOICE:
          window.location.replace(`/forms/invoices/${id}/edit`);
          break;
        case FormTypes.RECEIPT:
          window.location.replace(`/forms/receipts/${id}/edit`);
          break;
        default:
          window.location.replace(`/forms/quotations/${id}/edit`);
          break;
      }
    };

    /**
     * Delete selected form
     *
     * @returns {void}
     */
    const handleDeleteForm = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      /* eslint no-undef: 0 */
      const deleteModalNode = document.getElementById(
        'form-delete-confirmation'
      );
      const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
      const field = deleteModalNode.querySelector('input[name=id]');
      field.value = id;
      deleteModal.value.show();
    };

    /**
     * Toggle create from menu
     *
     * @returns {void}
     */
    const handleCreateFromMenu = (event) => {
      event.preventDefault();
      event.stopPropagation();
    };

    /**
     * Create new Purchase order from Quotation
     *
     * @returns {void}
     */
    const handleCreatePurchaseOrder = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/forms/purchase-orders/${id}/duplicate`;
    };

    /**
     * Create new Invoice from Quotation
     *
     * @returns {void}
     */
    const handleCreateInvoice = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/forms/invoices/${id}/duplicate`;
    };

    /**
     * Create new Delivery note from Quotation
     *
     * @returns {void}
     */
    const handleCreateDeliveryNote = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/forms/delivery-slips/${id}/duplicate`;
    };

    /**
     * Create new Receipt from Invoice
     *
     * @returns {void}
     */
    const handleCreateReceipt = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/forms/receipts/${id}/duplicate`;
    };

    /**
     * Watch for sort change
     */
    watch(
      () => [formData.value.sort_by],
      ([sort]) => {
        formData.value.sort_by = sort;
        getLists();
      }
    );

    onMounted(() => {
      getLists();
    });

    return {
      serviceName,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      formList,
      formData,
      FormSortTypes,
      FormTypes,
      changePage,
      paginationData,
      handleRedirectToViewPage,
      handleRedirectToCsvDownload,
      handleRedirectToBasicSettings,
      handleDuplicateForm,
      handleEditForm,
      handleDeleteForm,
      handleCreateFromMenu,
      handleCreatePurchaseOrder,
      handleCreateInvoice,
      handleCreateDeliveryNote,
      handleCreateReceipt,
      openDeleteHistory,
      searchQuotations,
      searchPurchaseOrder,
      searchInvoices,
      searchDeliverySlips,
      searchReceipts,
      isSearch,
      searchModalId,
      handleClickDefault,
      getLists,
      handleRedirectToCreatePage,
    };
  },
});
</script>
