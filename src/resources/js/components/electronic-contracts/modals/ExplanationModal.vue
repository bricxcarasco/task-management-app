<template>
  <div
    class="modal fade"
    id="electronicContractExplanation"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            {{ $t('paragraphs.electronic_contract_free_slots_explanation_one')
            }}<br /><br />

            {{ $t('paragraphs.electronic_contract_free_slots_explanation_two')
            }}<br /><br />

            {{
              $t('paragraphs.electronic_contract_free_slots_explanation_three')
            }}
          </p>
          <div class="text-center">
            <button type="button" class="btn btn-primary" @click="handleClick">
              {{ $t('buttons.contact_us') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';

export default {
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

    /**
     * Hide cancel conection modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
    };

    /**
     * Trigger set administrator button and emit to parent component
     *
     * @returns {void}
     */
    const handleClick = () => {
      window.open('https://hero.ne.jp/contact/', '_blank');
      modal.value.hide();
    };

    return {
      modalRef,
      modal,
      show,
      hide,
      handleClick,
    };
  },
};
</script>
