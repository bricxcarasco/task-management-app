<template>
  <div
    class="modal fade"
    id="attachmentConfirmation"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('links.sharing_settings') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p class="fs-sm">
            {{ $t('paragraphs.would_you_like_to_share_the_selected_file') }}
          </p>
          <div class="text-center">
            <button
              class="btn btn-primary"
              type="button"
              @click="sendAttachment"
            >
              {{ $t('buttons.share_and_paste_the_link') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';

export default {
  name: 'AttachmentConfirmationModal',
  props: {
    file: {
      type: Object,
      required: true,
    },
  },
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
     * Trigger set document file and emit to parent component
     *
     * @returns {void}
     */
    const sendAttachment = () => {
      setLoading(true);
      const data = [`${props.file.document_id}`];
      emit('send-attachment', data);
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
      sendAttachment,
    };
  },
};
</script>
