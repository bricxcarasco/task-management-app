<template>
  <div
    class="modal fade"
    id="affiliate-form-modal"
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
          <input type="hidden" name="id" v-model="formData.id" />
          <input
            class="form-control mt-3 text-center"
            :class="errors?.organization_name != null ? 'is-invalid' : ''"
            type="hidden"
            v-model="formData.organization_name"
            name="organization_name"
            readonly
          />
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t('headers.public_settings') }}
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
            <p class="text-center">{{ $t('messages.rio.update_display') }}</p>
            <p class="mt-3 text-center">{{ neoAffiliate }}</p>
            <div :class="errors?.is_display != null ? 'is-invalid' : ''">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  value="1"
                  v-model="formData.is_display"
                  name="is_display"
                  id="public"
                />
                <label class="form-check-label" for="public">{{
                  $t('labels.affiliation_public')
                }}</label>
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  value="0"
                  v-model="formData.is_display"
                  name="is_display"
                  id="private"
                />
                <label class="form-check-label" for="private">{{
                  $t('labels.affiliation_private')
                }}</label>
              </div>
            </div>
            <div
              v-show="errors?.is_display"
              v-for="(error, index) in errors?.is_display"
              :key="index"
              class="invalid-feedback"
            >
              {{ error }}
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

export default {
  name: 'FormAffiliateModal',
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const rioProfileApiService = new RioProfileApiService();
    const errors = ref(null);
    const modalLoading = ref(false);
    const modalRef = ref(null);
    const formRef = ref({});
    const formData = ref({});
    const neoAffiliate = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      modalRef.value.querySelector('#public').value = 1;
      modalRef.value.querySelector('#private').value = 0;
      modalRef.value.querySelector('.btn-close').click();
      formData.value = {};
      errors.value = null;
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = () => {
      const object = objectifyForm(formRef.value);
      formData.value = object;
      neoAffiliate.value = formData.value.organization_name;
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
      const apiCall = rioProfileApiService.updateAffiliate(
        formData.value.id,
        formData.value
      );

      // Handle responses
      apiCall
        .then(() => {
          emit('set-alert', 'success');
          emit('get-affiliates');
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
      neoAffiliate,
    };
  },
};
</script>

<style></style>
