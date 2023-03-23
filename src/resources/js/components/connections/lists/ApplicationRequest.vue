<template>
  <div class="tab-pane fade active show">
    <div class="d-flex justify-content-between align-items-center mt-2">
      <p id="resultTotal" class="mb-0" style="flex: 1">
        {{ totalResults }}{{ $t('links.total_suffix') }}
      </p>
      <slot name="header" />
    </div>
    <div class="card py-2 px-4 shadow mt-2" id="connection-requests-list-items">
      <section-loader :show="listLoading" />
      <div class="connection__wrapper">
        <accept-modal
          ref="acceptModalRef"
          :request="currentRequest"
          @acceptRequest="acceptRequest"
        />
        <decline-modal
          ref="declineModalRef"
          :request="currentRequest"
          @declineRequest="declineRequest"
        />
        <message-modal ref="messageModalRef" :request="applicantMessage" />
        <base-alert
          :success="alert.success"
          :danger="alert.failed"
          :message="alert.message"
          @closeAlert="resetAlert"
        />
        <ul
          v-if="connectionRequests.length > 0"
          class="connection__lists list-group list-group-flush mt-2"
        >
          <li
            class="list-group-item px-0 py-2 position-relative list--white px-2"
            v-for="(request, index) in connectionRequests"
            :key="`${request.id}${index}`"
            @click.stop="redirectIntroduction(request)"
          >
            <img
              class="rounded-circle me-2 d-inline-block img--profile_image_sm"
              :src="request.profile_photo"
              :alt="request.name"
              @error="
                Common.handleNotFoundImageException(
                  $event,
                  DefaultImageCategory.RIO_NEO
                )
              "
              width="40"
            />
            <span class="fs-xs c-primary ms-2">
              {{ request.name }}
            </span>
            <div
              v-if="status"
              class="
                vertical-right
                d-flex
                align-items-center
                justify-content-center
              "
            >
              <button
                v-if="request.message != null"
                class="fs-xs btn btn-link p-2"
                @click.stop="messageRequestModal(request)"
              >
                <i class="ai-message-square"></i>
              </button>
              <a
                class="fs-xs btn btn-link p-2"
                @click.stop="acceptRequestModal(request)"
              >
                {{ $t('links.accept') }}
              </a>
              <a
                class="fs-xs btn btn-link p-2"
                @click.stop="declineRequestModal(request)"
              >
                {{ $t('links.decline') }}
              </a>
            </div>
          </li>
        </ul>
        <div v-else class="d-flex justify-content-center mt-3">
          {{ $t('paragraphs.there_is_no_application_request') }}
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-1 mb-3">
          <pagination :meta="paginationData" @changePage="changePage" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import ApplicationRequest from '../../../api/connection/application_request';
