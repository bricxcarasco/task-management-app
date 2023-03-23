<template>
  <div
    class="modal fade"
    id="cancel"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('paragraphs.cancel_connection') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center justify-content-center">
            <button
              class="btn btn-primary btn--dialogue"
              type="button"
              @click="cancelConnection"
            >
              {{ $t('buttons.to_cancel') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  emits: ['handleUpdate'],
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show cancel conection modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide cancel conection modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
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
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
      });
    });

    /**
     * Trigger cancel connection button and emit to parent component
     *
     * @returns {void}
     */
    const cancelConnection = () => {
      setLoading(true);
      emit('handleUpdate');
    };

    return {
      modalRef,
      modal,
      modalLoading,
      show,
      hide,
      cancelConnection,
    };
  },
};
</script>
