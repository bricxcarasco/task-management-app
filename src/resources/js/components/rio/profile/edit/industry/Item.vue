<template>
  <div class="card mt-3 card-1">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="card-title text-center card-title--center">
          {{ `${$t('labels.industry')}${item_number + 1}` }}
        </h5>
        <div class="button-container">
          <!-- Edit button -->
          <button
            class="btn btn--hover-black p-1 mb-1"
            type="button"
            @click="onEdit"
          >
            <i class="ai-edit"></i>
          </button>
          <!-- Delete button -->
          <button
            class="btn-close p-1"
            type="button"
            aria-label="Close"
            @click="onDelete(industry.id)"
          ></button>
        </div>
      </div>
      <!-- Industry Name -->
      <div class="mb-3">
        <label for="select-input" class="form-label">{{
          $t('labels.industry')
        }}</label>
        <input
          class="form-control"
          type="text"
          :value="industry.value"
          readonly
        />
      </div>
      <!-- Years of Experience -->
      <div class="mb-3">
        <label for="select-input" class="form-label">{{
          $t('labels.years_of_experiences')
        }}</label>
        <input
          class="form-control"
          type="text"
          :value="industry.years"
          readonly
        />
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import Common from '../../../../../common';

export default {
  name: 'IndustryItem',
  props: {
    industry: {
      type: Object,
      required: true,
    },
    item_number: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    /* eslint no-undef: 0 */
    const deleteModalNode = document.getElementById('delete-industry-modal');
    const formModalNode = document.getElementById('industry-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Emit delete industry event
     *
     * @param {int} id
     * @returns {void}
     */
    const onDelete = (id) => {
      // Inject record id to modal
      const field = deleteModalNode.querySelector('input[name=id]');
      field.value = id;
      deleteModal.value.show();
    };

    /**
     * Emit update industry event
     *
     * @returns {void}
     */
    const onEdit = () => {
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, props.industry);

      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
