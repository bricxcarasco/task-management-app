<template>
  <div
    class="modal fade"
    id="product-form-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">
              {{ modalTitle }}
            </h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <input type="hidden" name="id" v-model="formData.id" />
              <div class="col-12">
                <div class="mb-3">
                  <!-- Product Name -->
                  <label class="form-label" for="product-name-field">
                    {{ $t('labels.product_name') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input
                    class="form-control"
                    :class="errors?.content != null ? 'is-invalid' : ''"
                    type="text"
                    v-model="formData.content"
                    name="content"
                    :placeholder="this.$i18n.t('placeholders.product_name')"
                  />
                  <div
                    v-show="errors?.content"
                    v-for="(error, index) in errors?.content"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>

                <div class="mb-3">
                  <!-- Product Description -->
                  <label class="form-label" for="product-description-field">
                    {{ $t('labels.product_description') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <textarea
                    class="form-control"
                    v-model="formData.additional"
                    rows="5"
                    :class="errors?.additional != null ? 'is-invalid' : ''"
                    name="additional"
                  ></textarea>
                  <div
                    v-show="errors?.additional"
                    v-for="(error, index) in errors?.additional"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>

                <div class="mb-3">
                  <!-- Reference URL -->
                  <label class="form-label" for="reference-url-field">
                    {{ $t('labels.product_reference_url') }}
                  </label>
                  <div
                    class="
                      d-flex
                      justify-content-between
                      align-items-center
                      fs-sm
                      mb-2
                      row
                    "
                  >
                    <span class="muted">{{
                      $t('messages.rio.please_paste_the_url')
                    }}</span>
                  </div>
                  <input
                    class="form-control"
                    :class="errors?.reference_url != null ? 'is-invalid' : ''"
                    type="text"
                    v-model="formData.reference_url"
                    name="reference_url"
                  />
                  <div
                    v-show="errors?.reference_url"
                    v-for="(error, index) in errors?.reference_url"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>

                <div class="mb-3 d-none">
                  <!-- Image -->
                  <label class="form-label" for="reg-fn">
                    {{ $t('labels.product_image') }}
                  </label>
                  <div
                    class="
                      d-flex
                      align-items-center
                      justify-content-between
                      btn__group--alt
                    "
                  >
                    <div
                      class="
                        position-relative
                        border-secondary
                        form-control
                        p-0
                        d-flex
                      "
                    >
                      <input
                        class="form-control"
                        :class="errors?.image_link != null ? 'is-invalid' : ''"
                        type="file"
                        id="image-field"
                        name="image_link"
                        style="display: none"
                      />
                      <label
                        for="image-field"
                        class="btn btn-secondary uploadImgFilename"
                        style="font-weight: 400"
                        >{{ $t('buttons.add_product_image') }}</label
                      >
                      <label
                        for="image-field"
                        class="
                          btn
                          color-primary
                          text-start
                          px-2
                          uploadImgFilename
                        "
                        style="flex: 1"
                        id="outputfile"
                      ></label>
                    </div>
                    <button type="button" class="delete btn btn-secondary">
                      <i class="text-nav ai-x"></i>
                    </button>
                  </div>
                  <div
                    v-show="errors?.image_link"
                    v-for="(error, index) in errors?.image_link"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.register') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { objectifyForm } from '../../../../../utils/objectifyForm';
import SectionLoader from '../../../../base/BaseSectionLoader.vue';
import RioProfileApiService from '../../../../../api/rio/profile';
import i18n from '../../../../../i18n';

export default {
  name: 'AddProductModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const rioProfileApiService = new RioProfileApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const modalTitle = ref(null);
    const formRef = ref({});
    const formData = ref({});

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /**
     * Check if modal is in edit state
     *
     * @returns {bool}
     */
    const isEdit = () => formData.value.id !== '';

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
    };

    /**
     * Update modal title depending on status
     *
     * @returns {void}
     */
    const updateModalTitle = () => {
      modalTitle.value = isEdit()
        ? i18n.global.t('headers.product_edit')
        : i18n.global.t('headers.product_registration');
    };

    /**
     * Event listener for add product form submit
     *
     * @returns {void}
     */
    const submitForm = async (event) => {
      event.preventDefault();

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;
      emit('reset-alert');

      // Execute API call

      const apiCall = isEdit()
        ? rioProfileApiService.updateProduct(formData.value.id, formData.value)
        : rioProfileApiService.addProduct(formData.value);

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-products');
          resetModal();
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          emit('set-alert', 'failed');
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        updateModel();
        updateModalTitle();
      });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(() => {
      attachShowModalListener();
    });

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
      modalTitle,
    };
  },
};
</script>

<style></style>
