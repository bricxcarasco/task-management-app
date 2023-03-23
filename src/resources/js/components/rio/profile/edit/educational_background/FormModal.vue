<template>
  <div
    class="modal fade"
    id="educational-form-modal"
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
              {{ modalTitle }}
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
              <input type="hidden" name="id" v-model="formData.id" />
              <div class="col-12">
                <div class="mb-3">
                  <label for="text-input" class="form-label">
                    {{ $t('labels.school_name') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control"
                    :class="errors?.school_name != null ? 'is-invalid' : ''"
                    type="text"
                    name="school_name"
                    v-model="formData.school_name"
                  />
                  <div
                    v-show="errors?.school_name"
                    v-for="(error, index) in errors?.school_name"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="mb-3">
                  <label for="select-input" class="form-label">
                    {{ $t('labels.graduation_date') }}
                  </label>
                  <input
                    class="form-control date-picker date-picker_ym rounded pe-5"
                    :class="errors?.graduation_date != null ? 'is-invalid' : ''"
                    type="text"
                    name="graduation_date"
                    v-model="formData.graduation_date"
                  />
                  <div
                    v-show="errors?.graduation_date"
                    v-for="(error, index) in errors?.graduation_date"
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
import RioProfileApiService from '../../../../../api/rio/profile';
import Common from '../../../../../common';
import i18n from '../../../../../i18n';

export default {
  name: 'EducationalBackgroundFormModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const rioProfileApiService = new RioProfileApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const modalTitle = ref(null);
    const formRef = ref({});
    const formData = ref({});

    /**
     * Reset validations handler
     */
    const resetValidations = () => {
      const gradDateElement = document.querySelector(
        'input.date-picker_ym[type=text]'
      );

      errors.value = null;
      gradDateElement.classList.remove('is-invalid');

      if (gradDateElement.nextSibling.classList.contains('invalid-feedback')) {
        gradDateElement.nextSibling.remove();
      }
    };

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formRef.value.querySelector('input[type=hidden]').value = '';
      formData.value = {};

      // Reset datepicker value
      const datepicker = document.querySelector('input.date-picker_ym');
      Common.clearFlatpickrYearMonthValue(datepicker);

      // Reset validation errors
      resetValidations();
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
        ? i18n.global.t('headers.educational_edit')
        : i18n.global.t('headers.educational_registration');
    };

    /**
     * Set default date if modal is for creating new data
     *
     * @returns {void}
     */
    const setDefaultDate = () => {
      if (!isEdit()) {
        /* eslint-disable no-underscore-dangle */
        const dateField = document.querySelector(
          'input[name=graduation_date]'
        )._flatpickr;
        dateField.setDate('2000-01', true);
      }
    };

    /**
     * Event listener for add educational background form submit
     *
     * @returns {void}
     */
    const submitForm = (event) => {
      event.preventDefault();

      // Reinitialize state
      modalLoading.value = true;
      resetValidations();
      emit('reset-alert');

      // Execute API call
      const apiCall = isEdit()
        ? rioProfileApiService.updateEducationalBackground(
            formData.value.id,
            formData.value
          )
        : rioProfileApiService.addEducationalBackground(formData.value);

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-backgrounds');
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;
          const gradDateElement = document.querySelector(
            'input.date-picker.date-picker_ym[type=text]'
          );

          // Inject validation errors
          if (responseData.status_code === 422) {
            const formErrors = responseData.data;
            errors.value = formErrors;

            if (formErrors.graduation_date) {
              const gradDateError = `<div class="invalid-feedback">
                ${formErrors.graduation_date}
              </div>`;

              gradDateElement.classList.add('is-invalid');
              gradDateElement.insertAdjacentHTML('afterEnd', gradDateError);
            }

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
        setDefaultDate();
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
      resetValidations,
      errors,
      modalLoading,
      modalRef,
      modalTitle,
    };
  },
};
</script>
