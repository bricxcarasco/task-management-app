<template>
  <div>
    <set-administrator-modal
      ref="setAdministratorModalRef"
      :data="currentRio"
      @setAdministrator="setRemoveAdministrator"
    />
    <remove-administrator-modal
      ref="removeAdministratorModalRef"
      :data="currentRio"
      @removeAdministrator="setRemoveAdministrator"
    />
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />
    <div
      class="
        d-flex
        align-items-center
        justify-content-center
        d-none d-md-flex
        mb-0 mb-md-2
        position-relative
      "
    >
      <a :href="neo_profile_link" class="btn btn-secondary btn--back">
        <i class="ai-arrow-left"></i>
      </a>
      <h3>
        {{ $t('headers.administrator_management') }}
      </h3>
    </div>
    <div class="d-flex flex-column bg-light rounded-3 shadow-lg p-2">
      <div class="border-bottom position-relative d-block d-md-none">
        <a :href="neo_profile_link" class="btn btn-secondary btn--back">
          <i class="ai-arrow-left"></i>
        </a>
        <h3 class="p-3 mb-0 text-center">
          {{ $t('headers.administrator_management') }}
        </h3>
      </div>
      <div class="bg-light p-2">
        <ul class="list-group list-group-flush mt-4 list-group--ellipsis">
          <p class="mb-2">
            {{ $t('labels.owner') }}
          </p>
          <owner-item
            :data="owner"
            :transfer_owner_link="transfer_owner_link"
          />
        </ul>
        <ul
          v-if="administrators.length > 0"
          class="list-group list-group-flush mt-4"
        >
          <p class="mb-2">
            {{ $t('labels.administrator') }}
          </p>
          <administrator-item
            v-for="administrator in administrators"
            :key="administrator.id"
            :data="administrator"
            @modalType="openAdministratorModal"
          />
        </ul>
        <ul v-if="members.length > 0" class="list-group list-group-flush mt-4">
          <p class="mb-2">
            {{ $t('labels.member') }}
          </p>
          <member-item
            v-for="member in members"
            :key="member.id"
            :data="member"
            @modalType="openMemberModal"
          />
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import NeoAdministratorApi from '../../../api/neo/administrator';
import BaseAlert from '../../base/BaseAlert.vue';
import OwnerItem from './items/Owner.vue';
import AdministratorItem from './items/Administrator.vue';
import MemberItem from './items/Member.vue';
import SetAdministratorModal from './modals/SetAdministrator.vue';
import RemoveAdministratorModal from './modals/RemoveAdministrator.vue';

export default {
  props: {
    neo_data: {
      type: Object,
      required: true,
    },
    owner_data: {
      type: Object,
      required: true,
    },
    administrators_data: {
      type: [Array, Object],
      required: true,
    },
    members_data: {
      type: [Array, Object],
      required: true,
    },
    neo_profile_link: {
      type: String,
      required: true,
    },
    transfer_owner_link: {
      type: String,
      required: true,
    },
  },
  components: {
    BaseAlert,
    OwnerItem,
    AdministratorItem,
    MemberItem,
    SetAdministratorModal,
    RemoveAdministratorModal,
  },
  setup(props) {
    const neoAdministrator = new NeoAdministratorApi();
    const neo = ref(props.neo_data);
    const owner = ref(props.owner_data);
    const administrators = ref(props.administrators_data);
    const members = ref(props.members_data);
    const currentRio = ref(props.owner_data);
    const setAdministratorModalRef = ref(null);
    const removeAdministratorModalRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });

    /**
     * Reset alert values
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Reset all connection modals
     *
     * @returns {void}
     */
    const resetModals = () => {
      removeAdministratorModalRef.value.hide();
      setAdministratorModalRef.value.hide();
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

    const setCurrentRio = (rio = null) => {
      currentRio.value = rio;
    };

    const setDataState = (data) => {
      administrators.value = data.administrators;
      members.value = data.members;
    };

    const openAdministratorModal = (data) => {
      setCurrentRio(data);
      removeAdministratorModalRef.value.show();
    };

    const openMemberModal = (data) => {
      setCurrentRio(data);
      setAdministratorModalRef.value.show();
    };

    const setRemoveAdministrator = async (data) => {
      resetAlert();
      await neoAdministrator
        .setRemoveAdministrator(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            setDataState(response.data.data);
            setAlert('success', response.data.message);
          }
        })
        .catch(() => setAlert('failed'))
        .finally(() => resetModals());
    };

    return {
      neo,
      owner,
      administrators,
      members,
      currentRio,
      alert,
      resetAlert,
      setAdministratorModalRef,
      removeAdministratorModalRef,
      openAdministratorModal,
      openMemberModal,
      setRemoveAdministrator,
    };
  },
};
</script>

<style></style>
