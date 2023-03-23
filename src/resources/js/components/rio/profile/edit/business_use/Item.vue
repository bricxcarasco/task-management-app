<template>
  <li
    class="
      bg-gray
      list-group-item
      d-flex
      align-items-center
      justify-content-between
      py-0
      px-2
      mt-2
    "
  >
    <p class="mb-0 text-truncate" style="flex: 1">
      {{ affiliate.organization_name }}
    </p>
    <div class="d-flex align-items-center justify-content-between">
      <p class="my-0 me-1">{{ DisplayableEnum[affiliate.is_display] }}</p>
      <button
        class="btn btn--hover-black p-1 mb-1"
        type="button"
        @click="onEdit"
      >
        <i class="color-primary ai-globe"></i>
      </button>
    </div>
  </li>
</template>

<script>
import { computed } from 'vue';
import Common from '../../../../../common';
import DisplayableEnum from '../../../../../enums/Displayable';

export default {
  name: 'AffiliateItem',
  props: {
    affiliate: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    /* eslint no-undef: 0 */
    const formModalNode = document.getElementById('affiliate-form-modal');
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const onEdit = () => {
      // Inject record id to modal
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, props.affiliate);
      formModal.value.show();
    };

    return {
      onEdit,
      DisplayableEnum,
    };
  },
};
</script>
