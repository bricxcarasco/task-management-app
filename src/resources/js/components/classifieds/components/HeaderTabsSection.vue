<template>
  <div class="position-relative">
    <h3 class="py-3 mb-0 text-center">
      {{ $t('headers.service_owned_classifieds', { name: serviceName }) }}
    </h3>
    <div class="text-end" style="margin-left: auto">
      <button
        class="btn btn-link p-2 c-primary"
        @click="redirectToPage($event, 'stripe')"
      >
        <div class="d-block text-center fs-3 mb-4"></div>
        <span class="fs-md">{{ $t('buttons.stripe') }}</span>
      </button>
      <button
        class="btn btn-link p-2 c-primary"
        @click="redirectToPage($event, 'settings')"
      >
        <i class="ai-user d-block text-center fs-3"></i>
        <span class="fs-md">{{ $t('buttons.receiving_account_setting') }}</span>
      </button>
      <button
        class="btn btn-link p-2 c-primary"
        @click="redirectToPage($event, 'favorites')"
      >
        <i class="ai-bookmark d-block text-center fs-3"></i>
        <span class="fs-md">{{ $t('buttons.favorite') }}</span>
      </button>
    </div>
    <ul class="connection__links p-0">
      <li
        class="connection__link bg-light hoverable"
        :class="activeTab === 'search' ? 'active' : ''"
      >
        <a @click="redirectToPage($event, 'search')">
          {{ $t('headers.search') }}
        </a>
      </li>
      <li
        class="connection__link bg-light hoverable"
        :class="activeTab === 'product_management' ? 'active' : ''"
      >
        <a @click="redirectToPage($event, 'product_management')">
          {{ $t('headers.product_management') }}
        </a>
      </li>
      <li
        class="connection__link bg-light hoverable"
        :class="activeTab === 'message' ? 'active' : ''"
      >
        <a @click="redirectToPage($event, 'message')">
          {{ $t('headers.message') }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
import { defineComponent, computed, ref } from 'vue';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'HeaderTabsSectionComponent',
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
    activeTab: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const service = ref(props.service);

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case ServiceSelectionTypesEnum.RIO:
          return data.full_name;
        case ServiceSelectionTypesEnum.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Redirect to page depending on tab clicked
     *
     * @param {object} event
     * @param {string} page
     * @returns {void}
     */
    const redirectToPage = (event, page) => {
      event.preventDefault();

      switch (page) {
        case 'search':
          window.location.href = `/classifieds`;
          break;
        case 'settings':
          window.location.href = `/classifieds/settings`;
          break;
        case 'favorites':
          window.location.href = `/classifieds/favorites`;
          break;
        case 'product_management':
          window.location.href = `/classifieds/registered-products`;
          break;
        case 'message':
          window.location.href = `/classifieds/messages`;
          break;
        case 'stripe':
          window.open('https://dashboard.stripe.com/dashboard', '_blank');
          break;
        default:
          break;
      }
    };

    return {
      serviceName,
      redirectToPage,
    };
  },
});
</script>
