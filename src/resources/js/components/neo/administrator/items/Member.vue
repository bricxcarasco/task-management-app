<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <img
      class="rounded-circle me-2 d-inline-block img--profile_image_sm"
      :src="member.profile_image"
      :alt="member.full_name"
      width="40"
    />
    <span class="fs-xs c-primary ms-2">
      {{ member.full_name }}
    </span>
    <a class="fs-xs btn btn-link vertical-right" @click.prevent="handleClick">
      {{ $t('links.designated_as_an_administrator') }}
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
  },
  emits: ['modalType'],
  setup(props, { emit }) {
    const member = ref(props.data);

    const handleClick = () => {
      emit('modalType', member.value);
    };

    return {
      member,
      handleClick,
    };
  },
};
</script>

<style></style>
