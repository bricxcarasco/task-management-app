<template>
  <div>
    <!-- Quotation Edit Form Component -->
    <quotation-edit-form
      ref="quotationCreateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      :is_edit="true"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Quotation Confirmation Preview Component -->
    <quotation-confirmation-preview
      v-else
      ref="quotationConfirmationPreviewRef"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      :is_edit="true"
      @switch-quotation-create-form="handleSwitchComponent"
      @save-quotation="handleSaveQuotation"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import FormsApiService from '../../../api/forms/forms';
import QuotationEditForm from './components/Form.vue';
import QuotationConfirmationPreview from './components/ConfirmationPreview.vue';

export default defineComponent({
  name: 'QuotationEdit',
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
    QuotationEditForm,
    QuotationConfirmationPreview,
  },
  setup(props) {
    const formsApiService = new FormsApiService();
    const isFormActive = ref(true);
    const quotationCreateFormRef = ref(null);
    const quotationConfirmationPreviewRef = ref(null);
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
    const handleSwitchComponent = (quotationCreateFormData) => {
      formData.value = quotationCreateFormData;
      setIssuerImage();
      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save quotation
     *
     * @returns {void}
     */
    const handleSaveQuotation = async () => {
      // Reinitialize state
      quotationConfirmationPreviewRef.value.setPageLoading(true);
      quotationConfirmationPreviewRef.value.resetAlert();

      formData.value.mode = 'save';

      // Handle responses
      await formsApiService
        .updateQuotation(formData.value, props.form.id)
        .then(() => {
          window.location.replace('/forms/quotations');
        })
        .catch(() => {
          quotationConfirmationPreviewRef.value.setAlert('failed');
        })
        .finally(() => {
          quotationConfirmationPreviewRef.value.setPageLoading(false);
        });
    };

    return {
      formData,
      isFormActive,
      quotationCreateFormRef,
      quotationConfirmationPreviewRef,
      handleSwitchComponent,
      handleSaveQuotation,
    };
  },
});
</script>
