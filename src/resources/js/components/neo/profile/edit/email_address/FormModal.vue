<template>
  <div
    class="modal fade"
    id="email-form-modal"
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
                  <!-- Email Input Field -->
                  <label class="form-label" for="registratin-email-field">
                    {{ $t('labels.email_address') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control"
                    :class="errors?.email_address != null ? 'is-invalid' : ''"
                    name="email_address"
                    type="text"
                    v-model="formData.email_address"
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
  name: 'FormEmailModal',
  props: {
    neo: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const neoProfileApiService = new NeoProfileApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const modalTitle = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const currentYear = new Date().getFullYear();
    const minimumYear = 1900;
    const editNeo = ref(props.neo);

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
        ? i18n.global.t('headers.email_edit')
        : i18n.global.t('headers.email_registration');
    };

    /**
     * Event listener for add email form submit
     *
     * @returns {void}
     */
    const submitForm = (event) => {
      event.preventDefault();

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');
      emit('set-page-loading', true);
      // Execute API call

      const apiCall = isEdit()
        ? neoProfileApiService.updateEmail(formData.value, formData.value.id)
        : neoProfileApiService.addEmail(formData.value, editNeo.value.id);

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-emails');

          resetModal();
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
      currentYear,
      minimumYear,
    };
  },
};
</script>
