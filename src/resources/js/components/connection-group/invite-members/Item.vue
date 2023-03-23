<template>
  <li
    class="
      d-flex
      justify-content-end
      flex-direction-row flex-wrap
      align-items-center
      list-group-item
      px-0
      py-2
      position-relative
      list--white
      px-2
    "
  >
    <!-- Profile Image -->
    <div class="me-auto">
      <img
        class="
          rounded-circle
          me-2
          d-inline-block
          connection__result
          img--profile_image_sm
        "
        data-field="profile_image"
        :src="user.profile_image"
        width="40"
        @error="
          Common.handleNotFoundImageException(
            $event,
            DefaultImageCategory.RIO_NEO
          )
        "
        @click="handleRedirectToRioProfile(user.id)"
      />

      <!-- Full Name -->
      <span
        class="fs-xs c-primary ms-2 connection__result"
        data-field="full_name"
        @click="handleRedirectToRioProfile(user.id)"
      >
        {{ user.full_name }}
      </span>
    </div>

    <!-- Action Element -->

    <!-- Participating -->
    <span v-if="isParticipating()" class="fs-xs c-primary p-2">
      {{ $t('labels.participating') }}
    </span>
    <!-- Pending Invite -->
    <div v-else-if="isInvitePending()">
      <span class="fs-xs c-gray p-2">{{ $t('labels.pending_invite') }}</span>
      <a class="fs-xs btn text-danger p-2" @click.prevent="onCancelInvite">
        {{ $t('buttons.cancel_invite') }}
      </a>
    </div>
    <!-- Invite -->
    <a
      v-else-if="!full_group"
      class="fs-xs btn btn-link p-2"
      @click.prevent="onInvite"
    >
      {{ $t('buttons.invite') }}
    </a>
  </li>
</template>

<script>
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
    full_group: {
      type: Boolean,
      required: true,
    },
  },
  setup(props, { emit }) {
    /**
     * Emit invite user action
     *
     * @returns {void}
     */
    const onInvite = () => {
      emit('invite-user', props.user);
    };

    /**
     * Emit cancel invite
     *
     * @returns {void}
     */
    const onCancelInvite = () => {
      emit('cancel-invite', props.user.invite_id);
    };

    /**
     * Check if has pending invite
     *
     * @returns {void}
     */
    const isInvitePending = () => props.user.invite_status === 0;

    /**
     * Check if participating
     *
     * @returns {void}
     */
    const isParticipating = () => props.user.invite_status === 1;

    const handleRedirectToRioProfile = (id) => {
      window.open(`/rio/profile/introduction/${id}`, '_blank');
    };

    return {
      onInvite,
      onCancelInvite,
      isInvitePending,
      isParticipating,
      handleRedirectToRioProfile,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
