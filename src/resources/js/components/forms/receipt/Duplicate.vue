<template>
  <div>
    <!-- Receipt Duplicate Form Component -->
    <receipt-duplicate-form
      ref="receiptDuplicateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.DUPLICATE"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Receipt Duplication Confirmation Preview Component -->
    <receipt-duplication-confirmation-preview
      v-else
      :form="formData"
      :form_basic_setting="form_basic_setting"
      @switch-receipt-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import ReceiptDuplicateForm from './components/Form.vue';
import ReceiptDuplicationConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'ReceiptDuplicate',
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
    ReceiptDuplicateForm,
    ReceiptDuplicationConfirmationPreview,
  },
  setup(props) {
    const isFormActive = ref(true);
    const receiptDuplicateFormRef = ref(null);
    const receiptConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (receiptDuplicateFormData) => {
      if (isFormActive.value) {
        receiptDuplicateFormRef.value.setAlert();
        receiptDuplicateFormRef.value.resetModal();
      }

      formData.value = receiptDuplicateFormData;

      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      receiptDuplicateFormRef,
      receiptConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
      FormOperation,
    };
  },
});
</script>
