<template>
  <div class="position-relative" ref="sectionRef">
    <form action="" class="row" @submit.prevent="submitForm" novalidate>
      <!-- Organization Name -->
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="organization-name">
          {{ $t('labels.organizational_name') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.organization_name"
          class="form-control"
          :class="errors?.organization_name != null ? 'is-invalid' : ''"
          type="text"
          id="organization-name"
          required
        />
        <base-validation-error :errors="errors?.organization_name" />
      </div>

      <!-- Organization Kana -->
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="organization-kana">
          {{ $t('labels.organizational_kana') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          v-model="formData.organization_kana"
          class="form-control"
          :class="errors?.organization_kana != null ? 'is-invalid' : ''"
          type="text"
          id="organization-kana"
          required
        />
        <base-validation-error :errors="errors?.organization_kana" />
      </div>

      <!-- Organization Type -->
      <div class="col-sm-12 mb-5">
        <label class="form-label" for="organization-type">
          {{ $t('labels.organizational_type') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <select
          v-model="formData.organization_type"
          id="organization-type"
          class="form-select"
          :class="errors?.organization_type != null ? 'is-invalid' : ''"
        >
          <option value="">{{ $t('labels.unselected') }}</option>
          <option
            v-for="(item, index) in organization_types"
            :key="index"
            :value="index"
          >
            {{ item }}
          </option>
        </select>
        <base-validation-error :errors="errors?.organization_type" />
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
import ApiService from '../../../../../api/neo/profile';

export default {
  name: 'IndustrySection',
  props: {
    neo: {
      type: [Array, Object],
      required: true,
    },
    organization_types: {
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

    // Get specific initial data
    const { neo } = props;

    // Initialize display data
    const displayData = ref({
      organization_name: neo.organization_name,
      organization_kana: neo.organization_kana,
      organization_type: neo.organization_type,
    });

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
        .updateOrganizationName(props.neo.id, formData.value)
        .then(() => {
          emit('set-alert', 'success');

          // Update display data with successful input
          displayData.value = formData.value;
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
          formData.value = { ...displayData.value };

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
