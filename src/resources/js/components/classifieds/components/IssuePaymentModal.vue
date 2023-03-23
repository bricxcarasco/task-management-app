<template>
  <div
    class="modal fade"
    id="issue-payment"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.issue_payment_screen') }}</h4>
          <button
            class="btn-close js-payment-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="resetModal"
          ></button>
        </div>
        <div class="modal-body">
          <div
            class="
              d-flex
              border
              align-items-center
              justify-content-between
              rounded-3
              p-2
            "
          >
            <img
              class="img-rounded me-4"
              :src="product.main_photo"
              alt="Image"
              width="60"
            />
            <p class="mb-2 flex-1">{{ product.title }}</p>
          </div>

          <!-- Form -->
          <form
            @submit="handleFormSubmission"
            action=""
            ref="formRef"
            novalidate
          >
            <div class="mt-2">
              <label for="text-input" class="form-label">
                ■{{ $t('labels.transaction_amount') }}
              </label>
              <div class="d-flex align-items-center justify-content-between">
                <input
                  class="form-control flex-1 me-2 classsified-create-price"
                  :class="errors?.price != null ? 'is-invalid' : ''"
                  type="number"
                  v-model="formData.price"
                />
                <span>{{ $t('labels.yen') }}</span>
              </div>
              <validation-error class="d-block" :errors="errors?.price" />
            </div>
            <div class="mt-2">
              <label for="select-input" class="form-label">
                ■{{ $t('labels.payment_method') }}
              </label>
              <select
                class="form-select"
                :class="errors?.payment_method != null ? 'is-invalid' : ''"
                name="payment_method"
                v-model="formData.payment_method"
              >
                <option
                  v-for="(item, index) in settings"
                  :key="index"
                  :value="index"
                >
                  {{ item }}
                </option>
              </select>
              <validation-error :errors="errors?.payment_method" />
            </div>
            <div
              v-if="isBankTransfer && bankAccounts.length !== 0"
              class="mt-2"
            >
              <p class="ps-3 form-label">
                ■{{ $t('labels.transfer_account_info') }}
              </p>
              <div
                v-for="(account, index) in bankAccounts"
                :key="index"
                class="border p-2"
              >
                {{ account.bank }} {{ account.branch }} ({{
                  AccountTypesEnum[account.account_type]
                }})
                <br />
                {{ account.account_number }} <br />
                カ）{{ account.account_name }}
              </div>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.issue_payment_info') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ValidationError from '../../base/BaseValidationError.vue';
import ContactPaymentsApiService from '../../../api/classifieds/payments';
import PaymentMethodsEnum from '../../../enums/PaymentMethods';
import AccountTypesEnum from '../../../enums/AccountTypes';

export default defineComponent({
  name: 'IssuePaymentModalComponents',
  props: {
    contact: {
      type: [Array, Object],
      required: true,
    },
    product: {
      type: [Array, Object],
      required: true,
    },
    settings: {
      type: [Array, Object],
      required: true,
    },
    bank_accounts: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
    ValidationError,
  },
  setup(props) {
    const contactPaymentsApiService = new ContactPaymentsApiService();
    const contact = ref(props.contact);
    const settings = ref(props.settings);
    const bankAccounts = ref(props.bank_accounts);
    const isBankTransfer = ref(false);
    const modalLoading = ref(false);
    const errors = ref(null);
    const formRef = ref({});
    const formData = ref({
      price: null,
      payment_method: null,
    });

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const defaultPayment = Object.keys(settings.value)[0];

      errors.value = null;
      formData.value = {
        price: null,
        payment_method: defaultPayment,
      };
    };

    /**
     * Event listener for add url form submit
     *
     * @returns {void}
     */
    const handleFormSubmission = (event) => {
      event.preventDefault();

      // Setup data
      const data = { ...formData.value };
      data.classified_sale_id = contact.value.classified_sale_id;
      data.classified_contact_id = contact.value.id;
      data.rio_id = contact.value.rio_id;
      data.neo_id = contact.value.neo_id;

      // Start modal loading
      modalLoading.value = true;

      contactPaymentsApiService
        .issueNewPayment(data)
        .then((response) => {
          const url = response.data.data;

          /* eslint no-undef: 0 */
          const modalCloseBtn = document.querySelector('.js-payment-close');
          const issuedPaymentNode =
            document.getElementById('issued-payment-url');
          const issuedPayment = computed(
            () => new bootstrap.Modal(issuedPaymentNode)
          );

          const linkSelector =
            issuedPaymentNode.querySelector('.js-payment-url');
          linkSelector.innerHTML = url;
          linkSelector.href = url;

          // Toggle modals
          modalCloseBtn.click();
          issuedPayment.value.show();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Watch changes for payment method
     */
    watch(
      () => formData.value.payment_method,
      () => {
        isBankTransfer.value =
          formData.value.payment_method === PaymentMethodsEnum.TRANSFER;
      }
    );

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      // Autoset first selectable payment method
      const [paymentMethod] = Object.keys(settings.value);
      formData.value.payment_method = paymentMethod;
    });

    return {
      formData,
      formRef,
      errors,
      modalLoading,
      isBankTransfer,
      bankAccounts,
      handleFormSubmission,
      AccountTypesEnum,
      resetModal,
    };
  },
});
</script>
