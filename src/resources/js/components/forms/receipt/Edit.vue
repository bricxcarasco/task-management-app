<template>
  <div>
    <!-- Receipt Edit Form Component -->
    <receipt-edit-form
      ref="receiptFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.EDIT"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Receipt Confirmation Preview Component -->
    <receipt-confirmation-preview
      v-else
      ref="receiptConfirmationRef"
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.EDIT"
      @switch-receipt-create-form="handleSwitchComponent"
      @update-receipt="handleSaveReceipt"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import ReceiptApiService from '../../../api/forms/receipts';
import ReceiptEditForm from './components/Form.vue';
import ReceiptConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'ReceiptEdit',
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
    ReceiptEditForm,
    ReceiptConfirmationPreview,
  },
  setup(props) {
    const receiptsApiService = new ReceiptApiService();
    const isFormActive = ref(true);
    const receiptFormRef = ref(null);
    const receiptConfirmationRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (receiptCreateFormData) => {
      if (isFormActive.value) {
        receiptFormRef.value.setAlert();
        receiptFormRef.value.resetModal();
      }

      formData.value = receiptCreateFormData;

      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save receipt
     *
     * @returns {void}
     */
    const handleSaveReceipt = async (data) => {
      // Reinitialize state
      receiptConfirmationRef.value.setPageLoading(true);

      formData.value.mode = 'save';

      // Handle responses
      await receiptsApiService
        .updateReceipt(data, props.form.id)
        .then(() => {
          window.location.href = `/forms/receipts`;
        })
        .finally(() => {
          receiptConfirmationRef.value.setPageLoading(false);
        });
    };

    return {
      FormOperation,
      isFormActive,
      receiptFormRef,
      receiptConfirmationRef,
      handleSwitchComponent,
      handleSaveReceipt,
      formData,
    };
  },
});
</script>
