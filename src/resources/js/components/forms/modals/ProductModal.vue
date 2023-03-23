<template>
  <div
    class="modal fade"
    id="quotation-product-form-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t('headers.item_input') }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="mb-3">
                <input
                  type="hidden"
                  :value="Math.random().toString(36).slice(2)"
                  name="product_id"
                />
                <label class="form-label">
                  ■{{ $t('labels.quotation_product_name') }}
                  <span class="text-danger">*</span>
                </label>
                <input
                  class="form-control"
                  type="text"
                  :class="errors?.name != null ? 'is-invalid' : ''"
                  v-model="formData.name"
                  name="name"
                />
                <base-validation-error :errors="errors?.name" />
              </div>
              <div class="mb-3">
                <label class="form-label">
                  ■{{ $t('labels.quotation_quantity') }}
                </label>
                <input
                  class="form-control js-quantity"
                  type="text"
                  :class="errors?.quantity != null ? 'is-invalid' : ''"
                  v-model="formData.quantity"
                  @change="getAmount()"
                  name="quantity"
                  v-numbers-only
                />
                <base-validation-error :errors="errors?.quantity" />
              </div>
              <div class="mb-3">
                <label class="form-label">
                  ■{{ $t('labels.quotation_unit') }}
                </label>
                <input
                  class="form-control"
                  type="text"
                  :class="errors?.unit != null ? 'is-invalid' : ''"
                  v-model="formData.unit"
                  name="unit"
                />
                <base-validation-error :errors="errors?.unit" />
              </div>
              <div class="mb-3">
                <label class="form-label">
                  ■{{ $t('labels.quotation_unit_price') }}
                </label>
                <div class="d-block">
                  <div class="d-flex align-items-center justify-content-around">
                    <input
                      class="form-control me-2 js-unit-price"
                      type="text"
                      :class="errors?.unit_price != null ? 'is-invalid' : ''"
                      v-model="formData.unit_price"
                      @change="getAmount()"
                      name="unit_price"
                      v-numbers-only
                    />
                    <span>
                      {{ $t('labels.quotation_circle') }}
                    </span>
                  </div>
                  <base-validation-error
                    :errors="errors?.unit_price"
                    class="d-block"
                  />
                </div>
              </div>
              <div class="mb-0">
                <label for="select-input" class="form-label">
                  ■{{ $t('labels.quotation_tax_classification') }}
                </label>
                <select
                  class="form-select"
                  id="select-input"
                  :class="errors?.tax_distinction != null ? 'is-invalid' : ''"
                  v-model="formData.tax_distinction"
                  name="tax_distinction"
                >
                  <option
                    v-for="(
                      tax_distinction, index
                    ) in FormQuotationProductTaxClassificationEnums"
                    :key="tax_distinction"
                    :value="index"
                  >
                    {{ tax_distinction }}
                  </option>
                </select>
                <base-validation-error :errors="errors?.tax_distinction" />
              </div>
              <input type="hidden" v-model="formData.amount" name="amount" />
              <input type="hidden" id="selected-product-id" />
              <p class="mt-5 mb-0">
                {{ $t('labels.quotation_total_excluding_tax') }}
              </p>
              <p v-if="formData.amount === 0" class="fs-4">________</p>
              <p v-else class="fs-4">
                {{
                  parseFloat(formData.amount)
                    .toFixed()
                    .replace(/\d(?=(\d{3})+$)/g, '$&,')
                }}{{ $t('labels.quotation_circle') }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">
              {{ $t('buttons.decision') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import FormsApiService from '../../../api/forms/forms';
import FormQuotationProductTaxClassificationEnums from '../../../enums/FormQuotationProductTaxClassification';
import ProductTaxDistinction from '../../../enums/ProductTaxDistinction';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'ProductModal',
  props: {},
  components: {
    SectionLoader,
    BaseValidationError,
  },
  setup(props, { emit }) {
    const formsApiService = new FormsApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const invalidAmount = ref(false);
    const regex = /^[0-9]*$/;

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {
        tax_distinction: ProductTaxDistinction.PERCENT_10,
        quantity: '',
        unit_price: '',
        amount: 0,
      };
      errors.value = null;
    };

    /**
     * Get amount
     *
     * @returns {amount}
     */
    const getAmount = () => {
      formData.value.amount = 0;

      if (!regex.test(formData.value.quantity) && formData.value.quantity) {
        formData.value.quantity = formData.value.quantity.replace(/\D/g, '');
      }

      if (!regex.test(formData.value.unit_price) && formData.value.unit_price) {
        formData.value.unit_price = formData.value.unit_price.replace(
          /\D/g,
          ''
        );
      }

      if (formData.value.quantity && formData.value.unit_price) {
        const price =
          parseInt(formData.value.quantity, 10) *
          parseInt(formData.value.unit_price, 10);
        if (!price.isNaN) {
          formData.value.amount = price;
        } else {
          formData.value.amount = 0;
        }
      }
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      invalidAmount.value = false;
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Format number using parseInt
     *
     * @returns {void}
     */
    const parseNumber = (number) => {
      if (number) {
        return parseInt(number, 10);
      }
      return number;
    };

    /**
     * Event listener for validate product form submit
     *
     * @returns {void}
     */
    const submitForm = async (event) => {
      event.preventDefault();

      updateModel();

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      formData.value.unit_price = parseNumber(formData.value.unit_price, 10);
      formData.value.quantity = parseNumber(formData.value.quantity, 10);

      // Handle responses
      formsApiService
        .validateQuotationProduct(formData.value)
        .then((response) => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('alerts.item_has_been_added')
          );
          emit('added-product', response.data);
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        updateModel();
        getAmount();
      });
    };

    /**
     * Attach default selected Tax Distinction
     */
    const attachDefaultTax = () => {
      formData.value.tax_distinction = ProductTaxDistinction.PERCENT_10;
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
      attachDefaultTax();
    });

    return {
      FormQuotationProductTaxClassificationEnums,
      formData,
      formRef,
      invalidAmount,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
      getAmount,
    };
  },
});
</script>
