<template>
  <div
    class="modal fade"
    id="purchase-search-modal"
    tabindex="-1"
    role="dialog"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">{{ $t('labels.form_search') }}</h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="text-input" class="form-label">{{
                $t('labels.free_word_search_for_purchase_orders')
              }}</label>
              <div class="position-relative">
                <i class="ai-search ai--absolute"></i>
                <input
                  class="form-control"
                  type="text"
                  id="text-input"
                  v-model="formData.free_word"
                  placeholder="キーワードを入力してください"
                  style="padding-left: 30px"
                />
              </div>
            </div>
            <div class="mb-3">
              <label>{{ $t('labels.specify_issue_date') }}</label>
              <div class="d-flex align-items-center justify-content-around">
                <div>
                  <input
                    v-model="formData.issue_start_date"
                    class="form-control datepicker_ymd_start rounded"
                    type="text"
                    data-linked-input="#end-date"
                  />
                </div>
                <span class="mx-4">~</span>
                <div>
                  <input
                    v-model="formData.issue_end_date"
                    class="form-control datepicker_ymd_end rounded"
                    :class="errors?.issue_end_date != null ? 'is-invalid' : ''"
                    type="text"
                  />
                  <base-validation-error :errors="errors?.issue_end_date" />
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label>{{ $t('labels.specify_delivery_date') }}</label>
              <div class="d-flex align-items-center justify-content-around">
                <div>
                  <input
                    v-model="formData.delivery_start_date"
                    class="form-control datepicker2_ymd_start rounded"
                    type="text"
                    data-linked-input="#start-date"
                  />
                </div>
                <span class="mx-4">~</span>
                <div>
                  <input
                    v-model="formData.delivery_end_date"
                    class="form-control datepicker2_ymd_end rounded"
                    :class="
                      errors?.delivery_end_date != null ? 'is-invalid' : ''
                    "
                    type="text"
                  />
                  <base-validation-error :errors="errors?.delivery_end_date" />
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label>{{ $t('labels.amount_of_money') }} </label>
              <div class="d-flex align-items-center justify-content-around">
                <div>
                  <input
                    v-model="formData.amount_min"
                    class="form-control"
                    type="text"
                    :class="errors?.amount_min != null ? 'is-invalid' : ''"
                    v-numbers-only
                  />
                  <base-validation-error :errors="errors?.amount_min" />
                </div>
                <span class="mx-4">~</span>
                <div>
                  <input
                    v-model="formData.amount_max"
                    class="form-control"
                    :class="errors?.amount_max != null ? 'is-invalid' : ''"
                    type="text"
                    v-numbers-only
                  />
                  <base-validation-error :errors="errors?.amount_max" />
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button
                type="button"
                class="btn btn-primary"
                @click="handleSearchPurchaseOrder"
              >
                {{ $t('buttons.search') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import PurchaseOrderApiService from '../../../api/forms/purchase-orders';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import BaseValidationError from '../../base/BaseValidationError.vue';

export default defineComponent({
  name: 'PurchaseOrderSearchModal',
  components: {
    SectionLoader,
    BaseValidationError,
  },
  setup(props, { emit }) {
    const purchaseOrderApiService = new PurchaseOrderApiService();
    const formData = ref({});
    const modalLoading = ref(false);
    const errors = ref(null);
    const regex = /^[0-9]*$/;

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#purchase-search-modal');
      modal.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
      modal.querySelector('form').reset();
    };

    /**
     * Search quotation
     *
     * @returns {void}
     */
    const handleSearchPurchaseOrder = async () => {
      modalLoading.value = true;
      const data = { ...formData.value };
      data.is_search = true;

      // Format number field values
      if (!regex.test(data.amount_max) && data.amount_max) {
        data.amount_max = data.amount_max.replace(/\D/g, '');
      }
      if (!regex.test(data.amount_min) && data.amount_min) {
        data.amount_min = data.amount_min.replace(/\D/g, '');
      }

      if (Object.values(formData.value).every((value) => value === '')) {
        data.is_search = false;
      }

      errors.value = null;

      await purchaseOrderApiService
        .validateSearchInputs(data)
        .then((response) => {
          resetModal();
          emit('search-purchase-order', response.data.data);
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      formData,
      handleSearchPurchaseOrder,
      resetModal,
      modalLoading,
      errors,
    };
  },
});
</script>
