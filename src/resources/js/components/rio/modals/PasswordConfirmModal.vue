<template>
  <div
    class="modal fade"
    id="password-confirm-modal"
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
              {{ $t('headers.password_confirm') }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <!-- Password field -->
                <label class="form-label">
                  {{ $t('messages.password_confirmation_description') }}
                  <sup class="text-danger ms-1">*</sup>
                </label>
                <input
                  class="form-control"
                  :class="errors?.password != null ? 'is-invalid' : ''"
                  type="password"
                  name="password"
                  v-model="formData.password"
                />
                <base-validation-error :errors="errors?.password" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.change') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'PasswordConfirmModal',
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));
    const onSubmit = ref(null);

    /**
     * Start password confirmation
     *
     * @returns {void}
     */
    const confirm = (callback) => {
      modal.value.show();
      onSubmit.value = callback;
    };

    /**
     * Display validation messages
     *
     * @returns {void}
     */
    const displayValidation = (validationErrors) => {
      errors.value = validationErrors;
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
     * Event listener confirm password on form submission
     *
     * @returns {void}
     */
    const submitForm = async (event) => {
      event.preventDefault();

      // Reinitialize state
      setLoading(true);
      errors.value = null;
      emit('reset-alert');

      onSubmit.value(formData.value.password);
    };

    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        modalRef.value.querySelector('form').reset();
        setLoading(false);
        formData.value = {};
        errors.value = null;
      });
    });

    return {
      formData,
      formRef,
      submitForm,
      errors,
      modalLoading,
      modalRef,
      modal,
      confirm,
      displayValidation,
      setLoading,
    };
  },
};
</script>

<style></style>
