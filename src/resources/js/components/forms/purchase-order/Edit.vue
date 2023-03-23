<template>
  <div>
    <!-- Purchase order Form Component -->
    <purchase-order-edit-form
      ref="purchaseOrderEditFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.EDIT"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Purchase order Confirmation Preview Component -->
    <purchase-order-confirmation-preview
      v-else
      ref="purchaseOrderConfirmationPreviewRef"
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :operation="FormOperation.EDIT"
      @switch-purchase-order-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import PurchaseOrderEditForm from './components/Form.vue';
import PurchaseOrderConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'PurchaseOrderEdit',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    form: {
      type: [Array, Object],
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
    PurchaseOrderEditForm,
    PurchaseOrderConfirmationPreview,
  },
  setup(props) {
    const isFormActive = ref(true);
    const purchaseOrderEditFormRef = ref(null);
    const purchaseOrderConfirmationPreviewRef = ref(null);
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
    const handleSwitchComponent = (purchaseOrderEditFormData) => {
      const data = { ...purchaseOrderEditFormData };
      data.id = props.form.id;

      formData.value = data;
      setIssuerImage();
      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      purchaseOrderEditFormRef,
      purchaseOrderConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
      setIssuerImage,
      FormOperation,
    };
  },
});
</script>
