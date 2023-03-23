<template>
  <div>
    <!-- Preview modal -->
    <preview-modal
      :form_data="formData"
      :product_price_with_taxes="productPriceWithTaxes"
      :logo="logoDisplay"
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
        break-word
      "
    >
      <div class="row">
        <page-loader :show="pageLoading" />
        <div class="col-12 col-md-9 offset-md-3">
          <div
            class="
              d-flex
              align-items-center
              justify-content-between
              border-bottom
            "
          >
            <a href="#" @click="handleClickEdit" class="btn btn-link">
              <i class="ai-arrow-left"></i>
            </a>
            <div class="d-flex align-items-center justify-content-around"></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <button
              type="button"
              class="btn btn-link"
              data-bs-toggle="modal"
              data-bs-target="#form-preview"
            >
              {{ $t('buttons.form_preview') }}
            </button>
          </div>

          <!-- Form information -->
          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.basic_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>{{ $t('labels.purchase_order_no') }}</th>
                  <td>{{ formData.form_no }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.invoice_subject') }}</th>
                  <td>{{ formData.title }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.suppliers') }}</th>
                  <td>{{ formData?.supplier_name }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.postal_code') }}</th>
                  <td>{{ formData.zipcode }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.invoice_address') }}</th>
                  <td>{{ formData.address }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.date_of_issue') }}</th>
                  <td>{{ formData.issue_date }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.delivery_location') }}</th>
                  <td>{{ formData.delivery_address }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.delivery_deadline') }}</th>
                  <td>{{ formData.delivery_date }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.payment_terms') }}</th>
                  <td>{{ formData.payment_terms }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Form products section -->
          <div v-if="form.products.length > 0">
            <p class="mb-0 bg-dark-gray p-2 c-white">{{ $t('labels.item') }}</p>
            <div class="p-4">
              <ol>
                <div v-for="(product, index) in form.products" :key="index">
                  <li>
                    <table class="table bg-gray">
                      <tbody>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.quotation_product_name') }}：
                          </th>
                          <td>{{ product.name }}</td>
                        </tr>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.quotation_quantity') }}：
                          </th>
                          <td>{{ product.quantity }}</td>
                        </tr>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.quotation_unit') }}：
                          </th>
                          <td>{{ product.unit }}</td>
                        </tr>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.quotation_unit_price') }}：
                          </th>
                          <td>
                            {{ product.unit_price ?? 0
                            }}{{ $t('labels.quotation_circle') }}
                          </td>
                        </tr>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.amount_of_money') }}：
                          </th>
                          <td>
                            {{ product.quantity * product.unit_price
                            }}{{ $t('labels.quotation_circle') }}
                          </td>
                        </tr>
                        <tr>
                          <th class="w-md-25">
                            {{ $t('labels.quotation_tax_classification') }}：
                          </th>
                          <td>
                            {{
                              FormQuotationProductTaxClassification[
                                product.tax_distinction
                              ]
                            }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </li>
                </div>
              </ol>
              <div>
                <div class="mt-4">
                  <table class="mx-auto table--border-0">
                    <tr>
                      <td>{{ $t('labels.sub_total') }}</td>
                      <td>
                        ：{{ moneyFormat(totalValue) }}
                        {{ $t('labels.quotation_circle') }}
                      </td>
                    </tr>
                    <tr>
                      <td>{{ $t('labels.gst_10') }}</td>
                      <td>
                        ：{{ moneyFormat(tax10Percent) }}
                        {{ $t('labels.quotation_circle') }}
                      </td>
                    </tr>
                    <tr>
                      <td>{{ $t('labels.consumption_tax') }}</td>
                      <td>
                        ：{{ moneyFormat(tax8Percent) }}
                        {{ $t('labels.quotation_circle') }}
                      </td>
                    </tr>
                  </table>
                </div>
                <p class="fs-4 mt-3 text-center">
                  <span
                    >{{ $t('labels.total') }} {{ moneyFormat(overAllPrice) }}
                    {{ $t('labels.quotation_circle') }}</span
                  >
                </p>
              </div>
            </div>
          </div>

          <!-- Remarks -->
          <div v-if="form.remarks">
            <p class="mb-0 bg-dark-gray p-2 c-white">
              {{ $t('labels.remarks') }}
            </p>
            <div class="p-4">
              <ul class="list-style-none">
                <li class="paragraph-content">{{ form.remarks }}</li>
              </ul>
            </div>
          </div>

          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.issuer_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>{{ $t('labels.store_and_trade_name') }}</th>
                  <td>{{ form.issuer_name }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.department_name') }}</th>
                  <td>{{ form.issuer_department_name }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.invoice_address') }}</th>
                  <td>{{ form.issuer_address }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.tel') }}</th>
                  <td>{{ form.issuer_tel }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.fax') }}</th>
                  <td>{{ form.issuer_fax }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.business_number') }}</th>
                  <td>{{ form.issuer_business_number }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.logo') }}</th>
                  <td>
                    <img
                      class="d-block"
                      v-if="logoDisplay"
                      :src="logoDisplay"
                      @error="
                        Common.handleNotFoundImageException(
                          $event,
                          DefaultImageCategory.FORM
                        )
                      "
                      alt="name"
                      width="110"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 text-center">
            <button
              type="button"
              class="btn btn-dark btnLeft"
              @click="handleClickEdit"
            >
              {{ $t('buttons.fix') }}
            </button>
            <button
              type="button"
              class="btn btn-primary btnRight"
              @click="handleClickSubmit"
            >
              {{ $t('buttons.next') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import PurchaseOrderApiService from '../../../../api/forms/purchase-orders';
import FormQuotationProductTaxClassification from '../../../../enums/FormQuotationProductTaxClassification';
import ProductTaxDistinction from '../../../../enums/ProductTaxDistinction';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import BpheroConfig from '../../../../config/bphero';
import Common from '../../../../common';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';
import FormOperation from '../../../../enums/FormOperationTypes';
import PreviewModal from '../../components/PreviewModal.vue';

export default defineComponent({
  name: 'PurchaseOrderConfirmationPreview',
  props: {
    form: {
      type: [Array, Object],
    },
    form_basic_setting: {
      type: [Array, Object],
    },
    is_edit: {
      type: Boolean,
      required: false,
      default: false,
    },
    operation: {
      type: String,
    },
  },
  components: {
    PageLoader,
    PreviewModal,
  },
  setup(props, { emit }) {
    const formData = ref(props.form);
    const basicSetting = ref(props.form_basic_setting);
    const purchaseOrderApiService = new PurchaseOrderApiService();
    const pageLoading = ref(false);
    const operationMode = ref(props.operation);

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    const logoDisplayTemp = computed(
      () =>
        `/api/forms/delivery-slips/images/restore?code=${formData.value.logo}`
    );

    /**
     * Calculate prices
     */
    const calculation = computed(() => {
      let tax10 = 0;
      let tax8 = 0;
      let total = 0;
      let overall = 0;

      formData.value.products.forEach((product) => {
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

      return {
        totalValue: total,
        tax10Percent: tax10,
        tax8Percent: tax8,
        overAllPrice: overall,
      };
    });

    const { totalValue, tax10Percent, tax8Percent, overAllPrice } =
      calculation.value;

    const productPriceWithTaxes = {
      subTotal: totalValue,
      totalWithGST: tax10Percent,
      totalWitConsumptionTax: tax8Percent,
      total: overAllPrice,
    };

    /**
     * Default image
     */
    const defaultImage = computed(() => {
      if (!basicSetting.value || basicSetting.value.image === null) {
        return null;
      }

      return `${basicSetting.value.image}`;
    });

    /**
     * Final issuer image display
     */
    const logoDisplay = computed(() => {
      if (
        operationMode.value === FormOperation.EDIT ||
        operationMode.value === FormOperation.DUPLICATE
      ) {
        const issuerImage = formData.value.issuer_image;

        if (
          issuerImage &&
          !formData.value.logo &&
          (issuerImage.startsWith('https://') ||
            issuerImage.startsWith('http://'))
        ) {
          return issuerImage;
        }

        if (issuerImage && !formData.value.logo) {
          return `/hero-storage/public/forms/issuer/profile_image/${issuerImage}`;
        }

        if (!issuerImage && !formData.value.logo) {
          return null;
        }

        return formData.value.logo
          ? logoDisplayTemp.value
          : `/hero-storage/public/forms/issuer/profile_image/${issuerImage}`;
      }

      return formData.value.logo ? logoDisplayTemp.value : defaultImage.value;
    });

    const moneyFormat = (value) =>
      Math.round(value)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    /**
     * Handle submit create purchase order form
     *
     * @param {string} productId
     */
    const handleClickSubmit = (event) => {
      event.preventDefault();
      let apiCall = null;

      if (operationMode.value === FormOperation.DUPLICATE) {
        formData.value.mode = FormOperation.DUPLICATE;
      }

      const data = { ...formData.value };
      data.total_price = parseInt(overAllPrice, 10);
      setPageLoading(true);

      switch (operationMode.value) {
        case FormOperation.ADD:
        case FormOperation.DUPLICATE:
          apiCall = purchaseOrderApiService.confirmPurchaseOrder(data);
          break;
        case FormOperation.EDIT:
          apiCall = purchaseOrderApiService.updatePurchaseOrder(
            data,
            formData.value.id
          );
          break;
        default:
          break;
      }

      // Handle responses
      apiCall
        .then(() => {
          window.location.href = `/forms/purchase-orders`;
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Handle edit purchase order form
     *
     * @param {string} productId
     */
    const handleClickEdit = (event) => {
      event.preventDefault();
      formData.value.mode = 'edit';
      emit('switch-purchase-order-create-form', formData.value);
    };

    return {
      BpheroConfig,
      Common,
      DefaultImageCategory,
      FormQuotationProductTaxClassification,
      formData,
      totalValue,
      tax10Percent,
      tax8Percent,
      overAllPrice,
      handleClickSubmit,
      ProductTaxDistinction,
      handleClickEdit,
      basicSetting,
      setPageLoading,
      pageLoading,
      logoDisplay,
      moneyFormat,
      defaultImage,
      productPriceWithTaxes,
    };
  },
});
</script>
