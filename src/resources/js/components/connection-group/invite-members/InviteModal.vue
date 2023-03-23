<template>
  <div
    class="modal fade"
    id="invite-member-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit.prevent="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">{{ $t('headers.member_invitation') }}</h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <!-- Profile Image and Full Name -->
            <div class="mb-3">
              <img
                class="
                  rounded-circle
                  me-2
                  d-inline-block
                  js-profile-image
                  img--profile_image_sm
                "
                data-field="profile_image"
                :src="user?.profile_image"
                @error="
                  Common.handleNotFoundImageException(
                    $event,
                    DefaultImageCategory.RIO_NEO
                  )
                "
                width="40"
              />
              <span class="c-primary ms-2">{{ user?.full_name }}</span>
            </div>
            <!-- Invitation Message -->
            <textarea
              class="form-control"
              :class="errors?.invite_message != null ? 'is-invalid' : ''"
              name="message"
              id="textarea-input"
              rows="5"
              v-model="formData.invite_message"
              :placeholder="this.$i18n.t('messages.input_invitation_message')"
            ></textarea>
            <base-validation-error :errors="errors?.invite_message" />
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
import { ref, computed } from 'vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ApiService from '../../../api/connection_group/invite-member';
import i18n from '../../../i18n';

export default {
  name: 'InviteModal',
  props: {
    user: {
      type: [Array, Object, null],
      required: true,
    },
    group_id: {
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
    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));
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
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Inject ID of selected user
      formData.value.rio_id = props.user.id;

      // Handle responses
      apiService
        .sendInvite(props.group_id, formData.value)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.connection_group.invite_sent')
          );
          emit('reset-list');

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          // Handle forbidden errors
          if (responseData.status_code === 403) {
            resetModal();
          }

          emit('reset-list');
          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
      modal,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
