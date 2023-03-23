<template>
  <div>
    <p class="text-center mb-0">
      {{ $t('paragraphs.check_the_content_and_press_the_finish_button') }}
    </p>
    <div class="mb-3">
      <label class="fs-sm fw-bold"> ■{{ $t('labels.title') }} </label>
      <p class="mb-0">
        {{ form_data.workflow_title }}
      </p>
    </div>
    <div class="mb-3">
      <label class="fs-sm fw-bold"> ■{{ $t('labels.explanation') }} </label>
      <p class="mb-0">
        {{ form_data.caption }}
      </p>
    </div>
    <div class="mb-3">
      <label class="fs-sm fw-bold"> ■{{ $t('labels.priority') }} </label>
      <p class="mb-0">
        {{ WorkflowPriorities[form_data.priority.toUpperCase()].text }}
      </p>
    </div>
    <div class="mb-3">
      <label class="fs-sm fw-bold"> ■{{ $t('labels.attachment') }} </label>
      <ul class="ps-0" style="list-style: none">
        <li v-for="attachment in form_data.attachments" :key="attachment">
          <!-- Display Locally uploaded file name(s) -->
          <p class="mb-0 text-break">
            {{ attachment }}
          </p>
        </li>
        <li v-for="attachment in form_data.attaches" :key="attachment">
          <a href="#">{{ attachment.link }}</a>
        </li>
      </ul>
    </div>
    <div class="mb-3">
      <label class="fs-sm fw-bold">
        ■{{ $t('labels.designated_approver') }}
      </label>
      <ol class="ps-0" style="list-style: none">
        <li v-for="approver in form_data.approvers" :key="approver.sequence">
          {{ approver.name }}
        </li>
      </ol>
    </div>
    <div class="text-center mt-4">
      <button
        type="button"
        class="btn btn-primary me-2"
        @click="$emit('previous-step', CreateWorkflowSteps.APPROVAL, true)"
      >
        {{ $t('labels.return') }}
      </button>
      <button
        type="button"
        class="btn btn-primary"
        @click="$emit('save-workflow')"
      >
        {{ $t('buttons.complete') }}
      </button>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import CreateWorkflowSteps from '../../../enums/CreateWorkflowSteps';
import WorkflowPriorities from '../../../enums/WorkflowPriorities';

export default defineComponent({
  name: 'WorkflowConfirmation',
  components: {},
  props: {
    form_data: {
      type: [Array, Object],
    },
  },
  setup() {
    return {
      CreateWorkflowSteps,
      WorkflowPriorities,
    };
  },
});
</script>
