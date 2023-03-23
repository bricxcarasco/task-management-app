<template>
  <div>
    <!-- Quotation Create Form Component -->
    <quotation-create-form
      ref="quotationCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :form_basic_setting="form_basic_setting"
      :form_data="formData"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Quotation Confirmation Preview Component -->
    <quotation-confirmation-preview
      v-else
      ref="quotationConfirmationPreviewRef"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      @switch-quotation-create-form="handleSwitchComponent"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import QuotationCreateForm from './components/Form.vue';
import QuotationConfirmationPreview from './components/ConfirmationPreview.vue';

export default defineComponent({
  name: 'QuotationCreate',
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
    QuotationCreateForm,
    QuotationConfirmationPreview,
  },
  setup() {
    const isFormActive = ref(true);
    const quotationCreateFormRef = ref(null);
    const formData = ref({});

    /**
     * Handle switch component
     *
     * @returns {void}
     */
    const handleSwitchComponent = (quotationCreateFormData) => {
      formData.value = quotationCreateFormData;
      isFormActive.value = !isFormActive.value;
    };

    return {
      formData,
      isFormActive,
      quotationCreateFormRef,
      handleSwitchComponent,
    };
  },
});
</script>
