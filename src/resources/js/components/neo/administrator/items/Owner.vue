<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <img
      class="rounded-circle me-2 d-inline-block img--profile_image_sm"
      :src="owner.profile_image"
      :alt="owner.full_name"
      width="40"
    />
    <span class="fs-xs c-primary ms-2">
      {{ owner.full_name }}
    </span>
    <a class="fs-xs btn btn-link vertical-right" :href="transfer_owner_link">
      {{ $t('links.transfer_of_owner_authority') }}
    </a>
  </li>
</template>

<script>
import { ref } from 'vue';

export default {
  props: {
    data: {
      type: Object,
      required: true,
    },
    transfer_owner_link: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const owner = ref(props.data);

    return {
      owner,
    };
  },
};
</script>

<style></style>
