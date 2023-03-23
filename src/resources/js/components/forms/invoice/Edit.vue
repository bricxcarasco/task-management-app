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
      :is_edit="true"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Invoice Confirmation Preview Component -->
    <invoice-confirmation-preview
      v-else
      ref="invoiceConfirmationPreviewRef"
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :is_edit="true"
      @switch-invoice-create-form="handleSwitchComponent"
      @save-invoice="handleSaveInvoice"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import InvoicesApiService from '../../../api/forms/invoices';
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
    form: {
      type: [Array, Object],
    },
    form_basic_setting: {
      type: [Array, Object],
    },
  },
  components: {
    InvoiceCreateForm,
    InvoiceConfirmationPreview,
  },
  setup(props) {
    const invoicesApiService = new InvoicesApiService();
    const isFormActive = ref(true);
    const invoiceCreateFormRef = ref(null);
    const invoiceConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (invoiceCreateFormData) => {
      formData.value = invoiceCreateFormData;
      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save quotation
     *
     * @returns {void}
     */
    const handleSaveInvoice = async (data) => {
      // Reinitialize state
      invoiceConfirmationPreviewRef.value.setPageLoading(true);

      formData.value.mode = 'save';

      // Handle responses
      await invoicesApiService
        .updateInvoice(data, props.form.id)
        .then(() => {
          window.location.href = `/forms/invoices`;
        })
        .finally(() => {
          invoiceConfirmationPreviewRef.value.setPageLoading(false);
        });
    };

    return {
      isFormActive,
      invoiceCreateFormRef,
      invoiceConfirmationPreviewRef,
      handleSwitchComponent,
      handleSaveInvoice,
      formData,
    };
  },
});
</script>
