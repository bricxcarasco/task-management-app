<template>
  <div
    class="modal fade"
    id="industry-form-modal"
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
            <h4 class="modal-title">{{ modalTitle }}</h4>
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
              <input type="hidden" name="id" v-model="formData.id" />
              <div class="col-12">
                <div class="mb-3">
                  <!-- Industry Input Field -->
                  <label class="form-label" for="registratin-industry-field">
                    {{ $t('labels.industry') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <select
                    class="form-select"
                    :class="
                      errors?.business_category_id != null ? 'is-invalid' : ''
                    "
                    name="business_category_id"
                    v-model="formData.business_category_id"
                  >
                    <option value="">{{ $t('labels.unselected') }}</option>
                    <option
                      v-for="(item, index) in business_categories"
                      :key="index"
                      :value="index"
                    >
                      {{ item }}
                    </option>
                  </select>
                  <div
                    v-show="errors?.business_category_id"
                    v-for="(error, index) in errors?.business_category_id"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>
                <div class="mb-3">
                  <!-- Years of Experience Input Field -->
                  <label class="form-label" for="registratin-industry-field">
                    {{ $t('labels.years_of_experiences') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <select
                    class="form-select"
                    :class="errors?.additional != null ? 'is-invalid' : ''"
                    name="additional"
                    v-model="formData.additional"
                  >
                    <option value="">{{ $t('labels.unselected') }}</option>
                    <option
                      v-for="(item, index) in years_of_experiences"
                      :key="index"
                      :value="index"
                    >
                      {{ item }}
                    </option>
                  </select>
                  <div
                    v-show="errors?.additional"
                    v-for="(error, index) in errors?.additional"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.register') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import NeoProfileApiService from '../../../../../api/neo/profile';
import i18n from '../../../../../i18n';

export default {
  name: 'FormIndustryModal',
  props: {
    neo: {
      type: [Array, Object],
      required: true,
    },
    years_of_experiences: {
      type: [Array, Object],
      required: true,
    },
    business_categories: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const neoProfileApiService = new NeoProfileApiService();
    const neo = ref(props.neo);
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const modalTitle = ref(null);
    const formRef = ref({});
    const formData = ref({});

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /**
     * Check if modal is in edit state
     *
     * @returns {bool}
     */
    const isEdit = () => formData.value.id !== '';

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Update modal title depending on status
     *
     * @returns {void}
     */
    const updateModalTitle = () => {
      modalTitle.value = isEdit()
        ? i18n.global.t('headers.industry_edit')
        : i18n.global.t('headers.industry_registration');
    };

    /**
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = (event) => {
      event.preventDefault();

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Execute API call
      const apiCall = isEdit()
        ? neoProfileApiService.updateIndustry(formData.value.id, formData.value)
        : neoProfileApiService.addIndustry(neo.value.id, formData.value);

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-industries');

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
        updateModalTitle();
      });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
    });

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
      modalTitle,
    };
  },
};
</script>
