<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Delete modal -->
    <delete-task-modal
      @get-tasks="handleReloadTaskList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Complete modal -->
    <complete-task-modal
      @get-tasks="handleReloadTaskList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

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
          <div class="d-flex flex-column h-100 rounded-3">
            <!-- Calendar -->
            <div id="calendar" style="overflow: auto"></div>

            <!-- Export button -->
            <div class="text-end" @click="handleRedirectionToExport">
              <button type="button" class="btn btn-link">
                {{ $t('buttons.export') }}
              </button>
            </div>

            <div class="events-list mb-3">
              <!-- Holidays -->
              <div
                v-for="holiday in holidays"
                :key="holiday.defId"
                class="toast holidays w-100 mt-2 show"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
              >
                <div class="toast-header">
                  <div
                    class="
                      d-inline-block
                      align-middle
                      bg-primary
                      rounded-1
                      me-2
                    "
                    style="width: 1.25rem; height: 1.25rem"
                  ></div>
                  <h6 class="fs-sm mb-0 me-auto holiday__date">
                    {{ holiday.title }}
                  </h6>
                  <button
                    type="button"
                    class="btn-close ms-2 mb-1"
                    data-bs-dismiss="toast"
                    aria-label="Close"
                    @click="handleCloseHolidays($event)"
                  ></button>
                </div>
                <div class="toast-body d-flex">
                  <p class="holiday__time" style="flex: 1">
                    {{ $t('labels.all_day') }}
                  </p>
                  <p class="holiday__title" style="flex: 1">
                    {{ holiday.title }}
                  </p>
                </div>
              </div>

              <!-- Scheduled events list -->
              <div class="events mt-4">
                <ul
                  class="p-0 mb-0"
                  v-for="event in eventsPerDate"
                  :key="event.id"
                >
                  <li class="d-flex mb-0">
                    <div class="bg-light border d-flex align-items-center w-25">
                      <p class="m-0 flex-1 fs-xs text-center">
                        {{ event.time }}
                      </p>
                    </div>
                    <div class="w-75">
                      <div
                        class="
                          bg-gray
                          d-flex
                          align-items-center
                          justify-content-between
                          border-bottom
                          p-2
                        "
                      >
                        <p class="m-0 fs-xs">{{ event.invited }}</p>
                        <span
                          class="badge fs-xs"
                          :class="event.status_classname"
                        >
                          {{ event.status }}
                        </span>
                      </div>
                      <div
                        class="
                          d-flex
                          align-items-center
                          justify-content-between
                          p-2
                          border
                          hoverable
                        "
                        @click="
                          handleRedirectionToDetail(
                            event.id,
                            event.ownerRioId,
                            event.ownerNeoId
                          )
                        "
                      >
                        <p class="m-0 fs-xs wrap-text">{{ event.title }}</p>
                        <i class="ai-arrow-right"></i>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>

              <!-- Tasks list -->
              <div class="events mt-2">
                <ul
                  class="p-0 mb-0"
                  v-for="task in tasksPerDate"
                  :key="task.id"
                >
                  <li class="d-block w-100 mb-0">
                    <div class="w-100 d-flex">
                      <div class="w-25">
                        <div
                          class="bg-danger text-white d-flex border-bottom p-2"
                        >
                          <p class="m-0 flex-1 fs-xs text-center">
                            {{ $t('headers.task') }}
                          </p>
                        </div>
                      </div>
                      <div class="w-75">
                        <div
                          class="
                            bg-gray
                            d-flex
                            align-items-center
                            justify-content-between
                            border-bottom
                            p-2
                          "
                        >
                          <p class="m-0 fs-xs">{{ task.name }}</p>

                          <!-- Completed task label -->
                          <span v-if="task.finished" class="text-danger fs-xs">
                            {{ $t('labels.completion') }}
                          </span>

                          <!-- Delete and complete button -->
                          <div v-else class="d-flex">
                            <button
                              type="button"
                              class="btn btn-link p-0 c-primary icon-xs me-2"
                              @click="handleDeleteTask(task.id)"
                            >
                              <i class="ai-trash fs-lg"></i>
                            </button>
                            <button
                              type="button"
                              class="btn btn-link p-0 c-primary icon-xs"
                              @click="handleCompleteTask(task.id)"
                            >
                              <i class="ai-check fs-lg"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="w-100">
                      <div class="d-flex">
                        <div class="w-25 d-flex align-items-center p-2 border">
                          <p class="m-0 flex-1 fs-xs text-center">
                            {{ task.limit_time.slice(0, -3) }}
                          </p>
                        </div>
                        <div class="w-75 p-2 border">
                          <p class="m-0 fs-xs wrap-text">
                            {{ task.task_title }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import DeleteTaskModal from '../../tasks/components/DeleteModal.vue';
import CompleteTaskModal from '../../tasks/components/CompleteModal.vue';
import ScheduleApiService from '../../../api/schedules/schedule';
import TaskApiService from '../../../api/tasks/task';
import ScheduleGuestStatusesEnums from '../../../enums/ScheduleGuestStatuses';
import ScheduleGuestStatusesDesc from '../../../enums/ScheduleGuestStatusesDescriptions';
import BpheroConfig from '../../../config/bphero';
import i18n from '../../../i18n';
import Common from '../../../common';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'ScheduleListIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    invitations_count: {
      type: Number,
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    DeleteTaskModal,
    CompleteTaskModal,
  },
  setup(props) {
    /**
     * Data properties
     */
    const calendar = ref(null);
    const scheduleApiService = new ScheduleApiService();
    const taskApiService = new TaskApiService();
    const schedules = ref([]);
    const tasks = ref([]);
    const holidays = ref([]);
    const eventsPerDate = ref([]);
    const tasksPerDate = ref([]);
    const pageLoading = ref(false);
    const serviceSelected = ref(props.service);
    const invitationsCount = ref(props.invitations_count);
    const currentSelectedDay = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });

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
     * Auto-scroll to list of events
     *
     * @param {bool} isScroll Defaults to true
     * @returns {void}
     */
    const handleScrollToList = (isScroll = true) => {
      if (isScroll) {
        document.querySelector('.events-list').scrollIntoView();
      }
    };

    /**
     * Get invited service handler
     *
     * @param {object} schedule
     * @return {string}
     */
    const handleGetInvited = (schedule) => {
      if (schedule.owner_rio !== null) {
        return schedule.owner_rio.full_name;
      }

      if (schedule.owner_neo !== null) {
        return schedule.owner_neo.organization_name;
      }

      return '-';
    };

    /**
     * Get scheduled time handler
     *
     * @param {object} schedule
     * @return {string}
     */
    const handleGetScheduledTime = (schedule) => {
      const isOneDayEvent = schedule.start_date === schedule.end_date;
      const isAllDay =
        schedule.start_time === null && schedule.end_time === null;

      if (isAllDay && isOneDayEvent) {
        return i18n.global.t('labels.all_day');
      }

      if (!isAllDay) {
        const startTime = schedule.start_time.slice(0, 5);
        const endTime = schedule.end_time.slice(0, 5);

        return `${startTime} - ${endTime}`;
      }

      return i18n.global.t('labels.all_day');
    };

    /**
     * Get status class name for badge labeling
     *
     * @param {string} status
     * @return {string}
     */
    const handleGetStatusClassname = (status) => {
      switch (status) {
        case ScheduleGuestStatusesEnums.PARTICIPATE:
          return 'bg-success';
        case ScheduleGuestStatusesEnums.NOT_PARTICIPATE:
          return 'bg-dark';
        default:
          return 'bg-secondary';
      }
    };

    /**
     * Add class by date handler
     */
    const handleAddClassByDate = (date) => {
      const dataAttr = Common.constructDate(date, '-').replace(
        /(^|\D)(\d)(?!\d)/g,
        '$10$2'
      );
      const fcDay = document.body.querySelector(
        `.fc-day[data-date="${dataAttr}"]`
      );

      fcDay.classList.add('is_holiday');
    };

    /**
     * Add holidays handler
     */
    const handleAddHolidays = () => {
      calendar.value.addEventSource({
        url: '',
        googleCalendarId: BpheroConfig.GOOGLE_CALENDAR_ID,
        className: 'holiday',
      });
    };

    /**
     * Reset holiday list
     */
    const handleHolidayListReset = () => {
      holidays.value = [];
    };

    /**
     * Display all holidays
     */
    const handleDisplayHolidays = () => {
      const eventsList = calendar.value.getEvents();
      const currentDate = Common.constructDate(currentSelectedDay.value);

      handleHolidayListReset();

      for (let i = 0; i < eventsList.length; i += 1) {
        /* eslint no-underscore-dangle: 0 */
        const eventDef = eventsList[i]._def;

        if (eventDef.publicId !== '') {
          const holiDate = eventDef.publicId.split('_', 2)[0];

          if (holiDate === currentDate) {
            holidays.value.push(eventDef);
          }
        }
      }
    };

    /**
     * Reset schedule and task list events
     */
    const handleScheduleAndTaskListReset = () => {
      eventsPerDate.value = [];
      tasksPerDate.value = [];
    };

    /**
     * Get schedules by selected day
     *
     * @param {object} date
     * @param {bool} isScroll Defaults to true
     */
    const handleGetSchedulesByDay = async (date, isScroll = true) => {
      resetAlert();
      setPageLoading(true);

      await scheduleApiService
        .getSchedulesPerDay(Common.constructDate(date))
        .then((response) => {
          const scheduledEvents = response.data.data;
          const events = [];

          scheduledEvents.forEach((schedule) => {
            const invited = handleGetInvited(schedule);
            const time = handleGetScheduledTime(schedule);
            const statusVal = schedule.status.toString();
            const status = ScheduleGuestStatusesDesc[statusVal];
            const statusClassname = handleGetStatusClassname(statusVal);
            const ownerRioId = schedule.owner_rio_id;
            const ownerNeoId = schedule.owner_neo_id;

            events.push({
              id: schedule.id,
              title: schedule.schedule_title,
              status_classname: statusClassname,
              time,
              status,
              invited,
              ownerRioId,
              ownerNeoId,
            });
          });

          // Render events
          eventsPerDate.value = events;
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.schedules.failed_to_load_schedules'
          );

          setAlert('failed', errorMessage);
          eventsPerDate.value = [];
        })
        .finally(() => {
          handleScrollToList(isScroll);
          setPageLoading(false);
        });
    };

    /**
     * Get tasks by selected day
     *
     * @param {object} date
     * @param {bool} isScroll Defaults to true
     */
    const handleGetTasksByDay = async (date, isScroll = true) => {
      setPageLoading(true);

      await taskApiService
        .getTasksPerDay(Common.constructDate(date))
        .then((response) => {
          tasksPerDate.value = response.data.data;
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.schedules.failed_to_load_schedules'
          );

          setAlert('failed', errorMessage);
          tasksPerDate.value = [];
        })
        .finally(() => {
          handleScrollToList(isScroll);
          setPageLoading(false);
        });
    };

    /**
     * Get schedules by selected month
     *
     * @param {object} date
     */
    const handleGetSchedulesByMonth = async (date) => {
      resetAlert();
      setPageLoading(true);

      await scheduleApiService
        .getSchedulesPerMonth(Common.constructDate(date))
        .then((response) => {
          schedules.value = response.data.data;

          // Create calendar events
          schedules.value.forEach((schedule) => {
            calendar.value.addEvent({
              title: schedule.title,
              start: schedule.start_date,
              end: schedule.end_date,
              className: 'event',
            });
          });
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.schedules.failed_to_load_schedules'
          );

          setAlert('failed', errorMessage);
          schedules.value = [];
        });
    };

    /**
     * Get tasks by selected month
     *
     * @param {object} date
     */
    const handleGetTasksByMonth = async (date) => {
      setPageLoading(true);

      await taskApiService
        .getTasksPerMonth(Common.constructDate(date))
        .then((response) => {
          tasks.value = response.data.data;

          // Create calendar events for tasks
          tasks.value.forEach((task) => {
            calendar.value.addEvent({
              title: task.task_title,
              start: task.limit_date,
              end: task.limit_date,
              className: 'event',
            });
          });
        })
        .catch(() => {
          const errorMessage = i18n.global.t(
            'messages.schedules.failed_to_load_schedules'
          );

          setAlert('failed', errorMessage);
          tasks.value = [];
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Delete task handler
     *
     * @param {integer} id
     * @returns {void}
     */
    const handleDeleteTask = (id) => {
      /* eslint no-undef: 0 */
      const deleteModalNode = document.getElementById('delete-task-modal');
      const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));

      // Inject record id to modal
      const field = deleteModalNode.querySelector('input[name=id]');
      field.value = id;
      deleteModal.value.show();
    };

    /**
     * Mark task as completed handler
     *
     * @param {integer} id
     * @returns {void}
     */
    const handleCompleteTask = (id) => {
      /* eslint no-undef: 0 */
      const completeModalNode = document.getElementById('complete-task-modal');
      const completeModal = computed(
        () => new bootstrap.Modal(completeModalNode)
      );

      // Inject record id to modal
      const field = completeModalNode.querySelector('input[name=id]');
      field.value = id;
      completeModal.value.show();
    };

    /**
     * Reload task list section
     */
    const handleReloadTaskList = () => {
      handleGetTasksByDay(currentSelectedDay.value);
    };

    /**
     * Delete all existing calendar events handler
     */
    const handleDeleteAllExistingEvents = () => {
      const existingEvents = calendar.value.getEventSources();
      const { length } = existingEvents;

      for (let i = 0; i < length; i += 1) {
        existingEvents[i].remove();
      }
    };

    /**
     * Initialize & render calendar
     */
    const handleCalendarInitialization = () => {
      const calendarEl = document.getElementById('calendar');
      const locale = BpheroConfig.LOCALE === 'jp' ? 'ja' : BpheroConfig.LOCALE;
      // aspectRatio: 0.8 smartphones | 1.35 pc; default ratio if contentHeight or aspectRatio is not present
      const mobileWidth = 821;
      const mobileAspectRatio = 0.8;
      const defaultAspectRatio = 1.35;
      const calendarRatio =
        $(window).width() < mobileWidth
          ? mobileAspectRatio
          : defaultAspectRatio;

      /* eslint no-undef: 0 */
      calendar.value = new FullCalendar.Calendar(calendarEl, {
        googleCalendarApiKey: BpheroConfig.GOOGLE_CALENDAR_API_KEY,
        initialView: 'dayGridMonth',
        nextDayThreshold: '00:00:00',
        aspectRatio: calendarRatio,
        locale,
        eventSources: [
          {
            url: null,
            googleCalendarId: BpheroConfig.GOOGLE_CALENDAR_ID,
            className: 'holiday',
          },
        ],
        customButtons: {
          create: {
            text: ``,
            icon: ' ai-plus',
            click() {
              window.location.href = '/schedules/create';
            },
          },
          invite: {
            text: ``,
            icon: ' ai-clipboard',
            click() {
              window.location.href = '/schedules/notifications';
            },
          },
        },
        headerToolbar: {
          left: 'prev today next',
          center: 'title',
          right: 'invite create',
        },
        windowResize() {
          // when window size is changed in pc or if view rotated to landscape in sp
          if ($(window).width() < mobileWidth) {
            calendar.value.setOption('aspectRatio', mobileAspectRatio);
          } else {
            calendar.value.setOption('aspectRatio', defaultAspectRatio);
          }
        },
        dayCellDidMount(info) {
          if (locale === 'ja') {
            // hide the original japanese day
            const originElement = info.el.querySelectorAll(
              '.fc-daygrid-day-number'
            );
            originElement.forEach((e) => {
              const forReplace = e.innerHTML;

              // insert only day number
              const targetElement = e.closest('.fc-daygrid-day-top');
              targetElement.classList.add('fc-daygrid-day-number');
              targetElement.innerHTML = forReplace.slice(0, -1);
              e.classList.add('d-none');
            });
          }
        },
        eventDataTransform(eventData) {
          handleAddClassByDate(new Date(eventData.start));
        },
        dateClick() {
          // Reset holiday list
          handleHolidayListReset();

          // Reset events list
          handleScheduleAndTaskListReset();
        },
        eventClick(eventObject) {
          /* eslint no-param-reassign: "error" */
          eventObject.jsEvent.cancelBubble = true;
          eventObject.jsEvent.preventDefault();

          // Reset events list
          handleScheduleAndTaskListReset();

          const { el } = eventObject;
          const classname = el.className;
          const td = el.closest('td.fc-day');
          currentSelectedDay.value = eventObject.event.start;

          if (classname.includes('event')) {
            handleGetSchedulesByDay(currentSelectedDay.value);
            handleGetTasksByDay(currentSelectedDay.value);
          }

          if (td.className.includes('is_holiday')) {
            handleDisplayHolidays();
          } else {
            handleHolidayListReset();
          }
        },
      });

      // Render calendar
      calendar.value.render();
    };

    /**
     * Get schedules on change month
     */
    const handleOnChangeCalendarMonth = () => {
      const prevButton = document.querySelector('button.fc-prev-button');
      const nextButton = document.querySelector('button.fc-next-button');

      prevButton.addEventListener('click', (event) => {
        event.preventDefault();

        handleHolidayListReset();
        handleScheduleAndTaskListReset();
        handleDeleteAllExistingEvents();
        handleAddHolidays();
        handleGetSchedulesByMonth(calendar.value.getDate());
        handleGetTasksByMonth(calendar.value.getDate());
      });
      nextButton.addEventListener('click', (event) => {
        event.preventDefault();

        handleHolidayListReset();
        handleScheduleAndTaskListReset();
        handleDeleteAllExistingEvents();
        handleAddHolidays();
        handleGetSchedulesByMonth(calendar.value.getDate());
        handleGetTasksByMonth(calendar.value.getDate());
      });
    };

    /**
     * Display invitation notification badge on load
     */
    const handleAppendInvitationNotificationBadge = () => {
      if (invitationsCount.value > 0) {
        const notifButtonEl = document.querySelector(
          'span.fc-icon.fc-icon-.ai-clipboard'
        );

        notifButtonEl.insertAdjacentHTML(
          'afterEnd',
          `<span class="invitation-badge badge notification-badge bg-danger">
            ${invitationsCount.value}
          </span>`
        );
      }
    };

    /**
     * Close holidays on button click
     */
    const handleCloseHolidays = (event) => {
      const el = event.srcElement;
      el.parentElement.parentElement.remove();
    };

    /**
     * Redirect page to schedule detail
     *
     * @param {integer} id
     * @return {void}
     */
    const handleRedirectionToDetail = (id, rioId, neoId) => {
      if (
        serviceSelected.value.type === ServiceSelectionTypes.RIO &&
        serviceSelected.value.data.id === rioId
      ) {
        window.location.href = `/schedules/${id}/edit`;
      } else if (
        serviceSelected.value.type === ServiceSelectionTypes.NEO &&
        serviceSelected.value.data.id === neoId &&
        !serviceSelected.value.data.is_member
      ) {
        window.location.href = `/schedules/${id}/edit`;
      } else {
        window.location.href = `/schedules/${id}`;
      }
    };

    /**
     * Redirect page to schedule export
     *
     * @param {integer} id
     * @return {void}
     */
    const handleRedirectionToExport = () => {
      window.location.href = `/schedules/export`;
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      // Set current selected day on load
      currentSelectedDay.value = new Date();

      handleCalendarInitialization();
      handleDisplayHolidays();
      handleGetSchedulesByMonth(currentSelectedDay.value);
      handleGetTasksByMonth(currentSelectedDay.value);
      handleGetSchedulesByDay(currentSelectedDay.value, false);
      handleGetTasksByDay(currentSelectedDay.value, false);
      handleOnChangeCalendarMonth();
      handleAppendInvitationNotificationBadge();
    });

    return {
      holidays,
      pageLoading,
      alert,
      setAlert,
      resetAlert,
      eventsPerDate,
      tasksPerDate,
      handleDeleteTask,
      handleCompleteTask,
      handleReloadTaskList,
      handleRedirectionToDetail,
      serviceSelected,
      ServiceSelectionTypes,
      handleRedirectionToExport,
      handleCloseHolidays,
    };
  },
});
</script>
