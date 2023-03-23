<template>
  <div
    class="modal fade"
    id="task-form-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="handleFormSubmission" ref="formRef" novalidate>
          <input type="hidden" id="operation" value="add" />
          <input type="hidden" name="id" v-model="formData.id" />

          <div class="modal-header">
            <h4 class="modal-title">{{ modalTitle }}</h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">
                ■{{ $t('labels.register_rio_neo') }}
              </label>
              <select
                v-model="formData.owner"
                name="owner"
                class="form-select"
                :class="errors?.owner != null ? 'is-invalid' : ''"
              >
                <option
                  v-for="(service, index) in service_selections"
                  :key="index"
                  :value="index"
                >
                  {{ service }}
                </option>
              </select>
              <base-validation-error :errors="errors?.owner" />
            </div>
            <div class="mb-3">
              <label class="form-label"> ■{{ $t('labels.task_title') }} </label>
              <input
                v-model="formData.task_title"
                name="task_title"
                class="form-control"
                :class="errors?.task_title != null ? 'is-invalid' : ''"
                type="text"
              />
              <base-validation-error :errors="errors?.task_title" />
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <label for="select-input" class="form-label m-0">
                  ■{{ $t('labels.duedate') }}（{{ $t('labels.any') }}）
                </label>
                <a
                  class="text-end datetime-clear btn btn-link py-0"
                  @click="handleCLickClear"
                >
                  {{ $t('buttons.reset_duedate') }}
                </a>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="pe-1 w-100">
                <input
                  v-model="formData.limit_date"
                  name="limit_date"
                  class="form-control datetime-picker_ymdhi rounded"
                  :class="errors?.limit_date != null ? 'is-invalid' : ''"
                  type="text"
                  :placeholder="$t('placeholders.select_duedate')"
                />
                <base-validation-error :errors="errors?.limit_date" />
              </div>
              <div class="w-75">
                <select
                  v-model="formData.limit_time"
                  name="limit_time"
                  class="form-select"
                  :class="errors?.limit_time != null ? 'is-invalid' : ''"
                >
                  <option value="">--:--</option>
                  <option
                    v-for="(limit_time, index) in time_selection"
                    :key="index"
                    :value="index"
                  >
                    {{ limit_time }}
                  </option>
                </select>
                <base-validation-error :errors="errors?.limit_time" />
              </div>
            </div>
            <div></div>
            <div class="mb-3">
              <label class="form-label">■{{ $t('labels.priority') }}</label>
              <select
                v-model="formData.priority"
                name="priority"
                class="form-select"
                :class="errors?.priority != null ? 'is-invalid' : ''"
              >
                <option value="">-</option>
                <option
                  v-for="(priority, index) in priority_selections"
                  :key="index"
                  :value="index"
                >
                  {{ priority }}
                </option>
              </select>
              <base-validation-error :errors="errors?.priority" />
            </div>
            <div class="mb-3">
              <label class="form-label">■{{ $t('labels.remarks') }}</label>
              <textarea
                class="form-control"
                id="textarea-input"
                rows="5"
                :class="errors?.remarks != null ? 'is-invalid' : ''"
                name="remarks"
                v-model="formData.remarks"
              ></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.register') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { objectifyForm } from '../../../utils/objectifyForm';
import BaseValidationError from '../../base/BaseValidationError.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import TaskApiService from '../../../api/tasks/task';
import TaskOperationEnum from '../../../enums/TaskOperations';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'TaskFormModal',
  props: {
    service_selections: {
      type: [Array, Object],
      required: true,
    },
    priority_selections: {
      type: [Array, Object],
      required: true,
    },
    time_selection: {
      type: [Array, Object],
      required: true,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const taskApiService = new TaskApiService();
    const operation = ref(null);
    const rio = ref(props.rio);
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const modalTitle = ref(null);
    const formRef = ref({});
    const formData = ref({});

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;

      switch (operation.value) {
        case TaskOperationEnum.ADD:
          formData.value.owner = `rio_${rio.value.id}`;
          formData.value.priority = '';
          break;
        case TaskOperationEnum.DUPLICATE:
          document.querySelector('.datetime-clear').click(); // Clear duedate
          break;
        default:
          break;
      }
    };

    /**
     * Update modal title depending on operation
     *
     * @returns {void}
     */
    const updateModalTitle = () => {
      switch (operation.value) {
        case TaskOperationEnum.ADD:
          modalTitle.value = i18n.global.t('headers.task_create');
          break;
        case TaskOperationEnum.EDIT:
          modalTitle.value = i18n.global.t('headers.task_edit');
          break;
        case TaskOperationEnum.DUPLICATE:
          modalTitle.value = i18n.global.t('headers.task_duplication');
          break;
        default:
          modalTitle.value = i18n.global.t('headers.task_create');
          break;
      }
    };

    /**
     * Event listener for add url form submit
     *
     * @returns {void}
     */
    const handleFormSubmission = (event) => {
      event.preventDefault();

      const data = { ...formData.value };
      const taskId = parseInt(data.id, 10);
      const [selectedService, id] = data.owner.split('_', 2);
      let apiCall = null;
      let message = null;

      // Set owner rio & neo IDs
      if (selectedService === 'rio') {
        data.owner_rio_id = parseInt(id, 10);
        data.owner_neo_id = null;
      } else {
        data.owner_neo_id = parseInt(id, 10);
        data.owner_rio_id = null;
      }

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      switch (operation.value) {
        case TaskOperationEnum.ADD:
        case TaskOperationEnum.DUPLICATE:
          apiCall = taskApiService.addTask(data);
          message = i18n.global.t('messages.tasks.task_has_been_created');
          break;
        case TaskOperationEnum.EDIT:
          apiCall = taskApiService.updateTask(taskId, data);
          message = i18n.global.t('messages.tasks.task_has_been_edited');
          break;
        default:
          break;
      }

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success', message);
          emit('get-tasks');

          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;

            if (responseData.data.limit_datetime) {
              errors.value.limit_datetime = responseData.data.limit_datetime;
            }

            return;
          }

          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Set modal operation
     */
    const setOperation = () => {
      const operationNode = document.querySelector(
        "form input#operation[type='hidden']"
      );
      operation.value = operationNode.value;
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        setOperation();
        updateModel();
        updateModalTitle();
      });
    };

    const handleCLickClear = () => {
      formData.value.limit_time = '';
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
    });

    return {
      formData,
      formRef,
      errors,
      modalLoading,
      modalTitle,
      handleFormSubmission,
      modalRef,
      resetModal,
      handleCLickClear,
    };
  },
});
</script>
