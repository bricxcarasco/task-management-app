<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Form modal -->
    <form-modal
      :rio="rio"
      :service_selections="service_selections"
      :priority_selections="priority_selections"
      :time_selection="time_selection"
      @get-tasks="getTasks"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <delete-tasks-modal
      ref="deleteTasksModalRef"
      @delete-tasks="handleDeleteTasks"
    />

    <!-- Search modal -->
    <search-modal @set-keyword="setKeyword" :keyword="formData.keyword" />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div
            class="
              d-flex
              align-items-center
              justify-content-between
              border-bottom
              pb-3
              mb-0 mb-md-2
              position-relative
            "
          >
            <p class="m-0 flex-1">{{ $t('labels.select_display') }}</p>
            <select
              class="form-select w-25"
              id="select-input"
              v-model="formData.filter"
            >
              <option
                v-for="(service, index) in task_subject_selection"
                :key="index"
                :value="index"
              >
                {{ service }}
              </option>
            </select>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="mt-2">
              <button
                type="button"
                class="btn btn-link p-0 me-2 c-primary"
                @click="() => deleteTasksModalRef.show()"
                :disabled="isDisabledDeleteButton"
              >
                <i class="ai-trash fs-lg"></i>
              </button>
              <button
                type="button"
                class="btn btn-link p-0 me-2 c-primary"
                @click="handleCompleteTasks"
                :disabled="isDisabledCompleteButton"
              >
                <i class="ai-check fs-lg"></i>
              </button>
              <button
                type="button"
                class="btn btn-link p-0 c-primary"
                data-bs-toggle="modal"
                data-bs-target="#task-search-modal"
              >
                <i class="ai-search fs-lg"></i>
              </button>
            </div>
            <button class="btn btn-link" type="button" @click="handleOnCreate">
              <i class="ai-plus me-2"></i>{{ $t('buttons.add_tasks') }}
            </button>
          </div>
          <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="flex-1">
              <input
                class="form-check-input"
                type="checkbox"
                id="alltask"
                v-model="formData.finished"
              />
              <label for="checkbox" class="form-label">{{
                $t('labels.show_completed')
              }}</label>
            </div>
            <select
              class="form-select w-50"
              id="select-input"
              v-model="formData.sortBy"
            >
              <option :value="TaskSortTypes.registration_newest">
                {{ $t('labels.registration_newest') }}
              </option>
              <option :value="TaskSortTypes.newest_due_date">
                {{ $t('labels.newest_due_date') }}
              </option>
              <option :value="TaskSortTypes.oldest_due_date">
                {{ $t('labels.oldest_due_date') }}
              </option>
            </select>
          </div>
          <!-- Free Word Search Indicator -->
          <ul class="filters list-inline mb-0 mt-2" v-if="isFreeWordSearch">
            <li class="list-inline-item">
              <a href="#" class="btn btn-link p-0" @click="clearKeyword">
                <i class="ai-x"></i>
                {{ $t('labels.narrow_down') }}
              </a>
            </li>
          </ul>
          <ul class="list-group mt-2">
            <li
              v-for="(list, index) in taskList"
              :key="index"
              class="list-group-item task__list"
            >
              <label
                class="
                  d-flex
                  align-items-center
                  justify-content-between
                  position-relative
                "
              >
                <span
                  v-if="list.finished === TaskStatusTypes.COMPLETION"
                  class="text-danger task-checkbox fs-xs"
                  >{{ $t('labels.completion') }}</span
                >
                <input
                  v-else
                  v-model="taskListCheckbox"
                  :value="list.id"
                  class="form-check-input task-checkbox"
                  type="checkbox"
                  name="task-list"
                />
                <div class="position-relative ms-5">
                  <span
                    v-if="list.priority === TaskPriorityTypes.LOW"
                    class="priority"
                    >!</span
                  >
                  <span
                    v-else-if="list.priority === TaskPriorityTypes.MID"
                    class="priority"
                    >!!</span
                  >
                  <span
                    v-else-if="list.priority === TaskPriorityTypes.HIGH"
                    class="priority"
                    >!!!</span
                  >
                  <span v-else class="priority"></span>
                  <span
                    class="form-check-label ellipsis ellipsis--task text-start"
                    for="switch1"
                    >{{ list.task_title }}</span
                  >
                  <p
                    v-if="list.limit_datetime !== null"
                    class="c-gray fs-xs m-0"
                  >
                    {{ $t('labels.deadline') }}：{{ list.limit_datetime }}
                  </p>
                </div>
                <div class="text-center">
                  <span class="fs-xs ellipsis ellipsis--company">{{
                    list.name
                  }}</span>
                  <div class="text-center">
                    <div v-if="list.finished === TaskStatusTypes.COMPLETION">
                      <a
                        href="#"
                        class="fs--status"
                        @click="handleClickReturnTask(list.id)"
                        >{{ $t('links.return_to_incomplete') }}</a
                      >
                    </div>
                    <button
                      type="button"
                      class="btn btn-link p-0 me-2 c-primary"
                      @click="handleOnDuplicate(list)"
                    >
                      <i class="ai-file fs-lg"></i>
                    </button>

                    <button
                      v-if="list.finished !== TaskStatusTypes.COMPLETION"
                      type="button"
                      class="btn btn-link p-0 me-2 c-primary"
                      @click="handleOnEdit(list)"
                    >
                      <i class="ai-edit fs-lg"></i>
                    </button>
                  </div>
                </div>
              </label>
            </li>
          </ul>
          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-2 mb-3">
            <pagination :meta="paginationData" @changePage="changePage" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, watch, computed, onMounted } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import FormModal from '../components/FormModal.vue';
