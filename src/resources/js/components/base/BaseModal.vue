<template>
  <div
    ref="modalRef"
    class="modal fade"
    style="display: none"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ modalObject.title }}</h4>
          <button
            @click="handleClose"
            class="btn-close"
            type="button"
            aria-label="キャンセル"
          ></button>
        </div>
        <div class="modal-body">
          <slot />
        </div>
        <div class="modal-footer">
          <button
            @click="handleClose"
            class="btn btn-secondary btn-sm"
            type="button"
          >
            キャンセル
          </button>
          <button class="btn btn-primary btn-shadow btn-sm" type="button">
            {{ buttonTitle }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';

export default {
  name: 'BaseModal',
  props: {
    open: Boolean,
    modalObject: {
      type: Object,
      required: true,
    },
    buttonTitle: {
      type: String,
      default: '登録',
    },
  },
  emits: ['close'],
  setup(props, { emit }) {
    const modalRef = ref(null);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    const handleClose = () => {
      emit('close', modal.value);
      modal.value.hide();
    };

    onMounted(() => {
      if (props.open) {
        modal.value.show();
      } else {
        modal.value.hide();
      }
    });

    return {
      modalRef,
      handleClose,
    };
  },
};
</script>

<style></style>
