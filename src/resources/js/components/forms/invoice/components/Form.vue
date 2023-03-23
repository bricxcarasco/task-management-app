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

    <!-- Payment Terms Modal -->
    <payment-terms-modal
      ref="paymentTermsModalRef"
      :basic_setting="form_basic_setting"
      @set-alert="setAlert"
      @choose-payment-term="handleSelectedPaymentTerm"
    />

    <!-- Invoice Product Modal -->
    <invoice-product-modal
      @added-product="handleAddedProduct"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
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
            <form action="" ref="formRef" novalidate>
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <th>
                      {{ $t('labels.invoice_no') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.invoice_no != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="invoice_no"
                        v-model="formData.invoice_no"
                      />
                      <base-validation-error :errors="errors?.invoice_no" />
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
                      {{ $t('labels.postal_code') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.zipcode != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="zipcode"
                        v-model="formData.zipcode"
                        v-numbers-only
                      />
                      <base-validation-error :errors="errors?.zipcode" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.invoice_address') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.address != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        name="address"
                        v-model="formData.address"
                      />
                      <base-validation-error :errors="errors?.address" />
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.invoice_subject') }}
                      <span class="text-danger">*</span>
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.subject != null ? 'is-invalid' : ''"
                        type="text"
                        id="text-input"
                        :placeholder="
                          $t('placeholders.development_support_work')
                        "
                        name="subject"
                        v-model="formData.subject"
                      />
                      <base-validation-error :errors="errors?.subject" />
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
                      {{ $t('labels.payment_date') }}
                    </th>
                    <td class="text-end">
                      <div class="flatpickr date-picker_ymd2">
                        <input
                          type="text"
                          class="form--clear text-end c-primary flatpickr-input"
                          :class="
                            errors?.payment_date != null ? 'is-invalid' : ''
                          "
                          data-input
                          name="payment_date"
                          v-model="formData.payment_date"
                        />
                        <a
                          class="input-button cursor--pointer"
                          title="toggle"
                          data-toggle
                        >
                          <i class="ai-calendar"></i>
                        </a>
                        <base-validation-error :errors="errors?.payment_date" />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t('labels.payment_terms') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.payment_terms != null ? 'is-invalid' : ''
                        "
                        type="text"
                        id="text-input"
                        name="payment_terms"
                        v-model="formData.payment_terms"
                      />
                      <base-validation-error :errors="errors?.payment_terms" />
                      <button
                        v-if="
                          form_basic_setting?.payment_term_list &&
                          form_basic_setting?.payment_term_list?.length !== 0
                        "
                        class="btn btn-link fs-xs"
                        type="button"
                        @click.prevent="openPaymentTerms"
                      >
                        {{ $t('buttons.choose_from_payment_terms') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
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
                      {{ $t('labels.department_name') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="
                          errors?.issuer_department_name != null
                            ? 'is-invalid'
                            : ''
                        "
                        type="text"
                        id="text-input"
                        name="issuer_department_name"
                        v-model="issuer.department_name"
                      />
                      <base-validation-error
                        :errors="errors?.issuer_department_name"
                      />
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
                      {{ $t('labels.fax') }}
                    </th>
                    <td class="text-end">
                      <input
                        class="form-control"
                        :class="errors?.issuer_fax != null ? 'is-invalid' : ''"
                        type="text"
                        name="issuer_fax"
                        v-model="issuer.fax"
                      />
                      <base-validation-error :errors="errors?.issuer_fax" />
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
              <div
                class="
                  d-flex
                  align-items-center
                  justify-content-around
                  mt-2
                  border-bottom
                  pb-2
                "
              >
                <span class="w-25">
                  {{ $t('labels.logo') }}
                </span>
                <div class="flex-1 mx-auto text-center">
                  <!-- v-if="!hasUploadFile && logoDisplay" -->
                  <img
                    :src="logoDisplay"
                    @error="
                      Common.handleNotFoundImageException(
                        $event,
                        DefaultImageCategory.FORM
                      )
                    "
                    class="d-block mx-auto"
                    style="width: 150px"
                  />
                  <div
                    class="file-uploader file-uploader--quotation_product"
                    v-show="hasUploadFile"
                  >
                    <input
                      type="file"
                      class="js-form-invoice-file-uploader"
                      name="upload_file[]"
                      data-upload="/api/forms/file/process"
                      data-chunk="/api/forms/file"
                      data-revert="/api/forms/file"
                    />
                  </div>
                  <div
                    class="btn-group mt-2"
                    role="group"
                    aria-label="Basic example"
                  >
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click="openFileBrowser"
                    >
                      {{ $t('buttons.addition') }}
                    </button>
                    <button
                      type="button"
                      class="btn btn-danger"
                      v-if="isDeletableLogo"
                      @click="clearLocalUploadFiles"
                    >
                      {{ $t('buttons.delete') }}
                    </button>
                  </div>
                </div>
              </div>
              <p class="bg-gray mb-0 p-2">
                {{ $t('labels.item') }}
                <span class="text-danger">*</span>
              </p>
              <table class="table table-striped mb-0">
                <tbody>
                  <invoice-product-list
                    v-if="products.length > 0"
                    :products="products"
                    @delete-invoice-product="handleDeleteProduct"
                    @duplicate-invoice-product="handleDuplicateProduct"
                  />
                  <tr>
                    <th></th>
                    <td class="text-end">
                      <button
                        v-if="products.length < 10"
                        class="btn btn-link"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#quotation-product-form-modal"
                      >
                        <i class="ai-plus"></i>
                        {{ $t('buttons.add_item') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <base-validation-error
                :class="'d-block mb-3 mt-2'"
                :errors="errors?.products"
              />
              <p class="bg-gray mb-0 p-2">
                {{ $t('labels.remarks') }}
              </p>
              <div class="p-2">
                <textarea
                  class="form-control"
                  id="textarea-input"
                  rows="5"
                  :class="errors?.remarks != null ? 'is-invalid' : ''"
                  name="remarks"
                  v-model="formData.remarks"
                ></textarea>
              </div>
              <p class="bg-gray mb-0 p-2 hidden-element">
                {{ $t('labels.payment_notes') }}
              </p>
              <div class="p-2 hidden-element">
                <textarea
                  class="form-control"
                  id="textarea-input"
                  rows="5"
                  :class="errors?.payment_notes != null ? 'is-invalid' : ''"
                  name="payment_notes"
                  v-model="formData.payment_notes"
                ></textarea>
              </div>
              <p class="bg-gray mb-0 p-2">
                {{ $t('labels.payee_information') }} ({{
                  $t('labels.payee_information_input_reminder')
                }})
              </p>
              <div class="p-2">
                <textarea
                  class="form-control"
                  id="textarea-input"
                  rows="5"
                  :class="errors?.payee_information != null ? 'is-invalid' : ''"
                  name="payee_information"
                  v-model="formData.payee_information"
                ></textarea>
              </div>
              <div class="mt-4 text-center">
                <base-button
                  :title="this.$i18n.t('buttons.next')"
                  :buttonType="'primary'"
                  :disabled="isSubmitButtonDisabled"
                  @handleClick="handleSubmitForm"
                />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import _ from 'lodash';
import Common from '../../../../common';
import { objectifyForm } from '../../../../utils/objectifyForm';
import BpheroConfig from '../../../../config/bphero';
import InvoicesApiService from '../../../../api/forms/invoices';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import BaseAlert from '../../../base/BaseAlert.vue';
import BaseValidationError from '../../../base/BaseValidationError.vue';
import ConnectionListModal from '../../modals/ConnectionListModal.vue';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';
import PaymentTermsModal from '../../modals/PaymentTermsModal.vue';
import InvoiceProductModal from '../../modals/ProductModal.vue';
import InvoiceProductList from '../product/ProductList.vue';
import BaseButton from '../../../base/BaseButton.vue';
import FormTypes from '../../../../enums/FormTypes';

export default defineComponent({
  name: 'InvoiceCreateForm',
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
    is_duplicate: {
      type: [Boolean],
      default: false,
    },
    is_edit: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    BaseButton,
    BaseValidationError,
    ConnectionListModal,
    PaymentTermsModal,
    InvoiceProductModal,
    InvoiceProductList,
  },
  setup(props, { emit }) {
    const invoicesApiService = new InvoicesApiService();
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });

    let formModalNode = null;
    let formModal = null;
    const errors = ref(null);
    const formRef = ref({});
    const connectionListModalRef = ref(null);
    const paymentTermsModalRef = ref(null);

    const hasUploadFile = ref(false);
    const fileUploader = ref({});
    const file = ref({});
    const logo = ref(props.existing_data.logo);
    const issuerImageDefault = ref(props.existing_data.issuer_image);
    const issuer = ref({});
    const supplier = ref(props.existing_data.supplier ?? {});
    const supplierName = ref(
      props.existing_data?.supplier_name ?? props.existing_data.supplier?.name
    );
    const products = ref(props.existing_data.products ?? []);
    const formData = ref({});
    const basicSetting = ref(props.form_basic_setting);
    const hasPendingUpload = ref(false);
    const isSelectedSupplierConnected = ref(
      props.existing_data.is_supplier_connected ?? null
    );
    const logoDisplay = ref(null);
    let issuerImage = props.existing_data.issuer_image ?? null;

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
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      const supplierInput = document.getElementById('text-supplier');
      formData.value = object;
      formData.value.type = FormTypes.INVOICE;
      formData.value.mode = 'validation';
      formData.value.logo = logo.value;
      formData.value.supplier = supplier.value;
      formData.value.products = products.value;
      formData.value.supplier_name = supplierName.value;
      formData.value.is_supplier_connected = supplierInput.disabled;
      if (!formData.value.logo) {
        formData.value.issuer_image = issuerImageDefault.value;
      }
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
      if (!_.isEmpty(formBasicSetting) && !formData.value?.issuer_name) {
        issuer.value = {
          name: formBasicSetting.name,
          department_name: formBasicSetting.department_name,
          address: formBasicSetting.address,
          tel: formBasicSetting.tel,
          fax: formBasicSetting.fax,
          business_number: formBasicSetting.business_number,
        };
      }
    };

    /**
     * Load form basic setting in issuer info
     *
     * @returns {void}
     */
    const loadExistingData = () => {
      const existingData = props.existing_data;
      if (!_.isEmpty(existingData)) {
        formData.value.invoice_no =
          existingData?.invoice_no ?? existingData.form_no;
        supplierName.value =
          existingData?.supplier_name ?? existingData.supplier.name;
        supplier.value = {
          service: existingData.supplier.service,
          id: existingData.supplier.id,
          name: existingData.supplier.name,
        };
        formData.value.zipcode = existingData.zipcode;
        formData.value.address = existingData.address;
        formData.value.subject = existingData?.subject ?? existingData.title;
        formData.value.issue_date = existingData.issue_date;
        formData.value.payment_terms = existingData.payment_terms;
        formData.value.payment_date = existingData.payment_date;
        formData.value.remarks = existingData.remarks;
        formData.value.payment_notes =
          existingData?.payment_notes ?? existingData.issuer_payment_notes;
        formData.value.payee_information =
          existingData?.payee_information ??
          existingData.issuer_payee_information;
        formData.value.issuer_image = existingData.issuer_image;
        // Issuer information
        issuer.value = {
          name: existingData.issuer_name,
          department_name: existingData.issuer_department_name,
          address: existingData.issuer_address,
          tel: existingData.issuer_tel,
          fax: existingData.issuer_fax,
          business_number: existingData.issuer_business_number,
        };
      }
    };

    /**
     * Open file browser
     */
    const openFileBrowser = () => {
      fileUploader.value.pond.browse();
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
     * Open payment terms modal
     */
    const openPaymentTerms = () => {
      paymentTermsModalRef.value.show();
    };

    /**
     * Close payment terms modal
     */
    const closePaymentTerms = () => {
      paymentTermsModalRef.value.hide();
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
     * Construct form data
     *
     * @param {Object} data
     * @returns {Object}
     */
    const constructFormData = (data) => ({
      name: data.name,
      unit: data.unit,
      quantity: data.quantity,
      unit_price: data.unit_price,
      amount: data.amount,
      tax_distinction: data.tax_distinction,
    });

    /**
     * Handle added product in invoice product form modal
     *
     * @param {object} invoiceProduct
     */
    const handleAddedProduct = (invoiceProduct) => {
      products.value.push(invoiceProduct);
    };

    /**
     * Handle duplicate product in invoice product form modal
     *
     * @param {object} invoiceProduct
     */
    const handleDuplicateProduct = (product) => {
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, constructFormData(product));
      formModal.value.show();
    };

    /**
     * Handle delete product in invoice product list
     *
     * @param {string} productId
     */
    const handleDeleteProduct = (productId) => {
      products.value.splice(productId, 1);
    };

    /**
     * Display Selected Payment Term
     *
     * @param {string} paymentTerm
     */
    const handleSelectedPaymentTerm = (paymentTerm) => {
      formData.value.payment_terms = paymentTerm;
      closePaymentTerms();
    };

    /**
     * Get total products price
     */
    const getProductsPrice = () => {
      formData.value.price = products.value
        .map((product) => parseFloat(product.amount))
        .reduce((prev, curr) => parseFloat(prev) + parseFloat(curr), 0);
    };

    /**
     * Redirect back to receiving account setting page
     *
     * @returns {void}
     */
    const redirectToFormListPage = () => {
      if (props.existing_data.type === FormTypes.QUOTATION) {
        window.location.href = `/forms/quotations`;
      } else {
        window.location.href = `/forms/invoices`;
      }
    };

    /**
     * Handle submit create invoice form
     *
     * @param {string} productId
     */
    const getImage = async (event) => {
      event.preventDefault();

      await invoicesApiService
        .getImage()
        .catch(() => {
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Default image
     */
    const defaultImage = computed(() => {
      if (!basicSetting || !basicSetting.value?.image) {
        return null;
      }

      return `${basicSetting.value.image}`;
    });

    /**
     * Get form logo
     *
     * @return {string}
     */
    const getLogo = () => {
      if (props.is_edit || props.is_duplicate) {
        if (
          issuerImage &&
          !logo.value &&
          (issuerImage.startsWith('https://') ||
            issuerImage.startsWith('http://'))
        ) {
          return issuerImage;
        }

        if (issuerImage && !logo.value) {
          return `/hero-storage/public/forms/issuer/profile_image/${issuerImage}`;
        }

        if (!issuerImage && !logo.value) {
          return null;
        }

        return logo.value
          ? `/api/forms/file/restore?code=${logo.value}`
          : `/hero-storage/public/forms/issuer/profile_image/${issuerImage}`;
      }

      return logo.value
        ? `/api/forms/file/restore?code=${logo.value}`
        : defaultImage.value;
    };

    /**
     * Remove uploaded files
     */
    const clearLocalUploadFiles = () => {
      logo.value = '';
      fileUploader.value.clearFiles();

      // Set image variables as null
      issuerImage = null;
      issuerImageDefault.value = null;
      logoDisplay.value = getLogo();
    };

    /**
     * Handle submit create invoice form
     *
     * @param {string} productId
     */
    const handleSubmitForm = async () => {
      updateModel();
      getProductsPrice();

      // Reinitialize state
      setPageLoading(true);
      errors.value = null;
      resetAlert();

      formData.value.zipcode =
        formData.value.zipcode ??
        parseInt(formData.value.zipcode, 10).toString();

      // Handle responses
      invoicesApiService
        .validateCreateInvoiceForm(formData.value)
        .then(() => emit('switch-confirmation-preview', formData.value))
        .catch((error) => {
          const responseData = error.response?.data;

          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Attach file uploader update file event listener
     */
    const attachUpdateFilesListener = () => {
      fileUploader.value.pond.on('addfile', () => {
        file.value = {};
        delete logo.value;

        hasUploadFile.value = true;
        hasPendingUpload.value = true;
      });

      fileUploader.value.pond.on('removefile', () => {
        // Remove local file info
        delete logo.value;
        hasUploadFile.value = false;
        hasPendingUpload.value = false;
      });
    };

    /**
     * Attach file uploader process file event listener
     */
    const attachProcessFileListener = () => {
      fileUploader.value.pond.on('processfile', (error, item) => {
        if (!error && hasPendingUpload.value) {
          logo.value = item.serverId;
        }
        hasPendingUpload.value = false;
        logoDisplay.value = getLogo();
      });
    };

    /**
     * Attach file uploader warning event listener
     */
    const attachUploadWarningListener = () => {
      fileUploader.value.pond.on('warning', (error) => {
        if (error.body === 'Max files') {
          const errorMessage = i18n.global.t(
            'messages.chat.max_upload_files_reached'
          );
          setAlert('failed', errorMessage);

          return;
        }

        setAlert('failed', error.body);
      });
    };

    /**
     * Initialize file uploader for
     */
    const initializeFileuploader = () => {
      /* eslint no-undef: 0 */
      fileUploader.value = FileUploaderFacade({
        selector: '.js-form-invoice-file-uploader',
        maxFileSize: BpheroConfig.FORM_SERVICE_MAX_FILE_SIZE,
        hasImagePreview: true,
        allowMultiple: false,
        allowReplace: true,
        chunkUploads: true,
        instantUpload: true,
        allowReorder: false,
        allowProcess: false,
        allowRemove: false,
        allowRevert: false,
        labelIdle: '',
        acceptedFileTypes: [
          'image/jpeg',
          'image/jpg',
          'image/pjpeg',
          'image/png',
        ],
        fileValidateTypeLabelExpectedTypesMap: {
          'image/jpeg': '.jpeg',
          'image/jpg': '.jpg',
          'image/pjpeg': null,
          'image/png': '.png',
        },
      });
    };

    /**
     * Load supplier field depending on current input of
     */
    const loadSupplierField = () => {
      const supplierInput = document.getElementById('text-supplier');
      const btnRemoveconnectedSupplier = document.getElementById(
        'btn-remove-connected-supplier'
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
     * Computed property for identifying when to disable submit button
     */
    const isSubmitButtonDisabled = computed(() => hasPendingUpload.value);

    /**
     * Computed property for deletable logo
     */
    const isDeletableLogo = computed(() => {
      const isBasicInfoLogo = logoDisplay.value === basicSetting.value?.image;

      // Has user uploaded image
      if (hasPendingUpload.value) {
        return true;
      }

      // Editable images
      if (props.is_edit || props.is_duplicate) {
        if (logoDisplay.value === null) {
          return false;
        }

        return logoDisplay.value || logo.value;
      }

      return !isBasicInfoLogo || logo.value;
    });

    /**
     * Mounted properties
     */
    onMounted(() => {
      initializeFileuploader();
      attachUpdateFilesListener();
      attachUploadWarningListener();
      attachProcessFileListener();
      loadFormBasicSetting();
      loadExistingData();
      loadSupplierField();
      initializeDatePickers();

      formModalNode = document.getElementById('quotation-product-form-modal');
      formModal = computed(() => new bootstrap.Modal(formModalNode));

      logoDisplay.value = getLogo();
    });

    return {
      BpheroConfig,
      Common,
      DefaultImageCategory,
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
      logo,
      products,
      hasUploadFile,
      openFileBrowser,
      clearLocalUploadFiles,
      file,
      formRef,
      paymentTermsModalRef,
      connectionListModalRef,
      openConnectionList,
      openPaymentTerms,
      handleSelectedConnectedSupplier,
      handleRemoveSelectedConnectedSupplier,
      handleSelectedPaymentTerm,
      handleAddedProduct,
      handleDuplicateProduct,
      handleDeleteProduct,
      handleSubmitForm,
      getImage,
      logoDisplay,
      basicSetting,
      defaultImage,
      isSubmitButtonDisabled,
      hasPendingUpload,
      redirectToFormListPage,
      isDeletableLogo,
    };
  },
});
</script>
