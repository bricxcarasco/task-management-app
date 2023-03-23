<template>
  <div>
    <!-- Invoice Create Form Component -->
    <invoice-create-form
      ref="invoiceCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Invoice Confirmation Preview Component -->
    <invoice-confirmation-preview
      v-else
      :form="formData"
      :form_basic_setting="form_basic_setting"
      @switch-invoice-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import InvoiceCreateForm from './components/Form.vue';
import InvoiceConfirmationPreview from './components/ConfirmationPreview.vue';

export default defineComponent({
  name: 'InvoiceCreate',
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
    InvoiceCreateForm,
    InvoiceConfirmationPreview,
  },
  setup() {
    const isFormActive = ref(true);
    const invoiceCreateFormRef = ref(null);
    const invoiceConfirmationPreviewRef = ref(null);
    const formData = ref({});

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (invoiceCreateFormData) => {
      if (isFormActive.value) {
        invoiceCreateFormRef.value.setAlert();
        invoiceCreateFormRef.value.resetModal();
      }

      formData.value = invoiceCreateFormData;

      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      invoiceCreateFormRef,
      invoiceConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
    };
  },
});
</script>
