<template>
  <div
    class="modal fade"
    id="transaction-flow"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.transaction_flow') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <ul>
            <li>
              {{ $t('messages.classifieds.transaction_flow_message_1') }}
            </li>
            <li>
              {{ $t('messages.classifieds.transaction_flow_message_2') }}
            </li>
            <li>
              {{ $t('messages.classifieds.transaction_flow_message_3') }}
            </li>
            <li>
              {{ $t('messages.classifieds.transaction_flow_message_4') }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TransactionFlowModalComponent',
  setup() {
    return {};
  },
});
</script>
