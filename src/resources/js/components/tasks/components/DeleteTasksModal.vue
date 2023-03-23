<template>
  <div
    class="modal fade"
    id="deleteTasksModal"
    aria-hidden="true"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('headers.task_delete') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 text-center">
              {{ $t('messages.tasks.confirm_deletion_selected_task') }}
              <input type="hidden" name="id" />
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button
            class="btn btn-danger btn-shadow btn-sm"
            type="button"
            @click="$emit('delete-tasks')"
          >
            {{ $t('buttons.delete') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'DeleteTasksModal',
  components: {
    SectionLoader,
  },
  setup() {
    const modalRef = ref(null);
    const modalLoading = ref(false);

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show cancel conection modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide cancel conection modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
    };

    /**
     * Set modal loading
     *
     * @returns {void}
     */
    const setLoading = (value) => {
      modalLoading.value = value;
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
      });
    });

    return {
      modalRef,
      modal,
      setLoading,
      modalLoading,
      show,
      hide,
    };
  },
};
</script>
