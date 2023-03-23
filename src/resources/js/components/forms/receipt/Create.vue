<template>
  <div>
    <!-- Receipt Create Form Component -->
    <receipt-create-form
      ref="receiptCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.ADD"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Receipt Confirmation Preview Component -->
    <receipt-confirmation-preview
      v-else
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.ADD"
      @switch-receipt-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import ReceiptCreateForm from './components/Form.vue';
import ReceiptConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'ReceiptCreate',
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
    ReceiptCreateForm,
    ReceiptConfirmationPreview,
  },
  setup() {
    const isFormActive = ref(true);
    const receiptCreateFormRef = ref(null);
    const receiptConfirmationPreviewRef = ref(null);
    const formData = ref({});

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (receiptCreateFormData) => {
      if (isFormActive.value) {
        receiptCreateFormRef.value.setAlert();
        receiptCreateFormRef.value.resetModal();
      }

      formData.value = receiptCreateFormData;

      isFormActive.value = !isFormActive.value;
    };

    return {
      FormOperation,
      isFormActive,
      receiptCreateFormRef,
      receiptConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
    };
  },
});
</script>
