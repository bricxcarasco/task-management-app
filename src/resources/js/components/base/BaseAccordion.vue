<template>
  <div class="accordion-item mb-0">
    <h2 class="accordion-header" id="headingOne">
      <button
        class="accordion-button collapsed"
        type="button"
        data-bs-toggle="collapse"
        :data-bs-target="`#collapse${index}`"
        @click.prevent="handleAccordionClick"
      >
        {{ title }}
      </button>
    </h2>
    <div
      class="accordion-collapse collapse"
      :id="`collapse${index}`"
      aria-labelledby="headingOne"
      data-bs-parent="#accordionExample"
    >
      <div class="accordion-body">
        <div class="col-sm-12 mb-3">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BaseAccordion',
  props: {
    index: {
      type: Number,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
  },
  emits: ['accordionClick'],
  setup(props, { emit }) {
    const handleAccordionClick = () => {
      emit('accordionClick', props.index);
    };

    return {
      handleAccordionClick,
    };
  },
};
</script>

<style></style>
