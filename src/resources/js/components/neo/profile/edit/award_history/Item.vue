<template>
  <div class="card mt-3 card-1">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="card-title text-center card-title--center">
          {{ `${$t('labels.award_history')}${item_number + 1}` }}
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
            class="btn-close"
            type="button"
            aria-label="Close"
            @click="onDelete(award.id)"
          ></button>
        </div>
      </div>
      <div class="mb-3">
        <label for="text-input" class="form-label">
          {{ $t('labels.award_year') }}
        </label>
        <input
          class="form-control"
          type="text"
          id="text-input"
          :value="award.award_year_display"
          readonly
        />
      </div>
      <div class="mb-3">
        <label for="select-input" class="form-label">
          {{ $t('labels.content') }}
        </label>
        <input
          class="form-control rounded pe-5"
          type="text"
          :value="award.content"
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
  name: 'AwardItem',
  props: {
    award: {
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
    const deleteModalNode = document.getElementById('delete-award-modal');
    const formModalNode = document.getElementById('award-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Emit delete award history event
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
     * Emit update award event
     *
     * @returns {void}
     */
    const onEdit = () => {
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, props.award);
      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