import SearchModal from '../components/SearchModal.vue';
import DeleteTasksModal from '../components/DeleteTasksModal.vue';
import TaskApiService from '../../../api/tasks/task';
import i18n from '../../../i18n';
import TaskOperationEnum from '../../../enums/TaskOperations';
import Common from '../../../common';
import TaskPriorityTypes from '../../../enums/TaskPriorityTypes';
import TaskStatusTypes from '../../../enums/TaskStatusTypes';
import TaskSortTypes from '../../../enums/TaskSortTypes';
import Pagination from '../../base/BasePagination.vue';

export default defineComponent({
  name: 'TaskListIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    service_selections: {
      type: [Array, Object],
      required: true,
    },
    priority_selections: {
      type: [Array, Object],
      required: true,
    },
    task_subject_selection: {
      type: [Array, Object],
      required: true,
    },
    time_selection: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    Pagination,
    PageLoader,
    BaseAlert,
    FormModal,
    SearchModal,
    DeleteTasksModal,
  },
  setup() {
    const taskApiService = new TaskApiService();
    const taskListCheckbox = ref([]);
    let operationNode = null;
    let formModalNode = null;
    let formModal = null;
    const pageLoading = ref(false);
    const isDisabledDeleteButton = ref(true);
    const isDisabledCompleteButton = ref(true);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      filter: '',
      sortBy: 0,
      finished: false,
    });
    const taskList = ref([]);
    const paginationData = ref([]);
    const defaultPage = 1;
    const isFreeWordSearch = ref(false);
    const deleteTasksModalRef = ref(null);

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Set alert messages
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status = 'success', message = null) => {
      const statusValue = status === 'success' ? 'success' : 'failed';
      alert.value[statusValue] = true;
      alert.value.message = message;
    };

    /**
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Get list of chat rooms
     *
     * @returns {void}
     */
    const getTasks = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getListApi = await taskApiService.getLists(formData.value);
        const getListResponseData = getListApi.data;
        taskList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
      } catch (error) {
        setAlert('failed');
        taskList.value = [];
        throw error;
      } finally {
        setPageLoading(false);
      }
    };

    /**
     * Construct form data
     *
     * @param {Object} data
     * @returns {Object}
     */
    const constructFormData = (data) => ({
      owner: data.owner,
      task_title: data.task_title,
      limit_date: data.limit_date,
      limit_time: data.limit_time,
      priority: data.priority,
      remarks: data.remarks,
    });

    /**
     * Create task handler
     */
    const handleOnCreate = () => {
      operationNode.value = TaskOperationEnum.ADD;
      formModal.value.show();
    };

    /**
     * Duplicate task handler
     *
     * @param {object} task
     * @returns {void}
     */
    const handleOnDuplicate = (task) => {
      const form = formModalNode.querySelector('form');
      operationNode.value = TaskOperationEnum.DUPLICATE;

      Common.fillForm(form, constructFormData(task));
      formModal.value.show();
    };

    /**
     * Edit task handler
     *
     * @param {object} task
     * @returns {void}
     */
    const handleOnEdit = (task) => {
      const form = formModalNode.querySelector('form');
      const data = constructFormData(task);
      operationNode.value = TaskOperationEnum.EDIT;
      data.id = parseInt(task.id, 10);

      Common.fillForm(form, data);
      formModal.value.show();
    };

    /**
     * Mark tasks as completed handler
     *
     * @returns {void}
     */
    const handleCompleteTasks = (event) => {
      event.preventDefault();

      const selectedTasks = taskListCheckbox.value;

      // Start page loading
      setPageLoading(true);

      taskApiService
        .completeTasks({ ids: selectedTasks })
        .then(() => {
          const message = i18n.global.t(
            'messages.tasks.completed_the_selected_tasks'
          );
          setAlert('success', message);
          taskListCheckbox.value = [];
          getTasks();
        })
        .catch(() => {
          setAlert('failed', i18n.global.t('alerts.error'));
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Delete selected tasks handler
     *
     * @returns {void}
     */
    const handleDeleteTasks = () => {
      const selectedTasks = taskListCheckbox.value;

      // Start modal loading
      deleteTasksModalRef.value.setLoading(true);

      taskApiService
        .deleteTasks({ ids: selectedTasks })
        .then(() => {
          const message = i18n.global.t(
            'messages.tasks.deleted_the_selected_tasks'
          );
          setAlert('success', message);
          taskListCheckbox.value = [];
          getTasks();
        })
        .catch(() => {
          setAlert('failed', i18n.global.t('alerts.error'));
        })
        .finally(() => {
          deleteTasksModalRef.value.setLoading(false);
          deleteTasksModalRef.value.hide();
          setPageLoading(false);
        });
    };

    /**
     * Return task to incomplete
     *
     * @returns {void}
     */
    const handleClickReturnTask = async (id) => {
      pageLoading.value = true;
      resetAlert();
      try {
        await taskApiService.returnTask(id).then(() => {
          getTasks();
        });
      } catch (error) {
        setAlert('failed');
        taskList.value = [];
        throw error;
      } finally {
        pageLoading.value = false;
      }
    };

    /**
     * Change page
     *
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getTasks();
    };

    /**
     * Set search keyword
     *
     * @returns {void}
     */
    const setKeyword = (keyword) => {
      formData.value.keyword = keyword;
      isFreeWordSearch.value =
        keyword !== '' && keyword !== undefined && keyword !== null;

      getTasks();
    };

    /**
     * Clear search keyword
     *
     * @returns {void}
     */
    const clearKeyword = () => {
      delete formData.value.keyword;
      isFreeWordSearch.value = false;
      getTasks();
    };

    /**
     * Watch for guests changes
     */
    watch(
      () => [
        formData.value.filter,
        formData.value.sortBy,
        formData.value.finished,
      ],
      ([filter, sort, status]) => {
        formData.value.filter = filter;
        formData.value.sortBy = sort;
        formData.value.finished = status;
        formData.value.page = defaultPage;
        getTasks();
      }
    );

    /**
     * Watch if has selected tasks
     */
    watch(
      () => taskListCheckbox.value,
      () => {
        if (taskListCheckbox.value.length > 0) {
          isDisabledDeleteButton.value = false;
          isDisabledCompleteButton.value = false;
        } else {
          isDisabledDeleteButton.value = true;
          isDisabledCompleteButton.value = true;
        }
      }
    );

    /**
     * On mounted properties
     */
    onMounted(() => {
      /* eslint no-undef: 0 */
      formModalNode = document.getElementById('task-form-modal');
      operationNode = document.querySelector(
        "form input#operation[type='hidden']"
      );
      formModal = computed(() => new bootstrap.Modal(formModalNode));

      // Get task list
      getTasks();
    });

    return {
      pageLoading,
      alert,
      setPageLoading,
      setAlert,
      resetAlert,
      deleteTasksModalRef,
      handleDeleteTasks,
      handleCompleteTasks,
      isDisabledDeleteButton,
      isDisabledCompleteButton,
      getTasks,
      handleOnCreate,
      handleOnDuplicate,
      handleOnEdit,
      setKeyword,
      clearKeyword,
      isFreeWordSearch,
      formData,
      taskList,
      taskListCheckbox,
      TaskPriorityTypes,
      TaskStatusTypes,
      TaskSortTypes,
      changePage,
      paginationData,
      defaultPage,
      handleClickReturnTask,
    };
  },
});
</script>
