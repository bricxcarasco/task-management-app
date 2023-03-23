<template>
  <div
    class="modal fade"
    id="task-search-modal"
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
            <h4 class="modal-title">{{ $t('headers.task_search') }}</h4>
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
  name: 'TaskSearchModal',
  props: {
    keyword: {
      type: String,
      default: null,
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

      // Trim search input
      if (keyword !== '' && keyword !== undefined && keyword !== null) {
        keyword = keyword.trim();
      }

      emit('set-keyword', keyword);
      closeModal();
    };

    /**
     * Attach event listener for showing modal
     */
    const attachShowModalListener = () => {
      modalRef.value.addEventListener('show.bs.modal', () => {
        // Set initial keyword to form
        formData.value.keyword = props.keyword;
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
