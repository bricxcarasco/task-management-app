<template>
  <li class="list-group-item px-0 py-2 position-relative list--white px-2">
    <img
      class="rounded-circle me-2 d-inline-block img--profile_image_sm"
      :src="administrator.profile_image"
      :alt="administrator.full_name"
      width="40"
    />
    <span class="fs-xs c-primary ms-2">
      {{ administrator.full_name }}
    </span>
    <a class="fs-xs btn btn-link vertical-right" @click.prevent="handleClick">
      {{ $t('links.release_as_an_administrator') }}
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
    const administrator = ref(props.data);

    const handleClick = () => {
      emit('modalType', administrator.value);
    };

    return {
      administrator,
      handleClick,
    };
  },
};
</script>

<style></style>
