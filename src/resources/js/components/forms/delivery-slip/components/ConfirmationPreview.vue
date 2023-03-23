<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <preview-modal
      :form_data="form_data"
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
        break-word
      "
    >
      <div class="row">
        <!-- Page loader -->
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
            <a
              href="#"
              class="btn btn-link"
              @click="
                () => $emit('switch-delivery-slip-create-form', form_data)
              "
            >
              <i class="ai-arrow-left"></i>
            </a>
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

          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.basic_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>
                    {{ $t('labels.delivery_number') }}
                  </th>
                  <td>
                    {{ form_data.form_no }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.subject2') }}
                  </th>
                  <td>
                    {{ form_data.title }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.suppliers') }}
                  </th>
                  <td>
                    {{ form_data.supplier_name }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.postal_code') }}
                  </th>
                  <td>
                    {{ form_data.zipcode }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.address') }}
                  </th>
                  <td>
                    {{ form_data.address }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.date_of_issue') }}
                  </th>
                  <td>
                    {{ form_data.issue_date }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.delivery_location') }}
                  </th>
                  <td>
                    {{ form_data.delivery_address }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.delivery_deadline') }}
                  </th>
                  <td>
                    {{ form_data.delivery_deadline }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.delivery_slip_date') }}
                  </th>
                  <td>
                    {{ form_data.delivery_date }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="form_data.products.length > 0">
            <p class="mb-0 bg-dark-gray p-2 c-white">
              {{ $t('labels.item') }}
            </p>
            <div class="p-4">
              <!-- Product List -->
              <ol>
                <li v-for="(product, index) in form_data.products" :key="index">
                  <table class="table bg-gray">
                    <tbody>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_product_name') }}
                        </th>
                        <td>
                          {{ product.name }}
                        </td>
                      </tr>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_quantity') }}
                        </th>
                        <td>
                          {{ product.quantity }}
                        </td>
                      </tr>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_unit') }}
                        </th>
                        <td>
                          {{ product.unit }}
                        </td>
                      </tr>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_unit_price') }}
                        </th>
                        <td>
                          {{ moneyFormat(product.unit_price)
                          }}{{ $t('labels.quotation_circle') }}
                        </td>
                      </tr>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_total_excluding_tax') }}
                        </th>
                        <td>
                          {{ moneyFormat(product.amount)
                          }}{{ $t('labels.quotation_circle') }}
                        </td>
                      </tr>
                      <tr>
                        <th class="w-md-25">
                          {{ $t('labels.quotation_tax_classification') }}
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
              </ol>

              <!-- Product Total and Taxes -->
              <div class="mt-4" v-if="productPriceWithTaxes">
                <table class="mx-auto table--border-0">
                  <tr>
                    <td>
                      {{ $t('labels.sub_total') }}
                    </td>
                    <td>
                      {{ moneyFormat(productPriceWithTaxes.subTotal)
                      }}{{ $t('labels.quotation_circle') }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ $t('labels.gst_ten_percent') }}
                    </td>
                    <td>
                      {{ moneyFormat(productPriceWithTaxes.totalWithGST)
                      }}{{ $t('labels.quotation_circle') }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ $t('labels.consumption_tax_reduction_eight_percent') }}
                    </td>
                    <td>
                      {{
                        moneyFormat(
                          productPriceWithTaxes.totalWitConsumptionTax
                        )
                      }}{{ $t('labels.quotation_circle') }}
                    </td>
                  </tr>
                </table>
              </div>
              <p class="fs-4 mt-3 text-center">
                <span>
                  {{ $t('labels.total') }}
                  ：
                </span>
                {{ moneyFormat(productPriceWithTaxes.total)
                }}{{ $t('labels.quotation_circle') }}
              </p>
            </div>
          </div>

          <!-- Remarks -->
          <div v-if="form_data.remarks">
            <p class="mb-0 bg-dark-gray p-2 c-white">
              {{ $t('labels.remarks') }}
            </p>
            <div class="p-4">
              <ul class="list-style-none">
                <li class="paragraph-content">{{ form_data.remarks }}</li>
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
                  <th>
                    {{ $t('labels.store_and_trade_name') }}
                  </th>
                  <td>
                    {{ form_data.issuer_name }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.department_name') }}
                  </th>
                  <td>
                    {{ form_data.issuer_department_name }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.address') }}
                  </th>
                  <td>
                    {{ form_data.issuer_address }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.tel') }}
                  </th>
                  <td>
                    {{ form_data.issuer_tel }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.fax') }}
                  </th>
                  <td>
                    {{ form_data.issuer_fax }}
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t('labels.business_number') }}
                  </th>
                  <td>
                    {{ form_data.issuer_business_number }}
                  </td>
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
              @click="
                () => $emit('switch-delivery-slip-create-form', form_data)
              "
            >
              {{ $t('buttons.fix') }}
            </button>
            <button
              type="button"
              class="btn btn-primary btnRight"
              @click="
                () => $emit('save-delivery-slip', productPriceWithTaxes.total)
              "
            >
              {{ $t('buttons.issue') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, defineComponent } from 'vue';
import FormQuotationProductTaxClassification from '../../../../enums/FormQuotationProductTaxClassification';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import BaseAlert from '../../../base/BaseAlert.vue';
import BpheroConfig from '../../../../config/bphero';
import Common from '../../../../common';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';
import FormOperation from '../../../../enums/FormOperationTypes';
import PreviewModal from '../../components/PreviewModal.vue';

export default defineComponent({
  name: 'DeliverySlipConfirmationPreview',
  props: {
    form_data: {
      type: [Array, Object],
      required: true,
    },
    form_basic_setting: {
      type: [Array, Object],
    },
    operation: {
      type: String,
    },
    is_duplicate: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    PreviewModal,
  },
  setup(props) {
    const formData = ref(props.form_data);
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const operationMode = ref(props.operation);
    const issuerImage = formData.value.issuer_image;

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

    const productPriceWithTaxes = computed(() => {
      const subTotal = props.form_data.products
        .map((product) => parseFloat(product.amount))
        .reduce((prev, curr) => prev + curr, 0);
      let totalWithGST = 0;
      let totalWitConsumptionTax = 0;

      props.form_data.products.forEach((product) => {
        switch (parseInt(product.tax_distinction, 3)) {
          case 1:
            totalWithGST += parseFloat(product.amount) * 0.1;
            break;
          case 2:
            totalWitConsumptionTax += parseFloat(product.amount) * 0.08;
            break;
          default:
            break;
        }
      });

      totalWithGST = Math.floor(parseFloat(totalWithGST));
      totalWitConsumptionTax = Math.floor(parseFloat(totalWitConsumptionTax));
      const total = subTotal + totalWithGST + totalWitConsumptionTax;

      return {
        subTotal,
        totalWithGST,
        totalWitConsumptionTax,
        total,
      };
    });

    /**
     * Computed property for logo display
     */
    const logoDisplayTemp = computed(
      () =>
        `/api/forms/delivery-slips/images/restore?code=${formData.value.logo}`
    );

    /**
     * Default image
     */
    const defaultImage = computed(() => {
      if (!props.form_basic_setting || !props.form_basic_setting?.image) {
        return null;
      }

      return `${props.form_basic_setting.image}`;
    });

    /**
     * Final issuer image display
     */
    const logoDisplay = computed(() => {
      if (operationMode.value === FormOperation.EDIT || props.is_duplicate) {
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

    return {
      BpheroConfig,
      Common,
      DefaultImageCategory,
      logoDisplay,
      defaultImage,
      FormQuotationProductTaxClassification,
      productPriceWithTaxes,
      moneyFormat,
      alert,
      pageLoading,
      setPageLoading,
      setAlert,
      resetAlert,
    };
  },
});
</script>

<style scoped>
.btnLeft {
  margin-right: 5px;
}

.btnRight {
  margin-left: 5px;
}
</style>
