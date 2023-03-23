<template>
  <div
    class="modal fade"
    :id="id"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="onDelete" novalidate>
          <div class="modal-header">
            <h4 class="modal-title"></h4>
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
                {{ $t('messages.rio.delete_confirmation') }}
                <slot />
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-danger btn-shadow btn-sm" type="submit">
              {{ $t('buttons.delete') }}
            </button>
            <button
              class="btn btn-secondary btn-shadow btn-sm"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
            >
              {{ $t('buttons.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';

export default {
  name: 'DeleteProfessionModal',
  props: {
    id: {
      type: String,
      required: true,
    },
    modalLoading: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const modalRef = ref(false);

    /**
     * Event listener for add profession form submit
     *
     * @returns {void}
     */
    const onDelete = async (event) => {
      event.preventDefault();
      emit('confirm-delete', event);
    };

    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        modalRef.value.querySelector('form').reset();
      });
    });

    return {
      modalRef,
      onDelete,
    };
  },
};
</script>
