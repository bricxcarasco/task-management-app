<template>
  <div class="js-function-info-container d-inline-block" v-if="modalExists">
    <button
      type="button"
      class="
        btn btn-primary btn-sm
        rounded-circle
        py-1
        px-2
        fs-xs
        lh-1
        js-function-info-modal
      "
    >
      ?
    </button>

    <div class="js-function-info-content d-none">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  setup() {
    const modal = document.querySelector('#function-info-modal');
    const modalExists = modal !== null;

    return {
      modalExists,
    };
  },
};
</script>
