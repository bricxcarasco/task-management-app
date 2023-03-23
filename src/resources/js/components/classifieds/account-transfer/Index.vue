<template>
  <div>
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />
    <!-- Account form modal -->
    <account-form-modal
      @get-setting="getSetting"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      @add-account="addAccount"
      @edit-account="editAccount"
    />
    <!-- Delete form modal -->
    <delete-setting-modal
      @get-setting="getSetting"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      @delete-account="deleteAccount"
    />
    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <page-loader :show="pageLoading" />
        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div class="position-relative">
              <h3 class="py-3 mb-0 text-center">
                {{
                  $t('headers.service_owned_classifieds', { name: serviceName })
                }}
              </h3>
            </div>
            <div>
              <button class="btn btn-link" @click="redirectBackToReceivingPage">
                <i class="ai-arrow-left me-2">{{ $t('labels.return') }}</i>
              </button>
            </div>
            <ul class="mt-4">
              <li>{{ $t('labels.please_register_the_account') }}</li>
              <li>{{ $t('labels.you_can_register_multiple_accounts') }}</li>
              <li>
                {{ $t('labels.please_enter_the_XX_of_the_created_account') }}
              </li>
            </ul>
            <bank-item
              v-for="(settings, index) in settings"
              :key="index"
              :item_count="index"
              :account_index="index"
              :settings="settings"
            />
            <div class="text-end" v-if="addedAccount < 3">
              <button
                class="btn btn-link"
                id="addAccount"
                @click="handleOnCreate"
              >
                <i class="ai-plus"></i>{{ $t('labels.add_account') }}
              </button>
            </div>
            <div class="text-center m-4">
              <button class="btn btn-primary" @click="handleOnClickSave">
                {{ $t('buttons.save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import BankItem from './Item.vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import ClassifiedSettingsApiService from '../../../api/classifieds/settings';
import BaseAlert from '../../base/BaseAlert.vue';
import AccountFormModal from './FormModal.vue';
import DeleteSettingModal from './DeleteModal.vue';
import AccountTransferOperation from '../../../enums/AccountTransferOperation';

export default defineComponent({
  name: 'AccountTransferIndex',
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    BankItem,
    AccountFormModal,
    DeleteSettingModal,
  },
  setup(props) {
    /**
     * Data properties
     */
    const classifiedSettingsApiService = new ClassifiedSettingsApiService();
    const service = ref(props.service);
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const settings = ref({});
    const isExceededLimit = ref(false);
    const addedAccount = ref(0);
    let formModalNode = ref(null);
    let formModal = ref(null);
    let operationNode = ref(null);

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Set alert messages
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
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Update account count
     *
     * @returns {void}
     */
    const updateCount = () => {
      addedAccount.value = settings.value.length;
    };

    /**
     * Register account function
     *
     * @returns {void}
     */
    const addAccount = (data) => {
      if (!settings.value) {
        settings.value = data;
      } else {
        settings.value.push(data);
      }

      updateCount();
    };

    /**
     * Edit account function
     *
     * @returns {void}
     */
    const editAccount = (data, index) => {
      settings.value[index] = data;
    };

    /**
     * Delete account function
     *
     * @returns {void}
     */
    const deleteAccount = (index) => {
      settings.value.splice(index, 1);
      updateCount();
    };

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case 'RIO':
          return data.full_name;
        case 'NEO':
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Redirect back to receiving account setting page
     *
     * @returns {void}
     */
    const redirectBackToReceivingPage = () => {
      window.location.href = `/classifieds/settings`;
    };

    /**
     * Get settings
     *
     * @returns {void}
     */
    const getSetting = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getSettingsApi = await classifiedSettingsApiService.getSettings();
        const getListResponseData = getSettingsApi.data;
        settings.value = getListResponseData?.data || [];
        addedAccount.value = getListResponseData.data.length;
      } catch (error) {
        return;
      } finally {
        setPageLoading(false);
      }
    };

    /**
     * Create modal
     */
    const handleOnCreate = () => {
      operationNode.value = AccountTransferOperation.ADD;
      formModal.value.show();
    };

    /**
     * Save account details
     */
    const handleOnClickSave = async () => {
      setPageLoading(true);
      resetAlert();
      try {
        await classifiedSettingsApiService
          .saveAccountDetails(settings.value)
          .then(() => {
            window.location.href = `/classifieds/settings`;
          });
      } catch (error) {
        setAlert('failed');
        throw error;
      } finally {
        setPageLoading(false);
      }
    };

    /**
     * On mounted properties
     */
    onMounted(() => {
      /* eslint no-undef: 0 */
      getSetting();
      operationNode = document.querySelector(
        "form input#operation[type='hidden']"
      );
      formModalNode = document.getElementById('account-form-modal');
      formModal = computed(() => new bootstrap.Modal(formModalNode));
    });

    return {
      serviceName,
      redirectBackToReceivingPage,
      getSetting,
      setAlert,
      resetAlert,
      alert,
      pageLoading,
      setPageLoading,
      settings,
      isExceededLimit,
      handleOnCreate,
      addAccount,
      addedAccount,
      editAccount,
      handleOnClickSave,
      deleteAccount,
      updateCount,
    };
  },
});
</script>
