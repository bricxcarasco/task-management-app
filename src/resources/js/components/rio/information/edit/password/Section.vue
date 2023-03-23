<template>
  <div class="position-relative" ref="sectionRef">
    <form action="" class="row" @submit.prevent="submitForm" novalidate>
      <!-- Current password -->
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="reg-fn">
          {{ $t('labels.current_password') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.current_password"
          class="form-control"
          :class="errors?.current_password != null ? 'is-invalid' : ''"
          type="password"
        />
        <base-validation-error :errors="errors?.current_password" />
      </div>

      <!-- New password -->
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="reg-fn">
          {{ $t('labels.new_password') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.new_password"
          class="form-control"
          :class="errors?.new_password != null ? 'is-invalid' : ''"
          type="password"
        />
        <base-validation-error :errors="errors?.new_password" />
      </div>

      <!-- New password confirm -->
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="reg-fn">
          {{ $t('labels.new_password_confirmation') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.new_password_confirmation"
          class="form-control"
          :class="errors?.new_password_confirmation != null ? 'is-invalid' : ''"
          type="password"
        />
        <base-validation-error :errors="errors?.new_password_confirmation" />
      </div>

      <!-- Register Button -->
      <div class="col-sm-12 text-center">
        <base-button
          type="submit"
          class="mx-1"
          :title="this.$i18n.t('buttons.register')"
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
import Common from '../../../../../common';
import i18n from '../../../../../i18n';

export default {
  name: 'IndustrySection',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
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

    // Initialize form data
    const formData = ref({});

    /**
     * Event listener for update organization name/type form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      emit('reset-alert');
      emit('set-page-loading', true);
      errors.value = {};

      apiService
        .updatePassword(formData.value)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('alerts.password_has_changed')
          );

          // Clear form
          formData.value = {};
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = Common.constructValidationErrors(responseData.data);

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
     *
     * @returns {void}
     */
    const attachShowAccordionListener = () => {
      sectionRef.value
        .closest('.accordion-collapse')
        .addEventListener('show.bs.collapse', () => {
          // Clear form
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
