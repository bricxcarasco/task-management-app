<template>
  <div>
    <!-- Delivery Slip Duplicate Form Component -->
    <delivery-slip-duplicate-form
      ref="deliverySlipDuplicateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :form_basic_setting="form_basic_setting"
      :form_data="formData"
      :is_duplicate="true"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Delivery Slip Duplication Confirmation Preview Component -->
    <delivery-slip-duplication-confirmation-preview
      v-else
      ref="deliverySlipDuplicationConfirmationPreviewRef"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      :is_duplicate="true"
      @switch-delivery-slip-create-form="handleSwitchComponent"
      @save-delivery-slip="handleSaveDeliverySlip"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import DeliverySlipApiService from '../../../api/forms/delivery-slips';
import DeliverySlipDuplicateForm from './components/Form.vue';
import DeliverySlipDuplicationConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'DeliverySlipDuplicate',
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
    form: {
      type: [Array, Object, null],
    },
  },
  components: {
    DeliverySlipDuplicateForm,
    DeliverySlipDuplicationConfirmationPreview,
  },
  setup(props) {
    const deliverySlipApiService = new DeliverySlipApiService();
    const isFormActive = ref(true);
    const deliverySlipDuplicateFormRef = ref(null);
    const deliverySlipDuplicationConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (deliverySlipDuplicateFormData) => {
      formData.value = deliverySlipDuplicateFormData;
      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save delivery slip
     *
     * @returns {void}
     */
    const handleSaveDeliverySlip = (grandTotal) => {
      // Reinitialize state
      deliverySlipDuplicationConfirmationPreviewRef.value.setPageLoading(true);
      deliverySlipDuplicationConfirmationPreviewRef.value.resetAlert();

      formData.value.mode = FormOperation.DUPLICATE;
      formData.value.price = parseInt(grandTotal, 10);

      // Handle responses
      deliverySlipApiService
        .confirmDeliverySlip(formData.value)
        .then(() => {
          window.location.replace('/forms/delivery-slips');
        })
        .catch(() => {
          deliverySlipDuplicationConfirmationPreviewRef.value.setAlert(
            'failed'
          );
        })
        .finally(() => {
          deliverySlipDuplicationConfirmationPreviewRef.value.setPageLoading(
            false
          );
        });
    };

    return {
      formData,
      isFormActive,
      deliverySlipDuplicateFormRef,
      deliverySlipDuplicationConfirmationPreviewRef,
      handleSwitchComponent,
      handleSaveDeliverySlip,
    };
  },
});
</script>
