<template>
  <div
    class="modal fade"
    id="send-inquiry-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.inquiry') }}</h4>
          <button
            class="btn-close js-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="resetModal"
          ></button>
        </div>
        <div class="modal-body">
          <div
            class="
              d-flex
              border
              align-items-center
              justify-content-between
              rounded-3
              p-2
            "
          >
            <img
              class="rounded-3 me-4"
              :src="product.main_photo"
              alt="Image"
              width="100"
            />
            <div class="flex-1">
              <p class="mb-2 break-word">{{ product.title }}</p>
              <span v-if="product.price_str !== null" class="text-danger">
                {{ $t('labels.price', { price: product.price_str }) }}
              </span>
              <span v-else class="text-danger">
                {{ $t('labels.individual_quote') }}
              </span>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between mt-4">
            <span>■ {{ $t('labels.contributor') }}</span>
            <a :href="sellerProfileLink">
              {{ $t('buttons.view_page', { type: product.selling_type }) }}
              <i class="ai-arrow-right"></i>
            </a>
          </div>
          <div
            class="
              d-flex
              border
              align-items-center
              justify-content-between
              rounded-3
              p-2
            "
          >
            <img
              class="img-rounded me-4"
              :src="product.selling_photo"
              alt="Image"
              width="60"
              @error="
                Common.handleNotFoundImageException(
                  $event,
                  DefaultImageCategory.RIO_NEO
                )
              "
            />
            <p class="mb-2 flex-1">
              {{ product.selling_name }}
            </p>
          </div>
          <form action="" @submit="handleSendInquiry" ref="formRef" novalidate>
            <div class="mt-4">
              <label for="textarea-input" class="form-label ps-0"
                >■ {{ $t('labels.inquiry') }}</label
              >
              <textarea
                v-model="formData.message"
                class="form-control"
                :class="errors?.message != null ? 'is-invalid' : ''"
                rows="3"
                :placeholder="
                  $t('placeholders.i_would_like_to_purhcase_thanks')
                "
              ></textarea>
              <base-validation-error :errors="errors?.message" />
            </div>
            <p
              class="p-3 mx-auto w-75 mt-4 mb-0 write-legal-terms"
              style="border: 1px dashed #ddd"
            >
              <span v-html="$t('paragraphs.write_legal_disclaimers')"></span>
              <br /><br />
              {{ $t('paragraphs.write_legal_disclaimers_second') }}
            </p>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.send2') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, computed, ref } from 'vue';
import BaseValidationError from '../../base/BaseValidationError.vue';
import Common from '../../../common';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ClassifiedContactsApiService from '../../../api/classifieds/contacts';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';

export default defineComponent({
  name: 'SendInquiryModalComponent',
  props: {
    product: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseValidationError,
    SectionLoader,
  },
  setup(props, { emit }) {
    const classifiedContactsApiService = new ClassifiedContactsApiService();
    const product = ref(props.product);
    const modalLoading = ref(false);
    const errors = ref(null);
    const formRef = ref({});
    const formData = ref({
      message: null,
    });

    /**
     * Compute selling profile link
     */
    const sellerProfileLink = computed(() => {
      switch (product.value.selling_type) {
        case 'RIO':
          return `/rio/profile/introduction/${product.value.selling_id}`;
        case 'NEO':
          return `/neo/profile/introduction/${product.value.selling_id}`;
        default:
          return null;
      }
    });

    /**
     * Event listener for add url form submit
     *
     * @returns {void}
     */
    const handleSendInquiry = (event) => {
      event.preventDefault();

      const data = {
        classified_sale_id: product.value.id,
        selling_rio_id: product.value.selling_rio_id,
        selling_neo_id: product.value.selling_neo_id,
        message: formData.value.message,
      };

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;

      classifiedContactsApiService
        .sendInquiry(data)
        .then(() => {
          /* eslint no-undef: 0 */
          const modalCloseBtn = document.querySelector('.js-close');
          const sentModalNode = document.getElementById('sent-inquiry-modal');
          const sentModal = computed(() => new bootstrap.Modal(sentModalNode));

          modalCloseBtn.click();
          sentModal.value.show();

          emit('update-button');
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Render validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            modalLoading.value = false;
          }
        })
        .finally(() => {
          modalLoading.value = false;
        });
    };

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const resetModal = () => {
      errors.value = null;
      formData.value = {
        message: null,
      };
    };

    return {
      formData,
      formRef,
      errors,
      modalLoading,
      sellerProfileLink,
      handleSendInquiry,
      resetModal,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
