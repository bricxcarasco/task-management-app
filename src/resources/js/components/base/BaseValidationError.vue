<template>
  <div v-show="errors" class="invalid-feedback">
    <p class="m-0" v-for="(error, index) in errors" :key="index">
      {{ error }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'BaseValidationError',
  props: {
    errors: {
      type: [Array, Object],
      default: () => ({}),
    },
  },
};
</script>
