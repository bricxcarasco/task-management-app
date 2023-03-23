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
    <update-history-preview-modal />

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

        <!-- Content-->
        <div class="col-12 col-md-9 offset-md-3">
          <div class="border-bottom">
            <button
              type="button"
              class="btn btn-link"
              @click="handleRedirectBack()"
            >
              <i class="ai-arrow-left"></i>
            </button>
          </div>
          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('headers.update_history') }}
          </p>
          <!-- Light table with striped rows -->
          <div v-if="historyList.length > 0" class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th>{{ $t('labels.operation_date') }}</th>
                  <th>{{ $t('labels.operation_description') }}</th>
                  <th>{{ $t('labels.operator') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(history, index) in historyList" :key="index">
                  <th scope="row">{{ index + 1 }}</th>
                  <td>{{ history.operation_datetime }}</td>
                  <td class="wrap">
                    {{ history.operation_details }}
                    <button
                      class="btn btn-link p-0 fs-xs"
                      type="button"
                      data-bs-toggle="modal"
                      data-bs-target="#form-preview"
                      @click="handlePreviewModal(history)"
                    >
                      {{ $t('buttons.details') }}
                    </button>
                  </td>
                  <td>{{ history.operator_email }}</td>
                </tr>
              </tbody>
            </table>
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-2 mb-3">
              <pagination :meta="paginationData" @changePage="changePage" />
            </div>
          </div>
          <div v-else class="p-3 text-center">
            <span>{{ $t('paragraphs.no_update_history') }}</span>
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
import FormTypes from '../../../enums/FormTypes';
import Pagination from '../../base/BasePagination.vue';
import UpdateHistoryPreviewModal from '../components/UpdateHistoryPreviewModal.vue';

export default defineComponent({
  name: 'UpdateHistory',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    form: {
      type: [Array, Object],
      required: true,
    },
    form_type: {
      type: [Number, null],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    Pagination,
    UpdateHistoryPreviewModal,
  },
  setup(props) {
    const formApiService = new FormApiService();
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
    const previousUrl = ref();

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
     * Get previous url
     *
     * @returns {string}
     */
    const getPreviousUrl = () => {
      switch (props.form_type) {
        case FormTypes.QUOTATION:
          return '/forms/quotations/';
        case FormTypes.PURCHASE_ORDER:
          return '/forms/purchase-orders/';
        case FormTypes.DELIVERY_SLIP:
          return '/forms/delivery-slips/';
        case FormTypes.INVOICE:
          return '/forms/invoices/';
        case FormTypes.RECEIPT:
          return '/forms/receipts/';
        default:
          return '/forms/quotations/';
      }
    };

    /**
     * Handle redirect back to form details
     *@returns {void}
     */
    const handleRedirectBack = () => {
      window.location.href = `${window.location.protocol}//${window.location.host}${previousUrl.value}${props.form.id}`;
    };

    /**
     * Show preview modal
     *
     * @returns {void}
     */
    const handlePreviewModal = async (history) => {
      const input = document.querySelector('#history_id');
      input.value = history.id;
    };

    /**
     * Get list of update history based on current form type
     *
     * @returns {void}
     */
    const getUpdateHistoryLists = async () => {
      // Start page loading
      setPageLoading(true);
      formData.value.form_id = props.form.id;

      try {
        const getListApi = await formApiService.getUpdateHistoryLists(
          formData.value
        );
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

    /**
     * Change page
     *
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getUpdateHistoryLists();
    };

    onMounted(() => {
      previousUrl.value = getPreviousUrl();
      getUpdateHistoryLists();
    });

    return {
      serviceName,
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      getUpdateHistoryLists,
      historyList,
      formData,
      FormTypes,
      changePage,
      paginationData,
      handlePreviewModal,
      handleRedirectBack,
      previousUrl,
    };
  },
});
</script>