import AcceptModal from '../modals/AcceptModal.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import Common from '../../../common';
import DeclineModal from '../modals/DeclineModal.vue';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import MessageModal from '../modals/MessageModal.vue';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'ConnectionApplicationRequest',
  props: {
    image_path: {
      type: String,
      required: true,
    },
    status: {
      type: Boolean,
      required: true,
    },
    mode: {
      type: [Number, String],
      required: true,
    },
  },
  components: {
    AcceptModal,
    BaseAlert,
    DeclineModal,
    MessageModal,
    Pagination,
    SectionLoader,
  },
  setup(props) {
    const acceptModalRef = ref(null);
    const applicationRequestApi = new ApplicationRequest();
    const alert = ref({
      success: false,
      failed: false,
    });
    const applicationData = ref({});
    const connectionRequests = ref([]);
    const currentRequest = ref(null);
    const declineModalRef = ref(null);
    const listLoading = ref(false);
    const applicantMessage = ref(null);
    const messageModalRef = ref(null);
    const paginationData = ref([]);
    const totalResults = ref(0);
    const url = ref(null);

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
     * Reset all connection request modals
     *
     * @returns {void}
     */
    const resetModals = () => {
      acceptModalRef.value.hide();
      declineModalRef.value.hide();
      messageModalRef.value.hide();
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
     * Set current request process
     *
     * @param {object} request
     * @returns {void}
     */
    const setCurrentRequest = (request = null) => {
      currentRequest.value = request;
    };

    /**
     * Set new connectionRequests value
     *
     * @param {object} data
     * @returns {void}
     */
    const setConnectionRequests = (data) => {
      connectionRequests.value = connectionRequests.value.filter(
        (request) =>
          request.connection_id !== data.connection_id && request.id !== data.id
      );
    };

    /**
     * Set current request and open accept modal
     *
     * @param {object} request
     * @returns {void}
     */
    const acceptRequestModal = (request) => {
      setCurrentRequest(request);
      acceptModalRef.value.show();
    };

    /**
     * Set current request and open decline modal
     *
     * @param {object} request
     * @returns {void}
     */
    const declineRequestModal = (request) => {
      setCurrentRequest(request);
      declineModalRef.value.show();
    };

    /**
     * Open message modal
     *
     * @param {object} request
     * @returns {void}
     */
    const messageRequestModal = (request) => {
      applicantMessage.value = request.message;
      messageModalRef.value.show();
    };

    /**
     * Get list of applications
     *
     * @returns {void}
     */
    const getApplications = async () => {
      listLoading.value = true;
      const data = {
        mode: props.mode,
        page: applicationData.value.page,
      };

      try {
        const getApplicationsApi =
          await applicationRequestApi.getApplicationRequests(data);
        const getUserResponseData = getApplicationsApi.data;

        connectionRequests.value = getUserResponseData?.data || [];
        paginationData.value = getUserResponseData?.meta || [];
        totalResults.value = getUserResponseData?.meta?.total || 0;

        // Handle out of bounds page
        if (connectionRequests.value.length === 0 && totalResults.value > 0) {
          applicationData.value.page = null;
          getApplications();
        }
      } catch (error) {
        resetAlert();
        setAlert('failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    /**
     * Redirect to rio/neo introduction
     *
     * @param {object} request
     * @returns {void}
     */
    const redirectIntroduction = (request) => {
      if (request.service === 'RIO') {
        url.value = '/rio/profile/introduction/:id';
      }
      if (request.service === 'NEO') {
        url.value = '/neo/profile/introduction/:id';
      }
      url.value = url.value.replace(':id', request.id);
      window.location.href = url.value;
    };

    /**
     * Call accept connection API
     *
     * @param {object} data
     * @returns {void}
     */
    const acceptRequest = async (data) => {
      applicationRequestApi
        .acceptConnection(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            setConnectionRequests(data);
            setAlert('success', response.data.message);
          }
        })
        .catch(() => setAlert('failed'))
        .finally(() => {
          resetModals();
          getApplications();
        });
    };

    /**
     * Call decline connection API
     *
     * @param {object} data
     * @returns {void}
     */
    const declineRequest = async (data) => {
      applicationRequestApi
        .declineConnection(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            setConnectionRequests(data);
            setAlert('success', response.data.message);
          }
        })
        .catch(() => setAlert('failed'))
        .finally(() => {
          resetModals();
          getApplications();
        });
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

    /**
     * Update list on page change
     *
     * @returns {void}
     */
    const changePage = (page) => {
      applicationData.value.page = page;
      getApplications();
    };

    getApplications();

    return {
      acceptModalRef,
      acceptRequest,
      acceptRequestModal,
      alert,
      applicationData,
      changePage,
      Common,
      connectionRequests,
      currentRequest,
      declineModalRef,
      declineRequest,
      declineRequestModal,
      DefaultImageCategory,
      getApplications,
      listLoading,
      applicantMessage,
      messageModalRef,
      messageRequestModal,
      paginationData,
      redirectIntroduction,
      resetAlert,
      setCurrentRequest,
      totalResults,
    };
  },
};
</script>
