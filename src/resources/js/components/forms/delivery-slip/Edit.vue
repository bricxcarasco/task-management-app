<template>
  <div>
    <!-- Delivery Slip Create Form Component -->
    <delivery-slip-create-form
      ref="deliverySlipCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :form_basic_setting="form_basic_setting"
      :form_data="formData"
      :operation="FormOperation.EDIT"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Delivery Slip Confirmation Preview Component -->
    <delivery-slip-confirmation-preview
      v-else
      ref="deliverySlipConfirmationPreviewRef"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.EDIT"
      @switch-delivery-slip-create-form="handleSwitchComponent"
      @save-delivery-slip="handleSaveDeliverySlip"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import DeliverySlipApiService from '../../../api/forms/delivery-slips';
import DeliverySlipCreateForm from './components/Form.vue';
import DeliverySlipConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'DeliverySlipCreate',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    form: {
      type: [Array, Object],
    },
    form_basic_setting: {
      type: [Array, Object],
    },
  },
  components: {
    DeliverySlipCreateForm,
    DeliverySlipConfirmationPreview,
  },
  setup(props) {
    const deliverySlipApiService = new DeliverySlipApiService();
    const isFormActive = ref(true);
    const deliverySlipCreateFormRef = ref(null);
    const deliverySlipConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (deliverySlipCreateFormData) => {
      if (isFormActive.value) {
        deliverySlipCreateFormRef.value.setAlert();
        deliverySlipCreateFormRef.value.resetModal();
      }

      formData.value = deliverySlipCreateFormData;
      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save quotation
     *
     * @returns {void}
     */
    const handleSaveDeliverySlip = (grandTotal) => {
      // Reinitialize state
      deliverySlipConfirmationPreviewRef.value.setPageLoading(true);
      deliverySlipConfirmationPreviewRef.value.resetAlert();

      formData.value.mode = 'save';
      formData.value.price = parseInt(grandTotal, 10);

      // Handle responses
      deliverySlipApiService
        .updateDeliverySlip(formData.value, props.form.id)
        .then(() => {
          window.location.replace('/forms/delivery-slips');
        })
        .catch(() => {
          deliverySlipConfirmationPreviewRef.value.setAlert('failed');
        })
        .finally(() => {
          deliverySlipConfirmationPreviewRef.value.setPageLoading(false);
        });
    };

    return {
      FormOperation,
      formData,
      isFormActive,
      deliverySlipCreateFormRef,
      deliverySlipConfirmationPreviewRef,
      handleSwitchComponent,
      handleSaveDeliverySlip,
    };
  },
});
</script>
