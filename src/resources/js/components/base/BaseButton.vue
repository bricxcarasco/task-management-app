<template>
  <button
    @click="clickButton"
    class="btn me-1"
    :class="buttonClass"
    type="button"
  >
    <div v-if="loading" class="d-flex justify-content-center">
      <div class="spinner-border spinner-border-sm" role="status"></div>
    </div>
    <span v-else>
      <i v-if="icon" :class="icon"></i>
      {{ title }}
    </span>
  </button>
</template>

<script>
import { computed } from 'vue';

export default {
  props: {
    title: {
      type: String,
      default: '登録',
    },
    buttonType: {
      type: String,
      default: 'primary',
      validator: (value) =>
        [
          'success',
          'warning',
          'danger',
          'info',
          'secondary',
          'primary',
          'light',
        ].indexOf(value) !== -1,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    icon: {
      type: String,
      default: null,
    },
  },
  emits: ['handleClick'],
  setup(props, { emit }) {
    const buttonClass = computed(() => `btn-${props.buttonType}`);

    const clickButton = () => {
      emit('handleClick');
    };

    return {
      buttonClass,
      clickButton,
    };
  },
};
</script>

<style scoped>
.spinner-border-sm {
  width: 1.4rem;
  height: 1.4rem;
}
</style>
