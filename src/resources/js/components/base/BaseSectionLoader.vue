<template>
  <div class="section-loader text-center" v-if="show">
    <div class="spinner-border text-primary"></div>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped>
.section-loader {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: white;
  opacity: 0.8;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 9;
  border-radius: inherit;
}

.section-loader--solid {
  opacity: 1;
}

.section-loader--lg > div {
  width: 3rem;
  height: 3rem;
}
</style>
