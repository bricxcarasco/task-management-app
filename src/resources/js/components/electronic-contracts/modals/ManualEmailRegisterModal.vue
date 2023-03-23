<template>
  <div
    class="modal fade"
    id="electronicContractManualEmailRegister"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('headers.address_input') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            {{ $t('paragraphs.please_enter_the_recipient_name') }}
          </p>
          <div class="mb-3">
            <label for="text-input" class="form-label">
              {{ $t('labels.destination_name')
              }}<sup class="text-danger ms-1">*</sup>
            </label>
            <input
              class="form-control"
              :class="errors?.name != null ? 'is-invalid' : ''"
              type="text"
              id="text-input"
              v-model="recipientName"
            />
            <div
              v-show="errors?.name"
              v-for="(error, index) in errors?.name"
              :key="index"
              class="invalid-feedback"
            >
              {{ error }}
            </div>
          </div>
          <div class="mb-3">
            <label for="text-input" class="form-label">
              {{ $t('labels.email_address')
              }}<sup class="text-danger ms-1">*</sup>
            </label>
            <input
              class="form-control"
              :class="errors?.email_address != null ? 'is-invalid' : ''"
              type="text"
              id="text-input"
              v-model="recipientEmail"
            />
            <div
              v-show="errors?.email_address"
              v-for="(error, index) in errors?.email_address"
              :key="index"
              class="invalid-feedback"
            >
              {{ error }}
            </div>
          </div>
          <div class="text-center mt-4">
            <button
              type="button"
              class="btn btn-primary"
              @click="registerRecipient"
            >
              {{ $t('buttons.setting') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ElectronicContractService from '../../../api/electronic-contract/electronic_contract';

export default {
  name: 'ManualEmailRegister',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const electronicContractApiService = new ElectronicContractService();
    const modalRef = ref(null);
    const modalLoading = ref(false);
    const recipientName = ref('');
    const recipientEmail = ref('');

    const errors = ref({});

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show cancel conection modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide cancel conection modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
    };

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * Reset recipient form data
     *
     * @returns {void}
     */
    const resetFormData = () => {
      recipientName.value = '';
      recipientEmail.value = '';
    };

    /**
     * Trigger set administrator button and emit to parent component
     *
     * @returns {void}
     */
    const registerRecipient = async () => {
      setLoading(true);
      errors.value = null;

      emit('reset-alert');

      const data = {
        name: recipientName.value,
        email_address: recipientEmail.value,
      };

      await electronicContractApiService
        .manualRecipientRegister(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            emit('register-manual-recipient', data);
          }
          resetFormData();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          // Display alert message
          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
        resetFormData();
      });
    });

    return {
      modalRef,
      modal,
      modalLoading,
      setLoading,
      show,
      hide,
      errors,
      recipientName,
      recipientEmail,
      registerRecipient,
    };
  },
};
</script>
