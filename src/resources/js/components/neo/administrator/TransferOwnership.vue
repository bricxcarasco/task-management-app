<template>
  <div>
    <form
      id="transferOwnership"
      :action="transfer_ownership_post"
      method="POST"
    >
      <transfer-ownership-modal
        ref="transferOwnershipModalRef"
        :data="currentRio"
        @transferOwnership="transferOwnership"
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
        <a :href="neo_administrator_link" class="btn btn-secondary btn--back">
          <i class="ai-arrow-left"></i>
        </a>
        <h3>
          {{ $t('headers.owner_authority_transfer') }}
        </h3>
      </div>
      <div class="d-flex flex-column bg-light rounded-3 shadow-lg p-2">
        <input type="hidden" name="_token" :value="csrf" />
        <div class="border-bottom position-relative d-block d-md-none">
          <a :href="neo_administrator_link" class="btn btn-secondary btn--back">
            <i class="ai-arrow-left"></i>
          </a>
          <h3 class="p-3 mb-0 text-center">
            {{ $t('headers.owner_authority_transfer') }}
          </h3>
        </div>
        <div class="bg-light p-2">
          <ul
            v-if="members.length > 0"
            class="list-group list-group-flush list-group--ellipsis"
          >
            <p class="my-4">
              {{ $t('messages.neo.transfer_ownership_select') }}
            </p>
            <transfer-ownership
              v-for="member in members"
              :key="member.id"
              :data="member"
              @click.prevent="openTransferOwnershipModal(member)"
            />
          </ul>
          <ul v-else>
            {{
              $t('messages.neo.transfer_ownership_select')
            }}
          </ul>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { computed, ref } from 'vue';
import TransferOwnership from './items/TransferOwnership.vue';
import TransferOwnershipModal from './modals/TransferOwnership.vue';

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
    members_data: {
      type: [Array, Object],
      required: true,
    },
    neo_administrator_link: {
      type: String,
      required: true,
    },
    transfer_ownership_post: {
      type: String,
      required: true,
    },
  },
  components: {
    TransferOwnership,
    TransferOwnershipModal,
  },
  setup(props) {
    const neo = ref(props.neo_data);
    const members = ref(props.members_data);
    const currentRio = ref(props.owner_data);
    const transferOwnershipModalRef = ref(null);

    const csrf = computed(
      () => document.querySelector('meta[name="csrf-token"]').content
    );

    const setCurrentRio = (rio = null) => {
      currentRio.value = rio;
    };

    const openTransferOwnershipModal = (data) => {
      setCurrentRio(data);
      transferOwnershipModalRef.value.show();
    };

    const transferOwnership = async (data) => {
      const transferOwnershipForm =
        document.getElementById('transferOwnership');
      const rioId = document.createElement('input');

      rioId.setAttribute('type', 'hidden');
      rioId.setAttribute('name', 'rio_id');
      rioId.setAttribute('value', data.rio_id);

      transferOwnershipForm.appendChild(rioId);
      transferOwnershipForm.submit();
    };

    return {
      neo,
      members,
      currentRio,
      csrf,
      transferOwnershipModalRef,
      openTransferOwnershipModal,
      transferOwnership,
    };
  },
};
</script>
