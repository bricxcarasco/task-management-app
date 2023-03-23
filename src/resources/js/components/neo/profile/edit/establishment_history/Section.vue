<template>
  <div class="position-relative" ref="sectionRef">
    <section-loader :show="listLoading" />

    <form action="" @submit.prevent="submitForm" novalidate>
      <div class="row">
        <!-- Business History -->
        <div class="col-sm-12 mb-3">
          <label class="form-label">
            {{ $t('labels.business_history') }}
            <sup class="text-danger ms-1">*</sup>
          </label>
          <span class="form-control-plaintext px-3">{{
            formData.business_duration
          }}</span>
        </div>

        <!-- Establishment Date -->
        <div class="col-sm-12 mb-4">
          <label class="form-label">
            {{ $t('labels.establishment_date') }}
            <sup class="text-danger ms-1">*</sup>
          </label>
          <input
            v-model="formData.establishment_date"
            class="form-control date-picker date-picker_ymd rounded pe-5"
            :class="errors?.establishment_date != null ? 'is-invalid' : ''"
            name="establishment_date"
            type="text"
            placeholder="yyyy-mm-dd"
            required
          />
          <base-validation-error :errors="errors?.establishment_date" />
        </div>
      </div>

      <!-- Educational background List -->
      <p>■ {{ $t('messages.neo.history_description') }}</p>
      <history-item
        v-for="history in histories"
        :key="history.id"
        :history="history"
      />

      <!-- Add History Button -->
      <div
        v-if="!isExceededLimit"
        :class="`text-${hasHistories ? 'end' : 'center'}`"
      >
        <base-button
          :title="this.$i18n.t('buttons.add_history')"
          :buttonType="'light'"
          :loading="loading"
          :icon="'ai-plus me-2'"
          data-bs-toggle="modal"
          data-bs-target="#history-form-modal"
        />
      </div>

      <div class="row mt-4">
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
      </div>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import BaseButton from '../../../../base/BaseButton.vue';
import BaseValidationError from '../../../../base/BaseValidationError.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import HistoryItem from './Item.vue';
import ApiService from '../../../../../api/neo/profile';

export default {
  name: 'EstablishmentHistorySection',
  props: {
    neo: {
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
    SectionLoader,
    HistoryItem,
  },
  setup(props, { emit }) {
    // Initialize variables
    const apiService = new ApiService();
    const histories = ref([]);
    const hasHistories = ref(false);
    const isExceededLimit = ref(false);
    const listLoading = ref(false);
    const errors = ref({});
    const sectionRef = ref(null);

    // Get specific initial data
    const { neo } = props;
    const neoData = {
      establishment_date: neo.establishment_date,
      business_duration: neo.business_duration,
    };

    // Initialize display and form data
    const displayData = ref(neoData);
    const formData = ref(neoData);

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
        .updateEstablishmentDate(props.neo.id, formData.value)
        .then((response) => {
          const responseData = response.data;
          const results = responseData.data;

          emit('set-alert', 'success');

          // Update business duration
          if (
            Object.prototype.hasOwnProperty.call(results, 'business_duration')
          ) {
            formData.value.business_duration = results.business_duration;
          }

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

          // Set datepicker date
          const dateField = sectionRef.value.querySelector(
            '[name=establishment_date]'
          );
          /* eslint-disable no-underscore-dangle */
          dateField._flatpickr.setDate(formData.value.establishment_date, true);
        });
    };

    /**
     * Get list of histories
     *
     * @returns {void}
     */
    const getHistories = async () => {
      listLoading.value = true;
      const getHistoryApi = await apiService.getHistories(props.neo.id);
      const getHistoryResponseData = getHistoryApi.data;
      histories.value = getHistoryResponseData.data || [];

      // Setup conditional parameters
      hasHistories.value = getHistoryResponseData.data.length > 0;
      isExceededLimit.value = getHistoryResponseData.data.length >= 10;
      listLoading.value = false;
    };

    // Fetch histories on created component
    getHistories();

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowAccordionListener();
    });

    return {
      histories,
      hasHistories,
      isExceededLimit,
      listLoading,
      formData,
      submitForm,
      errors,
      sectionRef,
      getHistories,
    };
  },
};
</script>
