<template>
  <div>
    <connect-modal
      :rio="rio"
      @handleConnectionRequest="connectRequestRio"
      ref="connectModalRef"
    />
    <connect-dialog-modal :rio="rio" ref="connectDialogModalRef" />
    <cancel-modal @handleUpdate="updateConnectionStatus" ref="cancelModalRef" />
    <disconnect-modal
      @handleUpdate="updateConnectionStatus"
      ref="disconnectModalRef"
    />
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />
    <base-button
      v-if="showButton"
      class="mt-2"
      :title="buttonText"
      :buttonType="'primary'"
      @handleClick="handleButtonClick"
    />
  </div>
</template>

<script>
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import RioConnectionStatus from '../../../enums/RioConnectionStatus';
import ServiceType from '../../../enums/ServiceSelectionTypes';
import RioConnectionApi from '../../../api/rio/connection';
import BaseAlert from '../../base/BaseAlert.vue';
import BaseButton from '../../base/BaseButton.vue';
import ConnectModal from './modals/ConnectModal.vue';
import CancelModal from './modals/CancelModal.vue';
import DisconnectModal from './modals/DisconnectModal.vue';
import ConnectDialogModal from './modals/ConnectDialogModal.vue';
import NeoConnectionStatus from '../../../enums/NeoConnectionStatus';

