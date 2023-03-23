<template>
  <div>
    <form action="" @submit="handleFormSubmission" ref="formRef" novalidate>
      <div
        class="
          d-flex
          align-items-center
          justify-content-between
          mb-0 mb-md-2
          position-relative
          border-bottom
          pb-2
        "
      >
        <button
          type="button"
          class="btn btn--link"
          @click="handleRedirectionToList"
        >
          {{ $t('buttons.cancel') }}
        </button>
        <div v-if="edit" class="text-end">
          <button type="submit" class="btn btn-primary">
            {{ $t('buttons.save') }}
          </button>
          <button
            type="button"
            class="btn btn-danger mx-1"
            data-bs-toggle="modal"
            data-bs-target="#delete-schedule-modal"
          >
            {{ $t('buttons.delete') }}
          </button>
        </div>
        <div v-else>
          <button type="submit" class="btn btn-primary">
            {{ $t('buttons.save') }}
          </button>
        </div>
      </div>
      <div class="mt-2">
        <!-- <div class="text-end" v-if="edit">
          <a href="#" class="btn btn-link">Googleカレンダーに登録</a>
        </div> -->
        <div class="mb-3">
          <label for="normal-input" class="form-label">
            ■{{ $t('labels.title') }}
            <sup class="text-danger ms-1">*</sup>
          </label>
          <input
            v-model="formData.schedule_title"
            class="form-control"
            :class="errors?.schedule_title != null ? 'is-invalid' : ''"
            type="text"
            placeholder=""
          />
          <base-validation-error :errors="errors?.schedule_title" />
        </div>
        <div class="mb-3">
          <label for="normal-input" class="form-label">
            ■{{ $t('labels.datetime') }}
            <sup class="text-danger ms-1">*</sup>
            <div class="mt-3">
              <input
                v-model="formData.all_day"
                type="checkbox"
                class="form-check-input"
                id="exampleCheck1"
                :checked="
                  formData.start_time ? false : formData.end_time ? false : true
                "
              /><label class="ms-1">{{ $t('labels.all_day') }}</label>
            </div>
          </label>
          <div class="row">
            <div
              class="
                col-6
                d-flex
                align-items-center
                justify-content-between
                pe-0
              "
            >
              <label for="text-input" class="form-label ms-0 me-2">
                {{ $t('labels.start') }}
              </label>
              <div class="datetime-container">
                <input
                  v-model="formData.start_date"
                  class="form-control datepicker_ymd_start rounded"
                  :class="errors?.start_date != null ? 'is-invalid' : ''"
                  type="text"
                />
                <base-validation-error :errors="errors?.start_date" />
              </div>
            </div>
            <div
              class="
                col-6
                d-flex
                align-items-center
                justify-content-between
                ps-0
              "
            >
              <label for="text-input" class="form-label ms-0 me-2">
                {{ $t('labels.time') }}
              </label>
              <div class="datetime-container">
                <select
                  v-model="formData.start_time"
                  class="form-select"
                  :class="errors?.start_time != null ? 'is-invalid' : ''"
                >
                  <option value="">--:--</option>
                  <option
                    v-for="(time, index) in time_selection"
                    :key="index"
                    :value="index"
                  >
                    {{ time }}
                  </option>
                </select>
                <base-validation-error :errors="errors?.start_time" />
              </div>
            </div>
          </div>
          <div class="row mt-2">
            <div
              class="
                col-6
                d-flex
                align-items-center
                justify-content-between
                pe-0
              "
            >
              <label for="text-input" class="form-label ms-0 me-2">
                {{ $t('labels.end') }}
              </label>
              <div class="datetime-container">
                <input
                  v-model="formData.end_date"
                  class="form-control datepicker_ymd_end rounded"
                  :class="errors?.end_date != null ? 'is-invalid' : ''"
                  type="text"
                />
                <base-validation-error :errors="errors?.end_date" />
              </div>
            </div>
            <div
              class="
                col-6
                d-flex
                align-items-center
                justify-content-between
                ps-0
              "
            >
              <label for="text-input" class="form-label ms-0 me-2">
                {{ $t('labels.time') }}
              </label>
              <div class="datetime-container">
                <select
                  v-model="formData.end_time"
                  class="form-select"
                  :class="errors?.end_time != null ? 'is-invalid' : ''"
                >
                  <option value="">--:--</option>
                  <option
                    v-for="(time, index) in time_selection"
                    :key="index"
                    :value="index"
                  >
                    {{ time }}
                  </option>
                </select>
                <base-validation-error :errors="errors?.end_time" />
              </div>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label for="normal-input" class="form-label">
            ■{{ $t('labels.calendar_to_register') }}
            <sup class="text-danger ms-1">*</sup>
          </label>
          <select
            @change="handleUpdateIssuer"
            v-model="formData.calendar"
            class="form-select"
            :class="errors?.calendar != null ? 'is-invalid' : ''"
          >
            <option
              v-for="(service, index) in service_selection"
              :key="index"
              :value="index"
            >
              {{ service }}
            </option>
          </select>
          <base-validation-error :errors="errors?.calendar" />
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
          <p class="mb-0 pe-2">■{{ $t('labels.participants') }}</p>
          <button
            type="button"
            class="btn btn-link"
            data-bs-toggle="modal"
            data-bs-target="#schedule-invitation-modal"
          >
            <i class="me-2 ai-plus"></i>
            {{ $t('buttons.invite') }}
          </button>
        </div>
        <div class="card p-4 shadow mt-2 mb-3">
          <div class="connection__wrapper">
            <ul class="connection__lists list-group list-group-flush mt-2">
              <li
                v-if="!isCurrentOwner"
                class="
                  d-flex
                  justify-content-between
                  align-items-center
                  list-group-item
                  position-relative
                  list--white
                  px-2
                "
              >
                <div>
                  <img
                    class="
                      rounded-circle
                      me-2
                      d-inline-block
                      img--profile_image_sm
                    "
                    :src="scheduleOwner.profile_image"
                    @error="
                      Common.handleNotFoundImageException(
                        $event,
                        DefaultImageCategory.RIO_NEO
                      )
                    "
                    alt="profile photo"
                    width="40"
                  />
                  <span class="fs-xs c-primary ms-2">
                    {{ scheduleOwner.name }}
                  </span>
                </div>
                <div
                  class="
                    vertical-right
                    d-flex
                    align-items-center
                    justify-content-center
                  "
                >
                  <a class="fs-xs btn btn-link p-2">
                    {{ $t('labels.owner') }}
                  </a>
                </div>
              </li>
              <li
                v-for="(list, index) in lists"
                :key="index"
                :value="index"
                class="
                  d-flex
                  justify-content-between
                  align-items-center
                  list-group-item
                  px-0
                  py-2
                  position-relative
                  list--white
                  px-2
                "
              >
                <div>
                  <img
                    class="
                      rounded-circle
                      me-2
                      d-inline-block
                      img--profile_image_sm
                    "
                    :src="list.profile_photo ?? list.profile_picture"
                    @error="
                      Common.handleNotFoundImageException(
                        $event,
                        DefaultImageCategory.RIO_NEO
                      )
                    "
                    alt="profile photo"
                    width="40"
                  />
                  <span class="fs-xs c-primary ms-2">{{ list.name }}</span>
                </div>
                <div
                  class="
                    vertical-right
                    d-flex
                    align-items-center
                    justify-content-center
                  "
                >
                  <a
                    v-if="
                      (scheduleOwner.type === ServiceSelectionTypes.RIO &&
                        scheduleOwner.id === list.rio_id) ||
                      (scheduleOwner.type === ServiceSelectionTypes.NEO &&
                        scheduleOwner.id === list.neo_id)
                    "
                    class="fs-xs btn btn-link no-hover p-2"
                  >
                    {{ $t('labels.owner') }}
                  </a>
                  <a v-else class="fs-xs btn btn-link no-hover p-2">
                    {{ list.equivalent_status }}
                  </a>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <!--<div class="mb-3" v-if="edit">
          <label for="normal-input" class="form-label"
            >■{{ $t('labels.issue_video_conference_URL') }}</label
          >
          <div class="position-relative upload__issue">
            <span
              class="text-muted fs-sm fw-normal ms-auto badge bg-primary px-3"
              >{{ $t('labels.issue') }}</span
            >
            <input
              class="form-control"
              type="text"
              id="normal-input"
              v-model="formData.meeting_url"
              placeholder="https://~~~~"
              :class="errors?.meeting_url != null ? 'is-invalid' : ''"
            />
            <a href="#" class="btn btn--link fs-xs">コピー</a>
            <span
              v-for="(error, index) in errors?.meeting_url"
              :key="index"
              class="invalid-feedback"
              >{{ error }}</span
            >
          </div>
        </div> -->
        <div class="mb-3">
          <label for="normal-input" class="form-label">
            ■{{ $t('labels.description') }}
          </label>
          <textarea
            v-model="formData.caption"
            class="form-control"
            :class="errors?.caption != null ? 'is-invalid' : ''"
            placeholder="全社MTGはこちらのURLで行います。https:// ~~"
            rows="5"
          ></textarea>
          <base-validation-error :errors="errors?.caption" />
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { defineComponent, ref, watch, computed, onMounted } from 'vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import ScheduleApiService from '../../../api/schedules/schedule';
import BpheroConfig from '../../../config/bphero';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import i18n from '../../../i18n';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'ScheduleFormComponent',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    schedule: {
      type: [Array, Object],
      required: false,
    },
    time_selection: {
      type: [Array, Object],
      required: true,
    },
    service_selection: {
      type: [Array, Object],
      required: true,
    },
    current_issuer: {
      type: [Array, Object],
      required: true,
    },
    selected_guests_lists: {
      type: [Array, Object],
      required: true,
    },
    current_guests: {
      type: [Array, Object],
      required: true,
    },
    member_list: {
      type: [Array, Object],
      required: false,
    },
    is_current_owner: {
      type: Boolean,
      required: true,
    },
    edit: {
      type: Boolean,
      required: true,
    },
    service: {
      type: Object,
      required: false,
    },
  },
  emits: ['set-alert', 'reset-alert', 'page-loading', 'handleUpdateIssuer'],
  components: {
    BaseValidationError,
  },
  setup(props, { emit }) {
    /**
     * Data properties
     */
    const scheduleApiService = new ScheduleApiService();
    const schedule = ref(props.schedule);
    const rio = ref(props.rio);
    const errors = ref(null);
    const formRef = ref({});
    const formData = ref(props.schedule);
    const scheduleOwner = ref(props.current_issuer);
    const lists = ref(props.current_guests);
    const saveLists = ref(props.current_guests);
    const initialIssuer = ref(null);
    const isCurrentOwner = ref(props.is_current_owner);
    const forEdit = ref(props.edit);

    /**
     * Watch for guests changes
     */
    watch(
      () => props.selected_guests_lists,
      (newValue) => {
        if (!newValue) {
          lists.value = null;
        } else {
          lists.value = newValue;
        }
      }
    );

    watch(
      () => props.is_current_owner,
      () => {
        if (props.is_current_owner) {
          lists.value = saveLists.value;
          isCurrentOwner.value = true;
        } else {
          isCurrentOwner.value = false;
        }
      }
    );

    /**
     * Watch for current issuer changes
     */
    watch(
      () => props.current_issuer,
      (newValue) => {
        scheduleOwner.value = newValue;
      }
    );

    /**
     * Watch for all day checkbox changes
     */
    watch(
      () => formData.value.all_day,
      (newValue) => {
        if (newValue) {
          formData.value.start_time = '';
          formData.value.end_time = '';
        }
      }
    );

    /**
     * Watch for start_time and end_time changes
     */
    watch(
      () => [formData.value.start_time, formData.value.end_time],
      ([startTime]) => {
        if (startTime) {
          formData.value.all_day = false;
        } else {
          formData.value.all_day = true;
        }
      }
    );

    /**
     * Watch for guest list changes
     */
    watch(
      () => props.guestList,
      (newValue) => {
        scheduleOwner.value = newValue;
      }
    );

    /**
     * Computed properties
     */
    const defaultImage = computed(() => BpheroConfig.DEFAULT_IMG);

    /**
     * Event listener for updating issuer
     *
     * @returns {void}
     */
    const handleUpdateIssuer = async () => {
      if (lists.length > 0) {
        saveLists.value = lists.value;
      }
      const data = { ...formData.value };
      const [selectedService, id] = data.calendar.split('_', 2);

      // Set owner rio & neo IDs
      if (selectedService === 'rio') {
        data.owner_rio_id = parseInt(id, 10);
        data.owner_neo_id = null;
      } else {
        data.owner_neo_id = parseInt(id, 10);
        data.owner_rio_id = null;
      }

      emit('handleUpdateIssuer', data, saveLists.value);
    };

    /**
     * Check if form is in edit state
     *
     * @returns {bool}
     */

    const isEdit = () => forEdit.value !== false;

    /**
     * Redirect page to schedule list
     *
     * @return {void}
     */
    const handleRedirectionToList = () => {
      window.location.href = `/schedules`;
    };

    /**
     * Event listener for add url form submit
     *
     * @returns {void}
     */
    const handleFormSubmission = (event) => {
      event.preventDefault();

      // Reinitialize state
      errors.value = null;
      emit('reset-alert');
      emit('page-loading', true);

      const data = { ...formData.value };
      const [selectedService, id] = data.calendar.split('_', 2);
      data.guests = lists.value;

      // Set time to null if empty string
      data.start_time = data.start_time !== '' ? data.start_time : null;
      data.end_time = data.end_time !== '' ? data.end_time : null;

      // Set owner rio & neo IDs
      if (selectedService === 'rio') {
        data.owner_rio_id = parseInt(id, 10);
        data.owner_neo_id = null;
      } else {
        data.owner_neo_id = parseInt(id, 10);
        data.owner_rio_id = null;
      }

      // Execute API call
      const apiCall = isEdit()
        ? scheduleApiService.editSchedule(schedule.value.id, data)
        : scheduleApiService.addSchedule(data);

      // Handle responses
      apiCall
        .then(() => {
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.schedules.schedule_has_been_saved')
          );
          handleRedirectionToList();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Render validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            emit('page-loading', false);

            return;
          }

          emit('set-alert', 'failed');
          emit('page-loading', false);
        });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      if (!isEdit()) {
        const dateStartEl = document.querySelector(
          'input.datepicker_ymd_start'
        );
        const dateEndEl = document.querySelector('input.datepicker_ymd_end');
        Common.setFlatpickrDateRange(dateStartEl, new Date());
        Common.setFlatpickrDateRange(dateEndEl, new Date());
        formData.value.calendar = `rio_${rio.value.id}`;
      }

      if (scheduleOwner.value.type === ServiceSelectionTypes.RIO) {
        formData.value.calendar = `rio_${scheduleOwner.value.id}`;
      } else {
        formData.value.calendar = `neo_${scheduleOwner.value.id}`;
      }
    });

    return {
      formRef,
      formData,
      errors,
      handleFormSubmission,
      handleRedirectionToList,
      scheduleOwner,
      handleUpdateIssuer,
      lists,
      defaultImage,
      initialIssuer,
      saveLists,
      ServiceSelectionTypes,
      isCurrentOwner,
      forEdit,
      isEdit,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
