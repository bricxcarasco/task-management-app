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
      :current_guests="current_guests"
      :selected="selected"
      :is_current_owner="isCurrentOwner"
      :update_selected="updateSelected"
      :service="service"
      @handleGuestsSelected="handleGuestsSelected"
    />

    <delete-schedule-modal :schedule="schedule" />

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
            :schedule="schedule"
            :rio="rio"
            :edit="true"
            :time_selection="time_selection"
            :service_selection="service_selection"
            :selected_guests_lists="selectedGuestsList"
            :current_issuer="currentIssuer"
            :current_guests="current_guests"
            :is_current_owner="isCurrentOwner"
            :member_list="member_list"
            :service="service"
            @page-loading="setPageLoading"
            @set-alert="setAlert"
            @reset-alert="resetAlert"
            @handleUpdateIssuer="handleUpdateIssuer"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import InvitationModal from '../components/InvitationModal.vue';
import FormComponent from '../components/Form.vue';
import ScheduleApiService from '../../../api/schedules/schedule';
import DeleteScheduleModal from './modals/DeleteModal.vue';

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
      required: true,
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
      required: false,
    },
    merged_list: {
      type: [Array, Object],
      required: false,
    },
    owner: {
      type: [Array, Object],
      required: true,
    },
    current_guests: {
      type: [Array, Object],
      required: true,
    },
    selected: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    InvitationModal,
    FormComponent,
    DeleteScheduleModal,
  },
  setup(props, { emit }) {
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
    const isCurrentOwner = ref(true);
    const saveData = ref([]);
    const updateSelected = ref([]);

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

    const handleUpdateIssuer = async (data, save) => {
      await scheduleApiService
        .updateGuestList(data)
        .then((response) => {
          connectionList.value = response.data.data.guests;
          mergedList.value = response.data.data.mergedList;
          memberList.value = response.data.data.members;
          currentIssuer.value = response.data.data.issuer;
          selectedGuestsList.value = [];
          updateSelected.value = response.data.data.selected;
          guestList.value =
            guestList.value === 'unselected' ? 'select' : 'unselected';
          isCurrentOwner.value = false;

          if (save.length > 0) {
            saveData.value = save;
          }

          if (response.data.data.isCurrentOwner) {
            isCurrentOwner.value = response.data.data.isCurrentOwner;
          }
        })
        .catch(() => {
          emit('set-alert', 'failed');
          emit('page-loading', false);
        });
    };

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
      isCurrentOwner,
      saveData,
      updateSelected,
      memberList,
      mergedList,
    };
  },
});
</script>
