<template>
  <div
    class="modal fade"
    id="connect"
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
            @click="resetModal"
          ></button>
        </div>
        <div class="modal-body">
          <p class="text-center">{{ $t('paragraphs.connect_application') }}</p>
          <div class="mb-3">
            <label class="form-label">
              {{ $t('headers.message') }}
            </label>
            <textarea
              class="form-control"
              id="textarea-input"
              rows="5"
              :class="errors?.message != null ? 'is-invalid' : ''"
              name="message"
              v-model="formData.message"
            ></textarea>
            <base-validation-error :errors="errors?.message" />
          </div>
          <div class="d-flex align-items-center justify-content-center">
            <button
              class="btn btn-primary btn--dialogue"
              type="button"
              @click="connectToRio"
            >
              {{ $t('buttons.apply_for_connection') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import BaseValidationError from '../../../base/BaseValidationError.vue';
import RioConnectionApi from '../../../../api/rio/connection';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
  },
  emits: ['handleConnectionRequest'],
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const errors = ref(null);
    const formData = ref({});
    const modalRef = ref(null);
    const modalLoading = ref(false);
    const rioConnection = new RioConnectionApi();

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
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
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
     * Trigger connect rio connection button and emit to parent component
     *
     * @returns {void}
     */
    const connectToRio = async () => {
      setLoading(true);

      await rioConnection
        .connect({ rio_id: props.rio.id, message: formData.value.message })
        .then((response) => {
          if (response.data.status_code === 200) {
            setLoading(true);
            emit(
              'handleConnectionRequest',
              response.data.data.connection_status
            );
            resetModal();
          }
        })
        .catch((error) => {
          const responseData = error.response?.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
        })
        .finally(() => setLoading(false));
    };

    return {
      connectToRio,
      errors,
      formData,
      hide,
      modal,
      modalLoading,
      modalRef,
      setLoading,
      show,
      resetModal,
    };
  },
};
</script>
