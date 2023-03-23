<template>
  <div
    class="modal fade"
    id="account-form-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="handleFormSubmission" ref="formRef" novalidate>
          <input type="hidden" id="operation" value="add" />
          <input
            type="hidden"
            name="account_index"
            v-model="formData.account_index"
          />

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
              <div class="col-12">
                <div class="mb-3">
                  <label for="text-input" class="form-label">
                    {{ $t('labels.bank_name') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control"
                    :class="errors?.bank != null ? 'is-invalid' : ''"
                    type="text"
                    name="bank"
                    v-model="formData.bank"
                  />
                  <base-validation-error :errors="errors?.bank" />
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label for="text-input" class="form-label">
                      {{ $t('labels.branch') }}
                      <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input
                      class="form-control"
                      :class="errors?.branch != null ? 'is-invalid' : ''"
                      type="text"
                      name="branch"
                      v-model="formData.branch"
                    />
                    <base-validation-error :errors="errors?.branch" />
                  </div>
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label for="text-input" class="form-label">
                      {{ $t('labels.account_type') }}
                      <sup class="text-danger ms-1">*</sup>
                    </label>
                  </div>
                  <div
                    class="form-check form-check-inline mb-3"
                    :class="errors?.account_type != null ? 'is-invalid' : ''"
                  >
                    <input
                      v-model="formData.account_type"
                      :value="AccountTypesConstant.SAVINGS"
                      class="form-check-input"
                      type="radio"
                      id="savings"
                      name="account_type"
                    />
                    <label class="form-check-label" for="savings">{{
                      $t('labels.savings')
                    }}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      v-model="formData.account_type"
                      :value="AccountTypesConstant.CURRENT"
                      class="form-check-input"
                      type="radio"
                      id="current"
                      name="account_type"
                    />
                    <label class="form-check-label" for="current">{{
                      $t('labels.current')
                    }}</label>
                  </div>
                  <base-validation-error :errors="errors?.account_type" />
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label for="text-input" class="form-label">
                      {{ $t('labels.account_number') }}
                      <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input
                      class="form-control"
                      :class="
                        errors?.account_number != null ? 'is-invalid' : ''
                      "
                      type="text"
                      name="account_number"
                      v-model="formData.account_number"
                    />
                    <base-validation-error :errors="errors?.account_number" />
                    <p class="text-muted fs-xs">
                      {{ $t('labels.required_7_digits') }}
                    </p>
                  </div>
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label for="text-input" class="form-label">
                      {{ $t('labels.account_name') }}
                      <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input
                      class="form-control"
                      :class="errors?.account_name != null ? 'is-invalid' : ''"
                      type="text"
                      name="account_name"
                      v-model="formData.account_name"
                    />
                    <base-validation-error :errors="errors?.account_name" />
                  </div>
                </div>
              </div>
            </div>
            <div class="text-center">
              <button class="btn btn-success btn-shadow btn-sm" type="submit">
                {{ $t('buttons.register') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import AccountTypesConstant from '../../../enums/AccountTypesConstant';
import { objectifyForm } from '../../../utils/objectifyForm';
import AccountTransferOperation from '../../../enums/AccountTransferOperation';
import ClassifiedSettingsApiService from '../../../api/classifieds/settings';
import i18n from '../../../i18n';
import BaseValidationError from '../../base/BaseValidationError.vue';

export default {
  name: 'AccountFormModal',
  components: {
    SectionLoader,
    BaseValidationError,
  },
  setup(props, { emit }) {
    const classifiedSettingsApiService = new ClassifiedSettingsApiService();
    const formData = ref({});
    const modalLoading = ref(false);
    const errors = ref(null);
    const modalRef = ref(null);
    const operation = ref('add');
    const formRef = ref({});
    const settingIndex = ref(0);
    const modalTitle = ref(null);

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
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Update modal title depending on operation
     *
     * @returns {void}
     */
    const updateModalTitle = () => {
      switch (operation.value) {
        case AccountTransferOperation.ADD:
          modalTitle.value = i18n.global.t('labels.register_bank_account');
          break;
        case AccountTransferOperation.EDIT:
          modalTitle.value = i18n.global.t('labels.edit_bank_account');
          break;
        default:
          break;
      }
    };

    /**
     * Handle register or edit account details
     *
     * @returns {void}
     */
    const handleFormSubmission = (event) => {
      event.preventDefault();
      let apiCall = null;
      modalLoading.value = true;
      errors.value = null;
      const data = { ...formData.value };
      const parseIndex = parseInt(formData.value.account_index, 10);
      switch (operation.value) {
        case AccountTransferOperation.EDIT:
          apiCall = classifiedSettingsApiService.editSetting(data);
          break;
        case AccountTransferOperation.ADD:
          apiCall = classifiedSettingsApiService.registerSetting(data);
          break;
        default:
          break;
      }

      apiCall
        .then((response) => {
          const responseData = response.data.data;
          if (operation.value === AccountTransferOperation.ADD) {
            emit('add-account', responseData);
          } else {
            emit('edit-account', responseData, parseIndex);
          }
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            const formErrors = responseData.data;
            errors.value = formErrors;
          }
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Set modal operation
     */
    const setOperation = () => {
      const operationNode = document.querySelector(
        "form input#operation[type='hidden']"
      );
      operation.value = operationNode.value;
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        setOperation();
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
      modalLoading,
      errors,
      AccountTypesConstant,
      resetModal,
      handleFormSubmission,
      modalRef,
      operation,
      formRef,
      settingIndex,
      updateModalTitle,
      modalTitle,
    };
  },
};
</script>
