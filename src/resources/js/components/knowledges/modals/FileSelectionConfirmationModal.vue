<template>
  <div
    class="modal fade"
    id="url-reference-confirmation"
    tabindex="-1"
    role="dialog"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="">
          <div class="modal-header">
            <i class="ai-arrow-left hoverable" @click="handleBackButton"></i>
            <h5 class="modal-title">
              {{
                $t('headers.personal_document_management', {
                  name: serviceName,
                })
              }}
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="p-2"></div>
            <p class="text-center mb-0">
              {{ $t('labels.paste_shared_link_as_reference_url') }}
            </p>
            <div class="p-4"></div>
            <div class="text-center mt-4">
              <input type="hidden" name="id" />
              <button
                type="button"
                class="btn btn-primary d-block w-100"
                @click="confirm"
              >
                {{ $t('buttons.paste_the_link') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default defineComponent({
  name: 'FileSelectionConfirmationModal',
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const modalLoading = ref(false);
    const service = ref(props.service);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#url-reference-confirmation');
      modal.querySelector('.btn-close').click();
    };

    /**
     * Event listener for form delete
     *
     * @returns {void}
     */
    const confirm = () => {
      modalLoading.value = true;
      emit('paste-url');
      modalLoading.value = false;
      resetModal();
    };

    /**
     * Handle back ( close modal and open document management modal )
     *
     */
    const handleBackButton = () => {
      resetModal();
      emit('handle-back');
    };

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case 'RIO':
          return data.full_name;
        case 'NEO':
          return data.organization_name;
        default:
          return `-`;
      }
    });

    return {
      confirm,
      resetModal,
      modalLoading,
      serviceName,
      handleBackButton,
    };
  },
});
</script>
