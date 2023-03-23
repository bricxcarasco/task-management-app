<template>
  <div
    class="modal fade"
    id="acceptModal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('links.application_request') }}
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
              class="btn btn-primary btn-sm"
              type="button"
              @click="handleClick"
            >
              {{ $t('links.accept') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  props: {
    request: {
      type: [Object, null],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  emits: ['acceptRequest'],
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
     * Trigger set administrator button and emit to parent component
     *
     * @returns {void}
     */
    const handleClick = () => {
      const data = {
        connection_id: props.request.connection_id,
        id: props.request.id,
        service: props.request.service,
      };
      setLoading(true);
      emit('acceptRequest', data);
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
      });
    });

    return {
      modalRef,
      modal,
      modalLoading,
      show,
      hide,
      handleClick,
    };
  },
};
</script>
