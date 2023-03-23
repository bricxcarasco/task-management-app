<template>
  <div
    class="modal fade"
    id="create-menu-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.create_new') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="list-group list-group-flush">
            <a
              class="list-group-item"
              @click="openModal('#create-folder-modal')"
              >{{ $t('headers.create_knowledge_folder') }}</a
            >
            <a
              @click="createArticle('/knowledges/articles/create')"
              class="list-group-item"
              >{{ $t('headers.create_knowledge_article') }}</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'CreateMenuModal',
  props: {
    directory_id: {
      type: Number,
      default: null,
    },
  },
  components: {},
  setup(props, { emit }) {
    const modalRef = ref(null);

    /**
     * Closes modal and reset state
     *
     * @returns {void}
     */
    const openModal = (modalId) => {
      modalRef.value.querySelector('.btn-close').click();
      emit('open-modal', modalId);
    };

    /**
     * Redirect to create article page
     *
     * @returns {void}
     */
    const createArticle = (url) => {
      if (props.directory_id === null) {
        window.location.href = url;
      } else {
        window.location.href = `${url}/${props.directory_id}`;
      }
    };

    return {
      openModal,
      modalRef,
      createArticle,
    };
  },
};
</script>
