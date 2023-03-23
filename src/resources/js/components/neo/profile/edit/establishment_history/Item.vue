<template>
  <div class="card mt-3 card-1">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-end">
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
            @click="onDelete(history.id)"
          ></button>
        </div>
      </div>

      <!-- Date -->
      <div class="mb-3">
        <label for="select-input" class="form-label">
          {{ $t('labels.year_month') }}
        </label>
        <input
          class="form-control rounded pe-5"
          type="text"
          :value="history.additional"
          readonly
        />
      </div>

      <!-- Content -->
      <div class="mb-3">
        <label for="text-input" class="form-label">
          {{ $t('labels.content') }}
        </label>
        <input
          class="form-control"
          type="text"
          id="text-input"
          :value="history.content"
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
  name: 'HistoryItem',
  props: {
    history: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    /* eslint no-undef: 0 */
    const deleteModalNode = document.getElementById('delete-history-modal');
    const formModalNode = document.getElementById('history-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Emit delete history background event
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
     * Emit update history event
     *
     * @returns {void}
     */
    const onEdit = () => {
      const form = formModalNode.querySelector('form');
      /* eslint-disable no-underscore-dangle */
      const dateField = form.querySelector('input[name=additional]')._flatpickr;
      Common.fillForm(form, props.history);
      dateField.setDate(props.history.additional, true);
      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
