<template>
  <div>
    <!-- Purchase Order Duplicate Form Component -->
    <purchase-order-duplicate-form
      ref="purchaseOrderDuplicateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.DUPLICATE"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Purchase Order Duplication Confirmation Preview Component -->
    <purchase-order-duplication-confirmation-preview
      v-else
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.DUPLICATE"
      @switch-purchase-order-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import PurchaseOrderDuplicateForm from './components/Form.vue';
import PurchaseOrderDuplicationConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'PurchaseOrderDuplicate',
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
    PurchaseOrderDuplicateForm,
    PurchaseOrderDuplicationConfirmationPreview,
  },
  setup(props) {
    const isFormActive = ref(true);
    const purchaseOrderDuplicateFormRef = ref(null);
    const purchaseOrderDuplicationConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Re-set issuer profile image
     *
     * @returns {void}
     */
    const setIssuerImage = () => {
      formData.value.issuer_image = props.form.issuer_image;
    };

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (purchaseOrderDuplicateFormData) => {
      if (isFormActive.value) {
        purchaseOrderDuplicateFormRef.value.setAlert();
        purchaseOrderDuplicateFormRef.value.resetModal();
      }

      formData.value = purchaseOrderDuplicateFormData;
      setIssuerImage();
      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      purchaseOrderDuplicateFormRef,
      purchaseOrderDuplicationConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
      FormOperation,
    };
  },
});
</script>
