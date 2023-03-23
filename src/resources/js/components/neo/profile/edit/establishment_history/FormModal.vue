<template>
  <div
    class="modal fade"
    id="history-form-modal"
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

              <!-- Year / Month -->
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">
                    {{ $t('labels.year_month') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control date-picker date-picker_ym rounded pe-5"
                    :class="errors?.additional != null ? 'is-invalid' : ''"
                    type="text"
                    name="additional"
                    v-model="formData.additional"
                  />
                  <base-validation-error :errors="errors?.additional" />
                </div>
              </div>

              <!-- Contents -->
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">
                    {{ $t('labels.content') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control"
                    :class="errors?.content != null ? 'is-invalid' : ''"
                    type="text"
                    name="content"
                    v-model="formData.content"
                  />
                  <base-validation-error :errors="errors?.content" />
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
import BaseValidationError from '../../../../base/BaseValidationError.vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import ApiService from '../../../../../api/neo/profile';
import Common from '../../../../../common';
import i18n from '../../../../../i18n';

export default {
  name: 'HistoryFormModal',
  props: {
    neo_id: {
      type: Number,
      required: true,
    },
  },
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const apiService = new ApiService();
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
      const dateElement = document.querySelector(
        'input.date-picker_ym[type=text]'
      );

      errors.value = null;
      dateElement.classList.remove('is-invalid');

      if (dateElement.nextSibling.classList.contains('invalid-feedback')) {
        dateElement.nextSibling.remove();
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
        ? i18n.global.t('headers.history_edit')
        : i18n.global.t('headers.history_registration');
    };

    /**
     * Event listener for add history background form submit
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
        ? apiService.updateHistory(formData.value.id, formData.value)
        : apiService.addHistory(props.neo_id, formData.value);

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-histories');
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;
          const dateElement = document.querySelector(
            'input.date-picker.date-picker_ym[type=text]'
          );

          // Inject validation errors
          if (responseData.status_code === 422) {
            const formErrors = responseData.data;
            errors.value = formErrors;

            if (formErrors.additional) {
              const dateError = `<div class="invalid-feedback">
                ${formErrors.additional}
              </div>`;

              dateElement.classList.add('is-invalid');
              dateElement.insertAdjacentHTML('afterEnd', dateError);
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
