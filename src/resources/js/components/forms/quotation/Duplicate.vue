<template>
  <div>
    <!-- Quotation Duplicate Form Component -->
    <quotation-duplicate-form
      ref="quotationDuplicateFormRef"
      v-if="isFormActive"
      :rio="rio"
      :service="service"
      :form_basic_setting="form_basic_setting"
      :form_data="formData"
      :is_duplicate="true"
      @switch-confirmation-preview="handleSwitchComponent"
    />

    <!-- Quotation Duplication Confirmation Preview Component -->
    <quotation-duplication-confirmation-preview
      v-else
      ref="quotationDuplicationConfirmationPreviewRef"
      :form_data="formData"
      :form_basic_setting="form_basic_setting"
      :is_duplicate="true"
      @switch-quotation-create-form="handleSwitchComponent"
      @save-quotation-duplicate="handleSaveQuotationDuplicate"
    />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import FormsApiService from '../../../api/forms/forms';
import QuotationDuplicateForm from './components/Form.vue';
import QuotationDuplicationConfirmationPreview from './components/ConfirmationPreview.vue';
import FormOperation from '../../../enums/FormOperationTypes';

export default defineComponent({
  name: 'QuotationDuplicate',
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
    QuotationDuplicateForm,
    QuotationDuplicationConfirmationPreview,
  },
  setup(props) {
    const formsApiService = new FormsApiService();
    const isFormActive = ref(true);
    const quotationDuplicateFormRef = ref(null);
    const quotationDuplicationConfirmationPreviewRef = ref(null);
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
    const handleSwitchComponent = (quotationDuplicateFormData) => {
      formData.value = quotationDuplicateFormData;
      setIssuerImage();
      isFormActive.value = !isFormActive.value;
    };

    /**
     * Handle save quotation
     *
     * @returns {void}
     */
    const handleSaveQuotationDuplicate = () => {
      // Reinitialize state
      quotationDuplicationConfirmationPreviewRef.value.setPageLoading(true);
      quotationDuplicationConfirmationPreviewRef.value.resetAlert();

      formData.value.mode = FormOperation.DUPLICATE;

      // Handle responses
      formsApiService
        .saveCreateQuotationForm(formData.value)
        .then(() => {
          window.location.replace('/forms/quotations');
        })
        .catch(() => {
          quotationDuplicationConfirmationPreviewRef.value.setAlert('failed');
        })
        .finally(() => {
          quotationDuplicationConfirmationPreviewRef.value.setPageLoading(
            false
          );
        });
    };

    return {
      formData,
      isFormActive,
      quotationDuplicateFormRef,
      quotationDuplicationConfirmationPreviewRef,
      handleSwitchComponent,
      handleSaveQuotationDuplicate,
    };
  },
});
</script>
