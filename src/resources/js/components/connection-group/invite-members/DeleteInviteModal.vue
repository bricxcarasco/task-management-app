<template>
  <div
    class="modal fade"
    id="delete-invite-modal"
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
            <h4 class="modal-title"></h4>
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
              <div class="col-12 text-center">
                {{ $t('messages.connection_group.delete_invite_confirm') }}
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.confirm_cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ApiService from '../../../api/connection_group/invite-member';
import i18n from '../../../i18n';

export default {
  name: 'DeleteInviteModal',
  props: {
    invite_id: {
      type: [Number, null],
      default: null,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const apiService = new ApiService();
    const modalLoading = ref(false);
    const modalRef = ref(null);
    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    /**
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      // Handle responses
      apiService
        .deleteInvite(props.invite_id)
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.connection_group.delete_invite_sent')
          );
          emit('reset-list');

          resetModal();
        })
        .catch(() => {
          emit('set-alert', 'failed');
          emit('reset-list');

          resetModal();
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitForm,
      resetModal,
      modalLoading,
      modalRef,
      modal,
    };
  },
};
</script>
