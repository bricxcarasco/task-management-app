<template>
  <div
    class="modal fade"
    id="removeAdministrator"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('links.release_as_an_administrator') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body text-center">
          <div
            class="
              modal__imgWrapper
              text-center
              d-flex
              align-items-center
              justify-content-center
              flex-column
              py-2
              w-50
              mx-auto
            "
          >
            <img
              class="rounded-circle d-block mx-auto img--profile_image_md"
              :src="data.profile_image"
              :alt="data.full_name"
              width="80"
            />
            <p class="mt-2 mb-0">
              {{ data.full_name }}
            </p>
          </div>
          <p class="mt-4 mb-0">
            {{ $t('messages.neo.remove_administrator_prompt') }}
          </p>
          <button
            class="btn btn-primary mt-4"
            type="button"
            @click="handleClick"
          >
            {{ $t('links.release_as_an_administrator') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import NeoBelongRoleType from '../../../../enums/NeoBelongRoleType';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  props: {
    data: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  emits: ['removeAdministrator'],
  setup(props, { emit }) {
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
     * Trigger remove administrator button and emit to parent component
     *
     * @returns {void}
     */
    const handleClick = () => {
      const data = {
        neo_id: props.data.neo_id,
        rio_id: props.data.rio_id,
        type: NeoBelongRoleType.MEMBER,
      };
      setLoading(true);
      emit('removeAdministrator', data);
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
      modalLoading,
      show,
      hide,
      handleClick,
    };
  },
};
</script>
