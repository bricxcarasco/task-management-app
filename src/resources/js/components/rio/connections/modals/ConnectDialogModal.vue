<template>
  <div
    class="modal fade"
    id="dialogue"
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
            {{ rio.full_name }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p class="text-center">つながり申請しました。</p>
          <p class="text-center">
            承認された場合は通知アイコン及びメールにてお知らせます。
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup() {
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show connect conection modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide connect conection modal
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

    return {
      modalRef,
      modal,
      modalLoading,
      show,
      hide,
    };
  },
};
</script>
