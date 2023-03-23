<template>
  <div
    class="modal fade"
    id="delete-setting-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="handleClickOnDelete" ref="formRef" novalidate>
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
                {{ $t('messages.rio.delete_confirmation') }}
                <input
                  type="hidden"
                  name="account_index"
                  v-model="formData.account_index"
                />
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.delete') }}
            </button>
            <button
              class="btn btn-secondary btn-shadow btn-sm"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            >
              {{ $t('buttons.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import { objectifyForm } from '../../../utils/objectifyForm';

export default {
  name: 'AccountDeleteModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#delete-setting-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
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
     * Emit delete bank account
     *
     * @returns {void}
     */
    const handleClickOnDelete = (event) => {
      event.preventDefault();
      const parseIndex = parseInt(formData.value.account_index, 10);
      resetModal();
      emit('delete-account', parseIndex);
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        updateModel();
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
      modalLoading,
      resetModal,
      handleClickOnDelete,
      modalRef,
      formRef,
      formData,
    };
  },
};
</script>
