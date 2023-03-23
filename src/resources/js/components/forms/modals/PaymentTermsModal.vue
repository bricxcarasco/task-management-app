<template>
  <div
    class="modal fade"
    id="selectPaymenTerms"
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
            {{ $t('labels.payment_terms') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="hide"
          ></button>
        </div>
        <div class="modal-body">
          <div v-if="basic_setting?.payment_term_list?.length !== 0">
            <ul class="list-group list-group-flush">
              <span
                v-for="paymentTerm in basic_setting?.payment_term_list"
                :key="paymentTerm"
              >
                <li
                  class="
                    list-group-item
                    px-0
                    py-2
                    position-relative
                    list--white
                    px-2
                    hoverable
                  "
                  @click.prevent="handleSelectedPaymentTerm(paymentTerm)"
                >
                  {{ paymentTerm }}
                </li>
              </span>
            </ul>
          </div>
          <div v-else>
            <p class="text-center p-2 mb-0">
              {{ $t('paragraphs.there_is_no_payment_terms') }}
            </p>
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
  name: 'PaymentTermsModal',
  components: {
    SectionLoader,
  },
  props: {
    basic_setting: {
      type: [Object, null],
      required: true,
    },
  },
  setup(props, { emit }) {
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show select connected RIO/NEO modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide select connected RIO/NEO modal
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
     * Trigger set recipient and emit to parent component
     *
     * @param {Object} connection
     * @returns {void}
     */
    const handleSelectedPaymentTerm = (connection) => {
      setLoading(true);
      emit('choose-payment-term', connection);
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
      setLoading,
      handleSelectedPaymentTerm,
    };
  },
};
</script>
