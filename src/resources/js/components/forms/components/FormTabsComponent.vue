<template>
  <div class="position-relative">
    <div class="btn--horizontal d-flex d-md-none">
      <button type class="btn btn-link btn--prev p-0">
        <i class="ai-arrow-left"></i>
      </button>
      <button type class="btn btn-link btn--next p-0">
        <i class="ai-arrow-right"></i>
      </button>
    </div>
    <div class="nav--horizontal">
      <ul
        class="
          nav nav-tabs
          tabs--custom
          d-flex
          justify-content-around
          mt-4
          position-relative
        "
        role="tablist"
      >
        <li
          class="nav-item flex-1 m-0 hoverable"
          v-if="
            service.plan_subscriptions.routes.some(
              (data) => data === 'forms.quotations.*'
            )
          "
        >
          <a
            class="nav-link px-md-4 px-0 text-center"
            :class="activeTab === FormTypes.QUOTATION ? 'active' : ''"
            @click="redirectToPage($event, FormTypes.QUOTATION)"
          >
            {{ $t('labels.quotation') }}
          </a>
        </li>
        <li
          class="nav-item flex-1 m-0 hoverable"
          v-if="
            service.plan_subscriptions.routes.some(
              (data) => data === 'forms.purchase-orders.*'
            )
          "
        >
          <a
            class="nav-link px-md-4 px-0 text-center"
            :class="activeTab === FormTypes.PURCHASE_ORDER ? 'active' : ''"
            @click="redirectToPage($event, FormTypes.PURCHASE_ORDER)"
          >
            {{ $t('labels.purchase_order') }}
          </a>
        </li>
        <li
          class="nav-item flex-1 m-0 hoverable"
          v-if="
            service.plan_subscriptions.routes.some(
              (data) => data === 'forms.delivery-slips.*'
            )
          "
        >
          <a
            class="nav-link px-md-4 px-0 text-center"
            :class="activeTab === FormTypes.DELIVERY_SLIP ? 'active' : ''"
            @click="redirectToPage($event, FormTypes.DELIVERY_SLIP)"
          >
            {{ $t('labels.delivery_slip') }}
          </a>
        </li>
        <li
          class="nav-item flex-1 m-0 hoverable"
          v-if="
            service.plan_subscriptions.routes.some(
              (data) => data === 'forms.invoices.*'
            )
          "
        >
          <a
            class="nav-link px-md-4 px-0 text-center"
            :class="activeTab === FormTypes.INVOICE ? 'active' : ''"
            @click="redirectToPage($event, FormTypes.INVOICE)"
          >
            {{ $t('labels.request') }}
          </a>
        </li>
        <li
          class="nav-item flex-1 m-0 hoverable"
          v-if="
            service.plan_subscriptions.routes.some(
              (data) => data === 'forms.receipts.*'
            )
          "
        >
          <a
            class="nav-link px-md-4 px-0 text-center"
            :class="activeTab === FormTypes.RECEIPT ? 'active' : ''"
            @click="redirectToPage($event, FormTypes.RECEIPT)"
          >
            {{ $t('labels.receipt') }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import FormTypes from '../../../enums/FormTypes';

export default defineComponent({
  name: 'FormTabsComponent',
  props: {
    activeTab: {
      type: Number,
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  setup() {
    /**
     * Redirect to page depending on tab clicked
     *
     * @param {object} event
     * @param {integer} page
     * @returns {void}
     */
    const redirectToPage = (event, page) => {
      event.preventDefault();

      switch (page) {
        case FormTypes.QUOTATION:
          window.location.href = `/forms/quotations`;
          break;
        case FormTypes.PURCHASE_ORDER:
          window.location.href = `/forms/purchase-orders`;
          break;
        case FormTypes.DELIVERY_SLIP:
          window.location.href = `/forms/delivery-slips`;
          break;
        case FormTypes.INVOICE:
          window.location.href = `/forms/invoices`;
          break;
        case FormTypes.RECEIPT:
          window.location.href = `/forms/receipts`;
          break;
        default:
          break;
      }
    };

    return {
      redirectToPage,
      FormTypes,
    };
  },
});
</script>
