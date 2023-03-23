<template>
  <div
    class="modal fade"
    id="complete-task-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitCompleteTask" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">{{ $t('headers.task_complete') }}</h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12 text-center">
                {{ $t('messages.tasks.confirm_completion_selected_task') }}
                <input type="hidden" name="id" />
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.complete') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import TaskApiService from '../../../api/tasks/task';
import i18n from '../../../i18n';

export default {
  name: 'CompleteTaskModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const taskApiService = new TaskApiService();
    const modalLoading = ref(false);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      const modal = document.querySelector('#complete-task-modal');
      modal.querySelector('.btn-close').click();
      modal.querySelector('form').reset();
    };

    /**
     * Event listener for add task form submit
     *
     * @returns {void}
     */
    const submitCompleteTask = async (event) => {
      event.preventDefault();
      const formData = objectifyForm(event.target);

      // Reinitialize state
      modalLoading.value = true;
      emit('reset-alert');

      taskApiService
        .completeTasks({ ids: [formData.id] })
        .then(() => {
          const message = i18n.global.t(
            'messages.tasks.completed_the_selected_tasks'
          );
          emit('set-alert', 'success', message);
          emit('get-tasks');

          resetModal();
        })
        .catch(() => {
          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      submitCompleteTask,
      modalLoading,
    };
  },
};
</script>
