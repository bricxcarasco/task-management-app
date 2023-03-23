<template>
  <div>
    <!-- Preview modal -->
    <preview-modal
      :form_data="form"
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

          <!--{{-- Form information --}} -->
          <p class="mb-0 bg-dark-gray p-2 c-white">
            {{ $t('labels.basic_information') }}
          </p>
          <div class="p-4">
            <table class="table table-striped table--quotation">
              <tbody>
                <tr class="bg-blue">
                  <th>{{ $t('labels.invoice_no') }}</th>
                  <td>{{ formData.invoice_no }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.invoice_subject') }}</th>
                  <td>{{ formData.subject }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.suppliers') }}</th>
                  <td>{{ formData.supplier_name }}</td>
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
                  <th>{{ $t('labels.payment_date') }}</th>
                  <td>{{ formData.payment_date }}</td>
                </tr>
                <tr>
                  <th>{{ $t('labels.payment_terms') }}</th>
                  <td>{{ formData.payment_terms }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!--  {{-- Form products section --}} -->
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
                            {{ product.unit_price
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

          <!-- Payment Notes -->
          <div v-if="form.payment_notes" class="hidden-element">
            <p class="mb-0 bg-dark-gray p-2 c-white">
              {{ $t('labels.payment_notes') }}
            </p>
            <div class="p-4">
              <ul class="list-style-none">
                <li class="paragraph-content">{{ form.payment_notes }}</li>
              </ul>
            </div>
          </div>

          <!-- Payee Information -->
          <div v-if="form.payee_information">
            <p class="mb-0 bg-dark-gray p-2 c-white">
              {{ $t('labels.payee_information') }}
            </p>
            <div class="p-4 wrap">
              <ul class="list-style-none">
                <li class="paragraph-content">{{ form.payee_information }}</li>
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
import Common from '../../../../common';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';
import InvoicesApiService from '../../../../api/forms/invoices';
import FormQuotationProductTaxClassification from '../../../../enums/FormQuotationProductTaxClassification';
import ProductTaxDistinction from '../../../../enums/ProductTaxDistinction';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import PreviewModal from '../../components/PreviewModal.vue';
import BpheroConfig from '../../../../config/bphero';
import FormOperation from '../../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'InvoiceConfirmationPreview',
  props: {
    form: {
      type: [Array, Object],
    },
    form_basic_setting: {
      type: [Array, Object],
    },
    is_duplicate: {
      type: Boolean,
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
    PreviewModal,
  },
  setup(props, { emit }) {
    const formData = ref(props.form);
    const basicSetting = ref(props.form_basic_setting);
    const invoicesApiService = new InvoicesApiService();
    const pageLoading = ref(false);
    const isDuplicate = ref(props.is_duplicate);

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
     * Computed property for logo display
     */
    const logoDisplayTemp = computed(
      () => `/api/forms/file/restore?code=${formData.value.logo}`
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
      if (props.is_edit || props.is_duplicate) {
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
     * Handle submit create invoice form
     *
     * @param {string} productId
     */
    const handleClickSubmit = async (event) => {
      event.preventDefault();

      if (isDuplicate.value) {
        formData.value.mode = FormOperation.DUPLICATE;
      }

      const data = { ...formData.value };
      data.total_price = parseInt(overAllPrice, 10);
      setPageLoading(true);

      if (props.is_edit) {
        emit('save-invoice', data);
        return;
      }

      // Handle responses
      await invoicesApiService
        .confirmInvoice(data)
        .then(() => {
          window.location.href = `/forms/invoices`;
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Handle edit invoice form
     *
     * @param {string} productId
     */
    const handleClickEdit = (event) => {
      event.preventDefault();
      formData.value.mode = 'edit';
      emit('switch-invoice-create-form', formData.value);
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
      isDuplicate,
    };
  },
});
</script>
