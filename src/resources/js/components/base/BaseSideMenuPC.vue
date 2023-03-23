<template>
  <div class="col-md-4 d-none d-md-block">
    <div class="bg-light rounded-3 shadow-lg">
      <div class="px-4 py-4 mb-1 text-center">
        <img
          class="d-block rounded-circle mx-auto my-2 img--profile_image"
          :src="defaultImage"
          width="110"
        />
        <h6 class="mb-0 pt-1">{{ fullname }}</h6>
      </div>
      <div class="d-lg-none px-4 pb-4 text-center">
        <a
          class="btn btn-primary px-5 mb-2"
          href="#account-menu"
          data-bs-toggle="collapse"
        >
          <i class="ai-menu me-2"></i>
        </a>
      </div>
      <div class="d-lg-block collapse pb-2" id="account-menu">
        <h3
          class="
            d-block d-flex
            align-items-center
            bg-secondary
            fs-sm
            fw-semibold
            text-muted
            mb-0
            px-4
            py-3
          "
        >
          {{ $t('links.account') }}
          <span class="text-muted fs-sm fw-normal ms-auto badge bg-dark">
            {{ $t('links.free_membership') }}
          </span>
        </h3>
        <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.home_screen') }}
        </a>
        <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.account_info') }}
        </a>
        <h3
          class="
            d-block
            bg-secondary
            fs-sm
            fw-semibold
            text-muted
            mb-0
            px-4
            py-3
          "
        >
          {{ $t('links.others') }}
        </h3>
        <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.basic_settings') }}
        </a>
        <!-- <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.help') }}
        </a> -->
        <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.logout') }}
        </a>
        <!-- <a
          class="
            d-flex
            align-items-center
            nav-link-style
            px-4
            py-3
            border-top
            mb-0
          "
          href="#"
        >
          {{ $t('links.withdrawal') }}
        </a> -->
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import BpheroConfig from '../../config/bphero';

export default {
  name: 'BaseSideMenuPC',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props) {
    /**
     * Computed properties
     */
    const defaultImage = computed(() => BpheroConfig.DEFAULT_IMG);
    const fullname = computed(
      () => `${props.rio.family_name} ${props.rio.first_name}`
    );

    return {
      defaultImage,
      fullname,
    };
  },
};
</script>
