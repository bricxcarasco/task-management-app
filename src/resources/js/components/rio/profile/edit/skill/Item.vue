<template>
  <div class="d-flex align-items-center justify-content-between mb-3">
    <input
      class="form-control"
      type="text"
      :id="`skill-${skill.id}`"
      :value="skill.content"
      readonly
    />

    <!-- Edit Button -->
    <button
      class="btn btn--hover-black p-1 mx-2"
      type="button"
      @click="onEdit()"
    >
      <i class="ai-edit"></i>
    </button>

    <!-- Delete Button -->
    <button
      class="btn-close mx-2"
      type="button"
      aria-label="Close"
      @click="onDelete(skill.id)"
    ></button>
  </div>
</template>

<script>
import { computed } from 'vue';
import Common from '../../../../../common';

export default {
  name: 'SkillItem',
  props: {
    skill: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    /* eslint no-undef: 0 */
    const deleteModalNode = document.getElementById('delete-skill-modal');
    const formModalNode = document.getElementById('skill-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Inject record and open modal
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
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const onEdit = () => {
      // Inject record id to modal
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, props.skill);
      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
