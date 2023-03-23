<template>
  <div class="card mt-3 card-1">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="card-title text-center card-title--center">
          {{ `${$t('labels.educational_background')}${item_number + 1}` }}
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
            @click="onDelete(educational_background.id)"
          ></button>
        </div>
      </div>

      <!-- School name -->
      <div class="mb-3">
        <label for="text-input" class="form-label">
          {{ $t('labels.school_name') }}
        </label>
        <input
          class="form-control"
          type="text"
          id="text-input"
          :value="educational_background.school_name"
          readonly
        />
      </div>

      <!-- Graduation date -->
      <div class="mb-3" v-if="educational_background.graduation_date_formatted">
        <label for="select-input" class="form-label">
          {{ $t('labels.graduation_date') }}
        </label>
        <input
          class="form-control rounded pe-5"
          type="text"
          :value="educational_background.graduation_date_formatted"
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
  name: 'EducationalBackgroundItem',
  props: {
    educational_background: {
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
    const deleteModalNode = document.getElementById('delete-educational-modal');
    const formModalNode = document.getElementById('educational-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Emit delete educational background event
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
      /* eslint-disable no-underscore-dangle */
      const dateField = form.querySelector(
        'input[name=graduation_date]'
      )._flatpickr;
      Common.fillForm(form, props.educational_background);
      dateField.setDate(props.educational_background.graduation_date, true);
      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
