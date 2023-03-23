<template>
  <div
    class="modal fade"
    id="electronic-contract-transition-confirm-modal"
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
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 text-center">
              <p class="m-0">
                {{
                  $t('messages.electronic_contracts.register_confirmation_main')
                }}
              </p>
              <p class="m-0">
                {{
                  $t('messages.electronic_contracts.register_confirmation_sub')
                }}
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center border-0">
          <button type="button" class="btn btn-primary" @click="submitForm">
            {{ $t('buttons.cm_sign_redirect') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ElectronicContractService from '../../../api/electronic-contract/electronic_contract';
import i18n from '../../../i18n';

export default {
  name: 'TransitionConfirmationModal',
  components: {
    SectionLoader,
  },
  props: {
    form_data: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props, { emit }) {
    const electronicContractApiService = new ElectronicContractService();
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() =>
      bootstrap.Modal.getOrCreateInstance(modalRef.value)
    );

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
    const submitForm = async () => {
      setLoading(true);
      emit('reset-alert');

      await electronicContractApiService
        .register(props.form_data)
        .then((response) => {
          const responseData = response.data;
          const prepareUrl = responseData?.data?.prepare_url;
          const slotData = responseData?.data?.slot_data;
          const message = i18n.global.t(
            'messages.electronic_contracts.register_success'
          );

          // Open prepare url on new tab/window
          if (prepareUrl) {
            window.open(prepareUrl, '_blank');
          }

          // Update slot data
          if (slotData) {
            emit('update-slots', slotData);
          }

          emit('set-alert', 'success', message);
          emit('reset-form');
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (error.response.status === 422) {
            emit('set-errors', responseData.errors);
            return;
          }

          // Display alert message
          emit('set-alert', 'failed', responseData.message ?? null);
        })
        .finally(() => {
          setLoading(false);
          hide();
        });
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
      setLoading,
      show,
      hide,
      submitForm,
    };
  },
};
</script>
