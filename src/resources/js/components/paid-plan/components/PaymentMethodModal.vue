<template>
  <div
    class="modal fade"
    id="PaymentMethodModal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">お支払方法選択</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="list-group list-group-flush">
            <a href="#" class="list-group-item">クレジットカード</a>
            <a href="#" class="list-group-item">銀行振込</a>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <p class="text-danger">
            年払いのご希望の方は銀行振込を選択してください。
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';

export default {
  name: 'PaymentMethodModal',
  setup() {
    const modalRef = ref(null);

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

    return {
      modalRef,
      modal,
      show,
    };
  },
};
</script>