export default {
  name: 'RioConnections',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    status: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: Object,
      required: true,
    },
    neo_status: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  components: {
    BaseAlert,
    BaseButton,
    ConnectModal,
    CancelModal,
    DisconnectModal,
    ConnectDialogModal,
  },
  setup(props) {
    /**
     * Data properties
     */
    const rioConnection = new RioConnectionApi();
    const rioConnectionStatus = RioConnectionStatus;
    const neoConnectionStatus = NeoConnectionStatus;
    const serviceType = ServiceType;
    const connectionStatus = ref(props.status.status);
    const isApplicant = ref(props.status.is_applicant);
    const isRioRequestedToNeo = ref(props.status.is_rio_requested_to_neo);
    const neoCurrentStatus = ref(props.neo_status);
    const connectModalRef = ref(null);
    const disconnectModalRef = ref(null);
    const cancelModalRef = ref(null);
    const connectDialogModalRef = ref(null);
    const serviceSession = ref(props.service);
    const showButton = ref(true);
    const alert = ref({
      success: false,
      failed: false,
    });
    const i18n = useI18n();
    /**
     * Reset alert values
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set button status
     *
     */
    if (
      (connectionStatus.value === rioConnectionStatus.CANCELLATION &&
        !isApplicant.value) ||
      isRioRequestedToNeo.value
    ) {
      showButton.value = false;
    }

    /**
     * Reset all connection modals
     *
     * @returns {void}
     */
    const resetModals = () => {
      connectModalRef.value.hide();
      disconnectModalRef.value.hide();
      cancelModalRef.value.hide();
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
     * Computed property of connection button based on connection status
     *
     * @returns {object}
     */
    const buttonText = computed(() => {
      let text = i18n.t('buttons.connect_application');
      if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.DISCONNECTION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.AFFLIATED)
      ) {
        text = i18n.t('buttons.disconnection');
      } else if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.CANCELLATION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.CHECK) ||
        neoCurrentStatus.value === neoConnectionStatus.APPLYING
      ) {
        text = i18n.t('buttons.cancel_connection');
      }
      return text;
    });

    /**
     * Alert message value when cancel or disconnect application
     *
     * @returns {string}
     */
    const cancelDisconnectAlertMessage = () => {
      let $message = '';
      if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.DISCONNECTION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.AFFLIATED) ||
        neoCurrentStatus.value === neoConnectionStatus.CHECK
      ) {
        $message = i18n.t('alerts.disconnected_rio_connection');
      } else if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.CANCELLATION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.APPLYING) ||
        neoCurrentStatus.value === neoConnectionStatus.CHECK
      ) {
        $message = i18n.t('alerts.cancelled_rio_connection');
      }
      return $message;
    };

    /**
     * Open connection dialog modal upon clicking connect modal -> confirm
     *
     * @returns {void}
     */
    const openConnectDialogModal = () => {
      if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.CANCELLATION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.APPLYING) ||
        neoCurrentStatus.value === neoConnectionStatus.CHECK
      ) {
        connectDialogModalRef.value.show();
        connectModalRef.value.setLoading(false);
      }
    };

    /**
     * Check what connection modal should appear
     *
     * @returns {void}
     */
    const handleButtonClick = () => {
      if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.DISCONNECTION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.AFFLIATED)
      ) {
        disconnectModalRef.value.show();
      } else if (
        (serviceSession.value.type === serviceType.RIO &&
          connectionStatus.value === rioConnectionStatus.CANCELLATION) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.APPLYING) ||
        (serviceSession.value.type === serviceType.NEO &&
          neoCurrentStatus.value === neoConnectionStatus.CHECK)
      ) {
        cancelModalRef.value.show();
      } else {
        connectModalRef.value.show();
      }
    };

    /**
     * Apply connection auth RIO to other RIO
     *
     * @returns {void}
     */
    const connectRio = async () => {
      await rioConnection
        .connect({ rio_id: props.rio.id })
        .then((response) => {
          if (response.data.status_code === 200) {
            if (serviceSession.value.type === 'RIO') {
              isApplicant.value = true;
              connectionStatus.value = response.data.data.connection_status;
            } else {
              neoCurrentStatus.value = response.data.data.connection_status;
            }
            openConnectDialogModal();
          }
        })
        .catch(() => setAlert('failed'))
        .finally(() => resetModals());
    };

    /**
     * Cancel or disconnect auth RIO to other RIO
     *
     * @returns {void}
     */
    const cancelDisconnectRio = async () => {
      await rioConnection
        .cancelDisconnect({ rio_id: props.rio.id })
        .then((response) => {
          if (response.data.status_code === 200) {
            setAlert('success', cancelDisconnectAlertMessage());
            if (serviceSession.value.type === 'RIO') {
              connectionStatus.value =
                response.data.data.connection_status.status;
            } else {
              neoCurrentStatus.value = response.data.data.connection_status;
            }
          }
        })
        .catch(() => setAlert('failed'))
        .finally(() => resetModals());
    };

    /**
     * Update connection records based of connection status
     *
     * @returns {void}
     */
    const updateConnectionStatus = () => {
      resetAlert();
      if (serviceSession.value.type === serviceType.RIO) {
        if (
          connectionStatus.value === rioConnectionStatus.DISCONNECTION ||
          connectionStatus.value === rioConnectionStatus.CANCELLATION
        ) {
          cancelDisconnectRio();
        } else {
          connectRio();
        }
      } else if (serviceSession.value.type === serviceType.NEO) {
        if (
          neoCurrentStatus.value === neoConnectionStatus.AFFLIATED ||
          neoCurrentStatus.value === neoConnectionStatus.APPLYING ||
          neoCurrentStatus.value === neoConnectionStatus.CHECK
        ) {
          cancelDisconnectRio();
        } else {
          connectRio();
        }
      }
    };

    /**
     * Handle successful connection request
     *
     * @returns {void}
     */
    const connectRequestRio = (status) => {
      if (serviceSession.value.type === 'RIO') {
        isApplicant.value = true;
        connectionStatus.value = status;
      } else {
        neoCurrentStatus.value = status;
      }
      openConnectDialogModal();
    };

    /**
     * Watch on alert changes
     */
    watch(alert.value, () => {
      if (alert.value.success || alert.value.failed) {
        setTimeout(() => {
          resetAlert();
        }, 3000);
      }
    });

    return {
      rioConnectionStatus,
      connectionStatus,
      serviceType,
      buttonText,
      handleButtonClick,
      openConnectDialogModal,
      updateConnectionStatus,
      connectModalRef,
      connectDialogModalRef,
      disconnectModalRef,
      cancelModalRef,
      alert,
      resetAlert,
      serviceSession,
      isApplicant,
      showButton,
      connectRequestRio,
    };
  },
};
</script>
