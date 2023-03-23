<template>
  <div
    class="modal fade"
    id="share-setting-modal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <form ref="formRef" novalidate>
      <input type="hidden" name="document_id" v-model="formData.document_id" />
    </form>
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('headers.share_setting') }}
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
          <!-- Connected list without share access, Share function and Search function -->
          <connected-list
            :document_id="
              formData.document_id === undefined ? 0 : formData.document_id
            "
            @reset-alert="$emit('reset-alert')"
            @set-alert="setAlert"
            @reset-modal="resetModal"
            @update-modal-loading="updateModalLoading"
          />
          <!-- Connected list with share access and Unshare function -->
          <permitted-list
            :document_id="
              formData.document_id === undefined ? 0 : formData.document_id
            "
            @reset-alert="$emit('reset-alert')"
            @set-alert="setAlert"
            @reset-modal="resetModal"
            @update-modal-loading="updateModalLoading"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import ConnectedList from './ConnectedList.vue';
import PermittedList from './PermittedList.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'ShareSettingModal',
  components: {
    ConnectedList,
    PermittedList,
    SectionLoader,
  },
  setup(props, { emit }) {
    const errors = ref(null);
    const modalRef = ref(null);
    const modalLoading = ref(false);

    // Initialize form data
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
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Show/Hide modal loading display
     *
     * @returns {void}
     */
    const updateModalLoading = (status = false) => {
      modalLoading.value = status;
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

    /**
     * Set alert message
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status, message) => {
      emit('set-alert', status, message);
    };

    return {
      errors,
      formData,
      formRef,
      modalLoading,
      modalRef,
      resetModal,
      setAlert,
      updateModalLoading,
    };
  },
};
</script>
