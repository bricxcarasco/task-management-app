<template>
  <div
    class="modal fade"
    id="receiving-account-not-set"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ $t('headers.receiving_account_not_set') }}
          </h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p class="text-center mb-0">
            {{ $t('messages.classifieds.necessary_to_set_payment') }}
          </p>
          <div class="text-center mt-4">
            <a href="/classifieds/settings" type="button" class="btn btn-link">
              {{ $t('buttons.redirect_to_receiving_account') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ReceivingAccountNotSetModalComponent',
  setup() {
    return {};
  },
});
</script>
