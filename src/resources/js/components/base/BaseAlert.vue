<template>
  <div class="alert-fixed">
    <div
      v-if="primary"
      class="alert alert-primary alert-dismissible fade show"
      role="alert"
    >
      Primary
      <button
        @click.prevent="close"
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
    <div
      v-if="success"
      class="alert alert-success alert-dismissible fade show"
      role="alert"
    >
      {{ message ?? $t('alerts.success') }}
      <button
        @click.prevent="close"
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
    <div
      v-if="danger"
      class="alert alert-danger alert-dismissible fade show"
      role="alert"
    >
      {{ message ?? $t('alerts.error') }}
      <button
        @click.prevent="close"
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
    <div
      v-if="warning"
      class="alert alert-warning alert-dismissible fade show"
      role="alert"
    >
      Warning
      <button
        @click.prevent="close"
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
    <div
      v-if="info"
      class="alert alert-info alert-dismissible fade show"
      role="alert"
    >
      Info
      <button
        @click.prevent="close"
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';

export default {
  name: 'BaseAlert',
  props: {
    primary: {
      type: Boolean,
      default: false,
    },
    success: {
      type: Boolean,
      default: false,
    },
    danger: {
      type: Boolean,
      default: false,
    },
    warning: {
      type: Boolean,
      default: false,
    },
    info: {
      type: Boolean,
      default: false,
    },
    message: {
      type: String,
      default: '',
    },
  },
  emits: ['closeAlert'],
  setup(props, { emit }) {
    const close = () => {
      emit('closeAlert');
    };

    // Timeout placeholder
    const dismissAlertTimeout = ref(null);

    /**
     * Auto-dismiss flash alert
     *
     * @returns {void}
     */
    const dismissAlert = () => {
      dismissAlertTimeout.value = setTimeout(() => {
        close();
      }, 3000);
    };

    /**
     * Handles auto-dismiss action for state changes
     *
     * @returns {void}
     */
    const handleAutoDismiss = (shown) => {
      clearTimeout(dismissAlertTimeout.value);
      if (shown) {
        dismissAlert();
      }
    };

    /**
     * Attach handler on specified prop changes
     */
    watch(() => props.success, handleAutoDismiss);
    watch(() => props.danger, handleAutoDismiss);

    return {
      close,
    };
  },
};
</script>

<style scoped>
.alert-fixed {
  position: fixed;
  top: 0px;
  left: 0px;
  width: 100%;
  z-index: 9999;
  border-radius: 0px;
}
</style>
