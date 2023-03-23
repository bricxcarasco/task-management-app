<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Connection List Modal -->
    <connection-list-modal
      ref="connectionListModalRef"
      :service="service"
      @set-alert="setAlert"
      @choose-target-connection="handleSelectedConnectedSupplier"
    />

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div class="pb-4 pb-md-0">
            <a class="btn btn-link" @click="redirectToFormListPage">
              <i class="ai-arrow-left"></i>
            </a>
            <p class="bg-gray mb-0 p-2">
              {{ $t('labels.basic_information') }}
            </p>
            <form action="" @submit="handleSubmitForm" ref="formRef" novalidate>
              <!-- Receipt Details -->
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <th>
                      {{ $t('labels.receipt_title') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.title != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="title"
                        v-model="formData.title"
                      />
                      <base-validation-error :errors="errors?.title" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.suppliers') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.supplier_name != null ? 'is-invalid' : ''
                        "
                        type="text"
                        id="text-supplier"
                        v-model="supplierName"
                      />
                      <base-validation-error :errors="errors?.supplier_name" />
                      <button
                        class="btn btn-link fs-xs"
                        type="button"
                        @click.prevent="openConnectionList"
                      >
                        {{ $t('buttons.choose_from_connections') }}
                      </button>
                      <button
                        class="btn btn-link fs-xs d-none"
                        id="btn-remove-connected-supplier"
                        type="button"
                        @click.prevent="handleRemoveSelectedConnectedSupplier"
                      >
                        {{ $t('buttons.delete') }}
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.date_of_issue') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <div class="flatpickr date-picker_ymd1">
                        <input
                          type="text"
                          class="form--clear text-end c-primary flatpickr-input"
                          :class="
                            errors?.issue_date != null ? 'is-invalid' : ''
                          "
                          data-input
                          name="issue_date"
                          v-model="formData.issue_date"
                        />
                        <a
                          class="input-button cursor--pointer"
                          title="toggle"
                          data-toggle
                        >
                          <i class="ai-calendar"></i>
                        </a>
                        <base-validation-error :errors="errors?.issue_date" />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.receipt_date') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <div class="flatpickr date-picker_ymd2">
                        <input
                          type="text"
                          class="form--clear text-end c-primary flatpickr-input"
                          :class="
                            errors?.receipt_date != null ? 'is-invalid' : ''
                          "
                          data-input
                          name="receipt_date"
                          v-model="formData.receipt_date"
                        />
                        <a
                          class="input-button cursor--pointer"
                          title="toggle"
                          data-toggle
                        >
                          <i class="ai-calendar"></i>
                        </a>
                        <base-validation-error :errors="errors?.receipt_date" />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.receipt_amount') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <div
                        class="d-flex align-items-center justify-content-around"
                      >
                        <input
                          class="form-control me-2"
                          :class="
                            errors?.receipt_amount != null ? 'is-invalid' : ''
                          "
                          type="text"
                          id="text-input"
                          name="receipt_amount"
                          v-model="formData.receipt_amount"
                          v-numbers-only
                        />
                        <span>
                          {{ $t('labels.quotation_circle') }}
                        </span>
                      </div>
                      <div class="receipt-amount-error">
                        <base-validation-error
                          :errors="errors?.receipt_amount"
                        />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.refer_receipt_number') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.refer_receipt_no != null ? 'is-invalid' : ''
                        "
                        type="text"
                        id="text-input"
                        name="refer_receipt_no"
                        v-model="formData.refer_receipt_no"
                        v-numbers-only
                      />
                      <base-validation-error
                        :errors="errors?.refer_receipt_no"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
              <!-- Issuer Information -->
              <p class="bg-gray mb-0 p-2">
                {{ $t('labels.issuer_information') }}
              </p>
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <th>
                      {{ $t('labels.store_and_trade_name') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.issuer_name != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="issuer_name"
                        v-model="issuer.name"
                      />
                      <base-validation-error :errors="errors?.issuer_name" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.address') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.issuer_address != null ? 'is-invalid' : ''
                        "
                        type="text"
                        id="text-input"
                        name="issuer_address"
                        v-model="issuer.address"
                      />
                      <base-validation-error :errors="errors?.issuer_address" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.tel') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.issuer_tel != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="issuer_tel"
                        v-model="issuer.tel"
                      />
                      <base-validation-error :errors="errors?.issuer_tel" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.business_number') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.issuer_business_number != null
                            ? 'is-invalid'
                            : ''
                        "
                        type="text"
                        id="text-input"
                        name="issuer_business_number"
                        v-model="issuer.business_number"
                      />
                      <base-validation-error
                        :errors="errors?.issuer_business_number"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary">
                  {{ $t('buttons.next') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import _ from 'lodash';
import { objectifyForm } from '../../../../utils/objectifyForm';
import ReceiptApiService from '../../../../api/forms/receipts';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import BaseAlert from '../../../base/BaseAlert.vue';
import BaseValidationError from '../../../base/BaseValidationError.vue';
import ConnectionListModal from '../../modals/ConnectionListModal.vue';
import FormTypes from '../../../../enums/FormTypes';

export default defineComponent({
  name: 'ReceiptCreateForm',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    form_basic_setting: {
      type: [Array, Object],
    },
    existing_data: {
      type: [Array, Object],
    },
    operation: {
      type: String,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    BaseValidationError,
    ConnectionListModal,
  },
  setup(props, { emit }) {
    const receiptsApiService = new ReceiptApiService();
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });

    const errors = ref(null);
    const formRef = ref({});
    const connectionListModalRef = ref(null);

    const issuer = ref({});
    const supplier = ref(props.existing_data.supplier ?? {});
    const supplierName = ref(
      props.existing_data?.supplier_name ?? props.existing_data.supplier?.name
    );
    const products = ref([]);
    const formData = ref({});
    const isSelectedSupplierConnected = ref(
      props.existing_data.is_supplier_connected ?? null
    );

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
     * Reset formData and error state
     *
     * @returns {void}
     */
    const resetModal = () => {
      formData.value = {};
      errors.value = null;
    };

    /**
     * Redirect back to form list page
     *
     * @returns {void}
     */
    const redirectToFormListPage = () => {
      if (props.existing_data.type === FormTypes.INVOICE) {
        window.location.href = `/forms/invoices`;
      } else {
        window.location.href = `/forms/receipts`;
      }
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      const supplierInput = document.getElementById('text-supplier');
      formData.value = object;
      formData.value.type = FormTypes.RECEIPT;
      formData.value.mode = 'validation';
      formData.value.supplier = supplier.value;
      formData.value.supplier_name = supplierName.value;
      formData.value.is_supplier_connected = supplierInput.disabled;
      if (!supplierInput.disabled && formData.value.supplier_name) {
        formData.value.supplier = {
          service: 'temp_service',
          id: 'temp_id',
          name: 'temp_name',
        };
      }
    };

    /**
     * Load form basic setting in issuer info
     *
     * @returns {void}
     */
    const loadFormBasicSetting = () => {
      const formBasicSetting = props.form_basic_setting;
      if (!_.isEmpty(formBasicSetting)) {
        issuer.value = {
          name: formBasicSetting.name,
          address: formBasicSetting.address,
          tel: formBasicSetting.tel,
          business_number: formBasicSetting.business_number,
        };
      }
    };

    /**
     * Load existing data to form
     *
     * @returns {void}
     */
    const loadExistingData = () => {
      const existingData = props.existing_data;
      if (!_.isEmpty(existingData)) {
        formData.value.title = existingData.title;
        supplierName.value =
          existingData?.supplier_name ?? existingData.supplier.name;
        supplier.value = {
          service: existingData.supplier.service,
          id: existingData.supplier.id,
          name: existingData.supplier.name,
        };
        formData.value.issue_date = existingData.issue_date;
        formData.value.receipt_date = existingData.receipt_date;
        formData.value.receipt_amount =
          parseInt(existingData.receipt_amount, 10) ??
          Number(existingData.price);
        formData.value.refer_receipt_no = existingData.refer_receipt_no;

        // Issuer information
        issuer.value = {
          name: existingData.issuer_name,
          address: existingData.issuer_address,
          tel: existingData.issuer_tel,
          business_number: existingData.issuer_business_number,
        };
      }
    };

    /**
     * Load supplier field depending on current input of
     */
    const loadSupplierField = () => {
      const supplierInput = document.querySelector('#text-supplier');
      const btnRemoveconnectedSupplier = document.querySelector(
        '#btn-remove-connected-supplier'
      );
      if (
        isSelectedSupplierConnected.value === false ||
        (!props.existing_data.supplier_rio_id &&
          !props.existing_data.supplier_neo_id)
      ) {
        supplierInput.disabled = false;
        btnRemoveconnectedSupplier.classList.add('d-none');
      }
      if (
        isSelectedSupplierConnected.value === true ||
        props.existing_data.supplier_rio_id ||
        props.existing_data.supplier_neo_id
      ) {
        supplierInput.disabled = true;
        btnRemoveconnectedSupplier.classList.remove('d-none');
      }
    };

    /**
     * Open connection list modal
     *
     * @param {int} id
     */
    const openConnectionList = () => {
      connectionListModalRef.value.getConnectionList();
      connectionListModalRef.value.show();
    };

    /**
     * Close connection list modal
     *
     */
    const closeConnectionList = () => {
      connectionListModalRef.value.setLoading(false);
      connectionListModalRef.value.hide();
    };

    /**
     * Display Selected Connected Supplier
     *
     * @param {object} chosenSupplier
     */
    const handleSelectedConnectedSupplier = (chosenSupplier) => {
      const supplierInput = document.getElementById('text-supplier');
      const btnRemoveconnectedSupplier = document.getElementById(
        'btn-remove-connected-supplier'
      );
      supplier.value = {
        service: chosenSupplier.service,
        id: chosenSupplier.id,
        name: chosenSupplier.kana,
      };
      supplierName.value = chosenSupplier.name;
      formData.value.address = chosenSupplier.address;
      supplierInput.disabled = true;
      btnRemoveconnectedSupplier.classList.remove('d-none');
      closeConnectionList();
    };

    /**
     * Display Selected Connected Supplier
     *
     * @param {object} chosenSupplier
     */
    const handleRemoveSelectedConnectedSupplier = () => {
      const supplierInput = document.getElementById('text-supplier');
      const btnRemoveconnectedSupplier = document.getElementById(
        'btn-remove-connected-supplier'
      );
      supplier.value = {};
      supplierName.value = '';
      formData.value.address = '';
      supplierInput.disabled = false;
      btnRemoveconnectedSupplier.classList.add('d-none');
    };

    /**
     * Handle submit create quotation form
     *
     * @param {string} productId
     */
    const handleSubmitForm = async (event) => {
      event.preventDefault();

      updateModel();

      // Reinitialize state
      setPageLoading(true);
      errors.value = null;
      resetAlert();

      formData.value.refer_receipt_no =
        formData.value.refer_receipt_no ??
        parseInt(formData.value.refer_receipt_no, 10).toString();
      formData.value.receipt_amount = parseInt(
        formData.value.receipt_amount,
        10
      );

      // Handle responses
      receiptsApiService
        .validateCreateReceiptForm(formData.value)
        .then(() => emit('switch-confirmation-preview', formData.value))
        .catch((error) => {
          const responseData = error.response?.data;

          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
            if (errors.value.receipt_amount) {
              const receiptAmountError = document
                .querySelector('.receipt-amount-error')
                .querySelector('.invalid-feedback');
              receiptAmountError.style.display = 'block';
            }

            return;
          }

          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Load supplier field depending on current input of
     */
    const initializeDatePickers = () => {
      /* eslint no-undef: 0 */
      flatpickr('.date-picker_ymd1', {
        wrap: true,
        locale: 'ja',
        altFormat: 'Y-m-d',
        disableMobile: 'true',
      });

      /* eslint no-undef: 0 */
      flatpickr('.date-picker_ymd2', {
        wrap: true,
        locale: 'ja',
        altFormat: 'Y-m-d',
        disableMobile: 'true',
      });
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      loadFormBasicSetting();
      loadExistingData();
      loadSupplierField();
      initializeDatePickers();
    });

    return {
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      resetModal,
      errors,
      formData,
      issuer,
      supplierName,
      products,
      formRef,
      connectionListModalRef,
      openConnectionList,
      handleSelectedConnectedSupplier,
      handleRemoveSelectedConnectedSupplier,
      handleSubmitForm,
      redirectToFormListPage,
    };
  },
});
</script>
