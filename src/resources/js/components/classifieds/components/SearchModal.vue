<template>
  <div
    class="modal fade"
    id="classified-search-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="" @submit.prevent="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">{{ $t('headers.product_search') }}</h4>
            <button
              class="btn-close"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <div class="position-relative">
                <i class="ai-search input-search"></i>
                <input
                  v-model="formData.keyword"
                  class="form-control ps-5"
                  type="text"
                  id="text-input"
                  :placeholder="$t('placeholders.search_keyword')"
                />
              </div>
            </div>
            <div class="mb-2">
              <select
                class="form-select"
                v-model="formData.salesCategory"
                id="select-input"
              >
                <option value="">{{ $t('labels.unselected') }}</option>
                <option
                  v-for="(value, key) in sales_category_list"
                  :key="key"
                  :value="value.id"
                >
                  {{ value.sale_category_name }}
                </option>
              </select>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">
                {{ $t('buttons.search') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, defineComponent, onMounted } from 'vue';

export default defineComponent({
  name: 'ClassifiedSearchModal',
  props: {
    keyword: {
      type: String,
      default: null,
    },
    sales_category: {
      type: [String, Number],
      default: null,
    },
    sales_category_list: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props, { emit }) {
    const formData = ref({});
    const formRef = ref(null);
    const modalRef = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const closeModal = () => {
      modalRef.value.querySelector('.btn-close').click();
    };

    /**
     * Event listener for submitting search keyword
     *
     * @returns {void}
     */
    const submitForm = () => {
      let { keyword } = formData.value;
      let { salesCategory } = formData.value;

      // Trim search input
      if (keyword !== '' && keyword !== undefined && keyword !== null) {
        keyword = keyword.trim();
      }

      // Set default value for sales category
      if (salesCategory === undefined || salesCategory === '') {
        salesCategory = null;
      }

      emit('set-Search', keyword, salesCategory);
      closeModal();
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        // Set initial keyword to form
        formData.value.keyword = props.keyword;
        formData.value.salesCategory = props.sales_category ?? '';
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
      modalRef,
      formRef,
      formData,
      closeModal,
      submitForm,
    };
  },
});
</script>
