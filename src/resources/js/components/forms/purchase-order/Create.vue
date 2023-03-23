<template>
  <div>
    <!-- Purchase order Form Component -->
    <purchase-order-create-form
      ref="purchaseOrderCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Purchase order Confirmation Preview Component -->
    <purchase-order-confirmation-preview
      v-else
      :form="formData"
      :operation="FormOperation.ADD"
      :form_basic_setting="form_basic_setting"
      @switch-purchase-order-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import PurchaseOrderCreateForm from './components/Form.vue';
import PurchaseOrderConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'PurchaseOrderCreate',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    form_basic_setting: {
      type: [Array, Object],
    },
  },
  components: {
    PurchaseOrderCreateForm,
    PurchaseOrderConfirmationPreview,
  },
  setup() {
    const isFormActive = ref(true);
    const purchaseOrderCreateFormRef = ref(null);
    const purchaseOrderConfirmationPreviewRef = ref(null);
    const formData = ref({});

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (purchaseOrderCreateFormData) => {
      if (isFormActive.value) {
        purchaseOrderCreateFormRef.value.setAlert();
        purchaseOrderCreateFormRef.value.resetModal();
      }

      formData.value = purchaseOrderCreateFormData;

      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      purchaseOrderCreateFormRef,
      purchaseOrderConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
      FormOperation,
    };
  },
});
</script>
