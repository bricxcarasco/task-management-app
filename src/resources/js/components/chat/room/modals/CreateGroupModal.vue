<template>
  <div
    class="modal fade"
    id="create-neo-group-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />

        <form action="" @submit.prevent="submitForm" ref="formRef" novalidate>
          <div class="modal-header">
            <h4 class="modal-title">{{ $t('headers.create_group') }}</h4>
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
              <div class="col-12">
                <div class="mb-3">
                  <!-- Industry Input Field -->
                  <label class="form-label" for="registratin-industry-field">
                    {{ $t('labels.group_name') }}
                    <sup class="text-danger ms-1">*</sup>
                  </label>
                  <input type="hidden" name="id" v-model="formData.id" />
                  <input
                    v-model="formData.group_name"
                    class="form-control"
                    :class="errors?.data != null ? 'is-invalid' : ''"
                    type="text"
                    name="group_name"
                    ref="nameRef"
                    required
                  />
                  <div
                    v-show="errors?.data"
                    v-for="(error, index) in errors?.data"
                    :key="index"
                    class="invalid-feedback"
                  >
                    {{ error.message }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-success btn-shadow btn-sm" type="submit">
              {{ $t('buttons.create') }}
            </button>
            <button
              class="btn btn-secondary btn-shadow btn-sm"
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetModal"
            >
              {{ $t('buttons.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import ChatRoomApiService from '../../../../api/chat/room';
import HttpResponse from '../../../../enums/HttpResponse';
import SectionLoader from '../../../base/BaseSectionLoader.vue';

export default {
  name: 'CreateNeoGroupModal',
  props: {
    session: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    SectionLoader,
  },
  setup(props, { emit }) {
    const chatRoomApiService = new ChatRoomApiService();
    const errors = ref(null);
    const responseCode = HttpResponse;
    const modalRef = ref(null);
    const modalLoading = ref(false);

    // Initialize form data
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
     * Event listener for add industry form submit
     *
     * @returns {void}
     */
    const submitForm = () => {
      const isValid = ref(true);
      const neoId = formData.value.id || props.session.data.id;
      const requestBody = ref({
        group_name: formData.value.group_name,
      });

      // Reinitialize state
      modalLoading.value = true;
      errors.value = null;

      // Handle responses
      chatRoomApiService
        .createNeoGroup(neoId, requestBody.value)
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === responseCode.INVALID_PARAMETERS) {
            errors.value = responseData;
            modalLoading.value = false;
            isValid.value = false;
            return;
          }

          // Handle forbidden errors
          if (responseData.status_code === responseCode.FORBIDDEN) {
            resetModal();
          }

          emit('reset-list');
          emit('set-alert', 'failed');
        })
        .finally(() => {
          if (isValid.value) {
            window.location.href = `${window.location.protocol}//${window.location.host}/neo/profile/groups/${neoId}`;
          }
        });
    };

    return {
      formData,
      formRef,
      submitForm,
      resetModal,
      errors,
      modalLoading,
      modalRef,
    };
  },
};
</script>
