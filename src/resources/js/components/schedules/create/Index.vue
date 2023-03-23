<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Invitation modal -->
    <invitation-modal
      :connection_list="connectionList"
      :member_list="memberList"
      :merged_list="mergedList"
      :guest_list="guestList"
      :current_guests="[]"
      :selected="[]"
      :is_current_owner="false"
      :service="service"
      @handleGuestsSelected="handleGuestsSelected"
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
          <!-- Form component -->
          <form-component
            :schedule="scheduleData"
            :rio="rio"
            :edit="false"
            :time_selection="time_selection"
            :service_selection="service_selection"
            :selected_guests_lists="selectedGuestsList"
            :current_issuer="currentIssuer"
            :current_guests="[]"
            :is_current_owner="false"
            :member_list="memberList"
            :service="service"
            @page-loading="setPageLoading"
            @set-alert="setAlert"
            @reset-alert="resetAlert"
            @handleGuestsSelected="handleGuestsSelected"
            @handleUpdateIssuer="handleUpdateIssuer"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import InvitationModal from '../components/InvitationModal.vue';
import FormComponent from '../components/Form.vue';
import ScheduleApiService from '../../../api/schedules/schedule';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'ScheduleListCreate',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
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
    connected_list: {
      type: [Array, Object],
      required: true,
    },
    member_list: {
      type: [Array, Object],
      required: true,
    },
    merged_list: {
      type: [Array, Object],
      required: true,
    },
    owner: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    InvitationModal,
    FormComponent,
  },
  setup(props) {
    /**
     * Data properties
     */
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const scheduleApiService = new ScheduleApiService();
    const connectionList = ref(props.connected_list);
    const memberList = ref(props.member_list);
    const mergedList = ref(props.merged_list);
    const selectedGuestsList = ref([]);
    const defaultImage = ref([]);
    const currentIssuer = ref(props.owner);
    const guestList = ref('unselected');
    const scheduleData = ref({
      schedule_title: '',
      start_date: '',
      end_date: '',
      start_time: '',
      end_time: '',
      calendar: '',
      caption: '',
      all_day: true,
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

    const handleGuestsSelected = (data) => {
      selectedGuestsList.value = data;
    };

    const handleUpdateIssuer = async (data) => {
      await scheduleApiService
        .updateConnectionList(data)
        .then((response) => {
          connectionList.value = response.data.data.guests;
          mergedList.value = response.data.data.mergedList;
          memberList.value = response.data.data.members;
          currentIssuer.value = response.data.data.issuer;
          selectedGuestsList.value = [];
          guestList.value =
            guestList.value === 'unselected' ? 'select' : 'unselected';
        })
        .catch(() => {
          setAlert('failed');
          setPageLoading(false);
        });
    };

    onMounted(() => {
      if (currentIssuer.value.type === ServiceSelectionTypes.RIO) {
        scheduleData.value.calendar = `rio_${currentIssuer.value.id}`;
      } else {
        scheduleData.value.calendar = `neo_${currentIssuer.value.id}`;
      }
    });

    return {
      pageLoading,
      alert,
      setAlert,
      resetAlert,
      setPageLoading,
      handleUpdateIssuer,
      connectionList,
      handleGuestsSelected,
      selectedGuestsList,
      defaultImage,
      currentIssuer,
      guestList,
      scheduleData,
      memberList,
      mergedList,
    };
  },
});
</script>
