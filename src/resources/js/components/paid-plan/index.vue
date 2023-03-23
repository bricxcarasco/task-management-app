<template>
  <div>
    <payment-method-modal ref="paymentMethodModalRef" />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <div class="col-12 col-md-9 offset-md-3">
          <h3>Test</h3>
          <button
            type="button"
            class="btn btn-primary"
            @click="() => paymentMethodModalRef.show()"
          >
            Payment Method
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import PaymentMethodModal from './components/PaymentMethodModal.vue';

export default {
  name: 'TestIndex',
  components: {
    PaymentMethodModal,
  },
  setup() {
    const paymentMethodModalRef = ref(null);

    return {
      paymentMethodModalRef,
    };
  },
};
</script>
