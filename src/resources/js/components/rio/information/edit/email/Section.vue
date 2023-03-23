<template>
  <div class="position-relative" ref="sectionRef">
    <form action="" class="row" @submit.prevent="submitForm" novalidate>
      <!-- Email Address -->
      <div class="col-sm-12 mb-3">
        <label class="form-label">
          {{ $t('labels.current_email_address') }}
        </label>
        <span class="form-control-plaintext px-3">{{ rio.email }}</span>
      </div>

      <!-- New Email Address -->
      <div class="col-sm-12 mb-4">
        <label class="form-label" for="organization-name">
          {{ $t('labels.new_email_address') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.email"
          class="form-control"
          :class="errors?.email != null ? 'is-invalid' : ''"
          type="text"
          id="organization-name"
          required
        />
        <base-validation-error :errors="errors?.email" />
      </div>

      <!-- Change Email Button -->
      <div class="col-sm-12 text-center">
        <base-button
          type="submit"
          class="mx-1"
          :title="this.$i18n.t('buttons.change_email_address')"
          :buttonType="'success'"
          :loading="loading"
        />
      </div>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import BaseButton from '../../../../base/BaseButton.vue';
import BaseValidationError from '../../../../base/BaseValidationError.vue';
import ApiService from '../../../../../api/rio/information';
import i18n from '../../../../../i18n';

export default {
  name: 'EmailSection',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    password_confirm: {
      type: [Array, Object, null],
      required: true,
    },
  },
  components: {
    BaseButton,
    BaseValidationError,
  },
  setup(props, { emit }) {
    // Initialize variables
    const apiService = new ApiService();
    const errors = ref({});
    const sectionRef = ref(null);
    const formData = ref({});

    /**
     * Checks if password validation remains in errors
     *
     * @param {Object}
     * @returns {bool}
     */
    const isRequiresPasswordConfirm = (validationError) => {
      const fields = Object.keys(validationError);

      return fields.length === 1 && fields[0] === 'password';
    };

    /**
     * Event listener for confirming password
     *
     * @param {string}
     * @returns {void}
     */
    const onConfirmPassword = (password) => {
      formData.value.password = password;

      // Call update email API with confirmed password
      apiService
        .updateEmailAddress(formData.value)
        .then(() => {
          props.password_confirm.modal.hide();
          formData.value = {};
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.rio.reset_email_notification')
          );
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Display password confirm validation errors
          if (responseData.status_code === 422) {
            props.password_confirm.displayValidation(responseData.data);

            return;
          }

          // Display alert message
          props.password_confirm.modal.hide();
          delete formData.value.password;
          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          props.password_confirm.setLoading(false);
        });
    };

    /**
     * Event listener for update email address form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      emit('reset-alert');
      emit('set-page-loading', true);
      errors.value = {};

      apiService
        .updateEmailAddress(formData.value)
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;

            // Checks for password confirmation
            if (isRequiresPasswordConfirm(responseData.data)) {
              props.password_confirm.confirm(onConfirmPassword);
            }

            return;
          }

          // Display alert message
          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          emit('set-page-loading', false);
        });
    };

    /**
     * Attach event listener for shown accordion section
     */
    const attachShowAccordionListener = () => {
      sectionRef.value
        .closest('.accordion-collapse')
        .addEventListener('show.bs.collapse', () => {
          // Place display data to form
          formData.value = {};

          // Reset validation errors
          errors.value = {};
        });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowAccordionListener();
    });

    return {
      formData,
      submitForm,
      errors,
      sectionRef,
    };
  },
};
</script>
