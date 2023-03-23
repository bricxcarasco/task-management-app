<template>
  <div>
    <!-- Invite Modal -->
    <invite-modal
      ref="inviteModalRef"
      :user="targetInvitee"
      :group_id="group.id"
      @reset-list="getUsers"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Invite Modal -->
    <delete-invite-modal
      ref="deleteInviteModalRef"
      :invite_id="targetDeleteInvite"
      @reset-list="getUsers"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
      <!-- <page-loader :show="pageLoading" /> -->
      <alert
        :success="alert.success"
        :danger="alert.failed"
        :message="alert.message"
        @closeAlert="resetAlert"
      />

      <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
          <div class="d-flex flex-column h-100 rounded-3">
            <slot name="header" />

            <!-- Member Invitation Search Form -->
            <div class="position-relative mb-2">
              <form action="" @submit.prevent="submitSearch" novalidate>
                <p class="mb-2">
                  {{ $t('headers.connection_group_invitation_search') }}
                </p>
                <div class="input-group mb-4">
                  <input
                    v-model="searchFormInput.name"
                    class="form-control text-center"
                    type="text"
                    name="name"
                    :placeholder="
                      this.$i18n.t('placeholders.search_connections')
                    "
                  />
                  <button class="btn btn-translucent-dark" type="submit">
                    {{ $t('buttons.search') }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Datalist Information -->
            <div class="position-relative mb-2">
              <p class="mb-2">{{ $t('headers.search_results') }}</p>
              <div
                class="d-flex justify-content-between align-items-center mt-2"
              >
                <p class="mb-0" style="flex: 1">
                  {{ totalResults }}{{ $t('links.total_suffix') }}
                  <span class="js-member-list-count"></span>
                </p>
              </div>
            </div>

            <!-- Member Invitation Listing -->
            <div class="card p-4 shadow mt-2 mb-3">
              <section-loader :show="listLoading" />

              <div class="connection__wrapper">
                <ul
                  v-if="users.length"
                  class="connection__lists list-group list-group-flush mt-2"
                >
                  <!-- Connect RIO List -->
                  <user-item
                    v-for="(user, index) in users"
                    :key="index"
                    :user="user"
                    :full_group="isGroupFull"
                    @invite-user="inviteUser"
                    @cancel-invite="cancelInvite"
                  />
                </ul>
                <ul
                  v-else
                  class="connection__lists list-group list-group-flush mt-2"
                >
                  <li
                    class="
                      list-group-item
                      px-0
                      py-2
                      position-relative
                      list--white
                      px-2
                      text-center
                    "
                  >
                    {{ $t('messages.connection_group.empty_connected_rio') }}
                  </li>
                </ul>
              </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-1 mb-3">
              <pagination :meta="paginationData" @changePage="changePage" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import InviteModal from './InviteModal.vue';
import DeleteInviteModal from './DeleteInviteModal.vue';
import ApiService from '../../../api/connection_group/invite-member';
import UserItem from './Item.vue';
import Alert from '../../base/BaseAlert.vue';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'InviteMembersSection',
  props: {
    rio: {
      type: Object,
      required: true,
    },
    group: {
      type: Object,
      required: true,
    },
  },
  components: {
    InviteModal,
    DeleteInviteModal,
    UserItem,
    Alert,
    Pagination,
    SectionLoader,
  },
  setup(props) {
    const apiService = new ApiService();
    const listLoading = ref(false);
    const users = ref([]);
    const paginationData = ref([]);
    const searchFormInput = ref({});
    const searchData = ref({});
    const inviteModalRef = ref(null);
    const deleteInviteModalRef = ref(null);
    const targetInvitee = ref(null);
    const targetDeleteInvite = ref(null);
    const totalResults = ref(0);
    const isGroupFull = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });

    /**
     * Reset Alert
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
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
     * Get list of industries
     *
     * @returns {void}
     */
    const getUsers = async () => {
      listLoading.value = true;

      try {
        const getUsersApi = await apiService.getRioConnectedUsers(
          props.group.id,
          searchData.value
        );
        const getUserResponseData = getUsersApi.data;
        users.value = getUserResponseData?.data || [];
        paginationData.value = getUserResponseData?.meta || [];
        totalResults.value = getUserResponseData?.meta?.total || 0;
        isGroupFull.value = getUserResponseData?.meta?.is_full || false;

        // Handle out of bounds page
        if (users.value.length === 0 && totalResults.value > 0) {
          searchData.value.page = null;
          getUsers();
        }
      } catch (error) {
        resetAlert();
        setAlert('failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    /**
     * Get list of industries
     *
     * @returns {void}
     */
    const submitSearch = () => {
      searchData.value = { ...searchFormInput.value };
      searchData.value.page = null;
      getUsers();
    };

    /**
     * Get list of industries
     *
     * @returns {void}
     */
    const changePage = (page) => {
      searchData.value.page = page;
      getUsers();
    };

    /**
     * Setup invite modal
     *
     * @returns {void}
     */
    const inviteUser = (user) => {
      targetInvitee.value = user;
      inviteModalRef.value.modal.show();
    };

    /**
     * Setup cancel invite
     *
     * @returns {void}
     */
    const cancelInvite = (invite) => {
      targetDeleteInvite.value = invite;
      deleteInviteModalRef.value.modal.show();
    };

    getUsers();

    return {
      alert,
      setAlert,
      resetAlert,
      listLoading,
      users,
      paginationData,
      searchFormInput,
      searchData,
      inviteModalRef,
      deleteInviteModalRef,
      targetInvitee,
      targetDeleteInvite,
      totalResults,
      inviteUser,
      cancelInvite,
      submitSearch,
      changePage,
      getUsers,
      isGroupFull,
    };
  },
};
</script>
