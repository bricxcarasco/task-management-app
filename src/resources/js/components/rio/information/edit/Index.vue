<template>
  <div>
    <!-- Password confirmation modal -->
    <password-confirm-modal
      ref="passwordConfirmRef"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
      <page-loader :show="pageLoading" />

      <base-alert
        :success="alert.success"
        :danger="alert.failed"
        :message="alert.message"
        @closeAlert="resetAlert"
      />
      <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
          <div class="d-flex flex-column h-100 bg-light rounded-3 shadow-lg">
            <div class="border-bottom">
              <h3 class="p-3 mb-0 text-center">
                {{ $t('headers.information_edit') }}
              </h3>
            </div>
            <div class="p-md-3">
              <div id="accordionExample" class="accordion accordion-no-radius">
                <base-accordion
                  v-for="info in infoList"
                  :key="info.id"
                  :index="info.id"
                  :title="info.title"
                  @accordionClick="manageAccordions"
                >
                  <!-- Email Address Section -->
                  <div v-if="info.id == 1">
                    <email-section
                      :rio="editUser"
                      :password_confirm="passwordConfirmRef"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-page-loading="setPageLoading"
                    />
                  </div>

                  <!-- Password Section -->
                  <div v-else-if="info.id == 2">
                    <password-section
                      :user="editUser"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-page-loading="setPageLoading"
                    />
                  </div>

                  <!-- Secret Question Section -->
                  <div v-else-if="info.id == 3">
                    <secret-question
                      :user="editUser"
                      :password_confirm="passwordConfirmRef"
                      :secretQuestions="secret_questions"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-page-loading="setPageLoading"
                    />
                  </div>
                </base-accordion>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import BaseAccordion from '../../../base/BaseAccordion.vue';
import BaseAlert from '../../../base/BaseAlert.vue';
import PasswordConfirmModal from '../../modals/PasswordConfirmModal.vue';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import SecretQuestion from './SecretQuestion.vue';
import { RioInformationEditInfoList } from '../../../../utils/data';
import EmailSection from './email/Section.vue';
import PasswordSection from './password/Section.vue';
import BpheroConfig from '../../../../config/bphero';

export default defineComponent({
  name: 'RioInformationEdit',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    secret_questions: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAccordion,
    BaseAlert,
    PageLoader,
    PasswordConfirmModal,
    EmailSection,
    SecretQuestion,
    PasswordSection,
  },
  setup(props) {
    /**
     * Data properties
     */

    const loading = ref(false);
    const editUser = ref(props.user);
    const editId = ref(0);
    const infoList = ref(RioInformationEditInfoList);
    const pageLoading = ref(false);
    const errors = ref(null);
    const passwordConfirmRef = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });

    /**
     * Computed properties
     */
    const profileImage = computed(() => BpheroConfig.DEFAULT_IMG);
    const imageAltName = computed(
      () => `${editUser.first_name} ${editUser.family_name}`
    );
    /**
     * Manage accordions
     */
    const manageAccordions = (id) => {
      errors.value = null;
      editId.value = id;
    };

    /**
     * Reset alert messages
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status = 'success', message = null) => {
      const statusValue = status === 'success' ? 'success' : 'failed';
      alert.value[statusValue] = true;
      alert.value.message = message;
    };

    /**
     * Set loading status
     *
     * @param {string} status
     * @returns {void}
     */
    const setLoading = (status) => {
      loading.value = status;
    };

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    return {
      setAlert,
      resetAlert,
      setLoading,
      setPageLoading,
      alert,
      editUser,
      infoList,
      pageLoading,
      errors,
      manageAccordions,
      passwordConfirmRef,
      profileImage,
      imageAltName,
    };
  },
});
</script>
