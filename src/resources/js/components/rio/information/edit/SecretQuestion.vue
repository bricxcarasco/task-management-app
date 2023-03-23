<template>
  <div ref="sectionRef">
    <form action="" class="row" @submit.prevent="submitForm" novalidate>
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="reg-email">
          {{ $t('labels.secret_question') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <select
          v-model="editUser.secret_question"
          class="form-select"
          :class="errors?.secret_question != null ? 'is-invalid' : ''"
          id="select-input"
        >
          <option
            v-for="secretQuestion in secretQuestions"
            :key="secretQuestion.key"
            :value="secretQuestion.key"
            :selected="secretQuestion.key == editUser.secret_question"
          >
            {{ secretQuestion.value }}
          </option>
        </select>
        <base-validation-error :errors="errors?.secret_question" />
      </div>
      <div class="col-sm-12 mb-3">
        <label class="form-label" for="reg-fn">
          {{ $t('labels.secret_answer') }}
          <sup class="text-danger ms-1">*</sup>
        </label>
        <input
          class="form-control"
          :class="errors?.secret_answer != null ? 'is-invalid' : ''"
          type="text"
          v-model="editUser.secret_answer"
          id="reg-fn"
        />
        <base-validation-error :errors="errors?.secret_answer" />
      </div>
      <div class="d-flex justify-content-center align-items-center mt-3">
        <base-button
          type="submit"
          class="mx-1"
          :title="this.$i18n.t('buttons.change_secret_question')"
          :buttonType="'success'"
          :loading="loadingButton"
        />
      </div>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import BaseButton from '../../../base/BaseButton.vue';
import BaseValidationError from '../../../base/BaseValidationError.vue';
import ApiService from '../../../../api/rio/account_information';

export default {
  name: 'SecretQuestion',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    secretQuestions: {
      type: [Array, Object],
      required: true,
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
    const apiService = new ApiService();
    const editUser = ref(props.user);
    const initialUser = ref(props.user);
    const errors = ref({});
    const loadingButton = ref(false);
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

    const resetState = () => {
      // Place display data to form
      formData.value = {};

      // Reset to its original values
      editUser.value = { ...initialUser.value };

      // Reset validation errors
      errors.value = {};
    };

    /**
     * Event listener for confirming password
     *
     * @param {string}
     * @returns {void}
     */
    const onConfirmPassword = (password) => {
      const secretQuestionDataWithPassword = {
        secret_question: editUser.value.secret_question,
        secret_answer: editUser.value.secret_answer,
        password,
      };

      // Call update email API with confirmed password
      apiService
        .updateSecretQuestion(secretQuestionDataWithPassword)
        .then(() => {
          props.password_confirm.modal.hide();
          initialUser.value = { ...editUser.value };
          emit('set-alert', 'success');
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Display password confirm validation errors
          if (responseData.status_code === 422) {
            props.password_confirm.displayValidation(responseData.data);
          }
        })
        .finally(() => props.password_confirm.setLoading(false));
    };

    /**
     * Event listener for update email address form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      emit('reset-alert');
      emit('set-page-loading', true);
      loadingButton.value = true;
      errors.value = {};

      const secretQuestionData = {
        secret_question: editUser.value.secret_question,
        secret_answer: editUser.value.secret_answer,
      };

      apiService
        .updateSecretQuestion(secretQuestionData)
        .catch((error) => {
          // console.log(error.response.data);
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

          // // Display alert message
          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          emit('set-page-loading', false);
          loadingButton.value = false;
        });
    };

    /**
     * Attach event listener for shown accordion section
     */
    const attachShowAccordionListener = () => {
      sectionRef.value
        .closest('.accordion-collapse')
        .addEventListener('show.bs.collapse', () => {
          resetState();
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
      editUser,
      formData,
      submitForm,
      loadingButton,
      errors,
      sectionRef,
    };
  },
};
</script>
