<template>
  <div
    class="modal fade"
    id="delete-schedule-modal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title"></h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 text-center">
              {{ $t('messages.rio.delete_confirmation') }}
              <input type="hidden" name="id" />
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button
            @click="handleDeleteSchedule"
            class="btn btn-danger btn-shadow btn-sm"
            type="button"
          >
            {{ $t('buttons.delete') }}
          </button>
          <button
            class="btn btn-secondary btn-shadow btn-sm"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            {{ $t('buttons.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { defineComponent, ref } from 'vue';
import ScheduleApiService from '../../../../api/schedules/schedule';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default defineComponent({
  name: 'DeleteScheduleModalComponent',
  props: {
    schedule: {
      type: [Array, Object],
      required: false,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const scheduleApiService = new ScheduleApiService();
    const schedule = ref(props.schedule);
    const modalLoading = ref(false);

    const handleRedirectionToList = () => {
      window.location.href = `/schedules`;
    };

    const handleDeleteSchedule = async () => {
      modalLoading.value = true;

      await scheduleApiService
        .deleteSchedule(schedule.value.id)
        .then(() => {
          handleRedirectionToList();
        })
        .catch(() => {
          emit('set-alert', 'failed');
          emit('page-loading', false);
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    return {
      handleDeleteSchedule,
      handleRedirectionToList,
      modalLoading,
    };
  },
});
</script>
