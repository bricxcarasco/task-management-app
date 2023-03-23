<template>
  <div>
    <!-- Invoice Duplicate Form Component -->
    <invoice-duplicate-form
      ref="invoiceDuplicateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :existing_data="formData"
      :form_basic_setting="form_basic_setting"
      :is_duplicate="true"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Invoice Duplication Confirmation Preview Component -->
    <invoice-duplication-confirmation-preview
      v-else
      :form="formData"
      :form_basic_setting="form_basic_setting"
      :is_duplicate="true"
      @switch-invoice-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import InvoiceDuplicateForm from './components/Form.vue';
import InvoiceDuplicationConfirmationPreview from './components/ConfirmationPreview.vue';

export default defineComponent({
  name: 'InvoiceDuplicate',
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
    InvoiceDuplicateForm,
    InvoiceDuplicationConfirmationPreview,
  },
  setup(props) {
    const isFormActive = ref(true);
    const invoiceDuplicateFormRef = ref(null);
    const invoiceDuplicationConfirmationPreviewRef = ref(null);
    const formData = ref(props.form);

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (invoiceDuplicateFormData) => {
      if (isFormActive.value) {
        invoiceDuplicateFormRef.value.setAlert();
        invoiceDuplicateFormRef.value.resetModal();
      }

      formData.value = invoiceDuplicateFormData;
      isFormActive.value = !isFormActive.value;
    };

    return {
      isFormActive,
      invoiceDuplicateFormRef,
      invoiceDuplicationConfirmationPreviewRef,
      handleSwitchComponent,
      formData,
    };
  },
});
</script>
