<template>
  <div
    class="modal fade"
    id="selectConnectionList"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <section-loader :show="modalLoading" />
        <div class="modal-header">
          <h4 class="modal-title">
            {{ $t('headers.select_recipient') }}
          </h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="input-group">
            <input
              type="text"
              class="form-control"
              v-model="searchFormInput.kana_search"
              :placeholder="$t('placeholders.search')"
              style="border-top-right-radius: 0; border-bottom-right-radius: 0"
            />
            <button
              type="button"
              @click="handleClickSearch"
              class="btn btn-primary"
            >
              {{ $t('buttons.search') }}
            </button>
          </div>
          <div v-if="connections.length !== 0">
            <p class="mb-2">
              {{ connections.length }}{{ $t('links.total_suffix') }}
            </p>
            <ul class="list-group list-group-flush">
              <span v-for="connection in connections" :key="connection.id">
                <li
                  class="
                    list-group-item
                    px-0
                    py-2
                    position-relative
                    list--white
                    px-2
                  "
                  @click.prevent="handleSelectedConnection(connection)"
                >
                  <img
                    class="
                      rounded-circle
                      me-2
                      d-inline-block
                      img--profile_image_sm
                    "
                    :src="connection.profile_photo"
                    :alt="connection.name"
                    @error="
                      Common.handleNotFoundImageException(
                        $event,
                        DefaultImageCategory.RIO_NEO
                      )
                    "
                    width="40"
                  />
                  <span class="fs-xs c-primary ms-2">
                    {{ connection.name }}
                  </span>
                </li>
              </span>
            </ul>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-1 mb-3">
              <pagination :meta="paginationData" @changePage="changePage" />
            </div>
          </div>
          <div v-else>
            <p class="text-center p-2 mb-0">
              {{ $t('paragraphs.there_is_no_connection') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import ConnectionListApiService from '../../../api/electronic_contract/connection-list';
import ServiceSelectionTypesEnum from '../../../enums/ServiceSelectionTypes';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import Pagination from '../../base/BasePagination.vue';

export default {
  name: 'ConnectionListModal',
  components: {
    SectionLoader,
    Pagination,
  },
  props: {
    service: {
      type: [Object, null],
      required: true,
    },
  },
  setup(props, { emit }) {
    const connectionListApi = new ConnectionListApiService();
    const modalRef = ref(null);
    const modalLoading = ref(false);
    const connections = ref({});
    const paginationData = ref([]);
    const totalResults = ref(0);
    const applicationData = ref({});
    const searchFormInput = ref({});
    const searchData = ref({});

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show select recipient modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide select recipient modal
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
     * Get connected services to current user
     *
     * @param {int} id
     * @returns {void}
     */
    const getConnectionList = async () => {
      setLoading(true);

      await connectionListApi
        .getConnections(searchData.value)
        .then((response) => {
          const responseData = response.data;
          connections.value = responseData?.data || [];
          paginationData.value = responseData?.meta || [];
          totalResults.value = responseData?.meta?.total || 0;

          // Handle out of bounds page
          if (connections.value.length === 0 && totalResults.value > 0) {
            applicationData.value.page = null;
            getConnectionList();
          }
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
          modal.value.hide();
        })
        .finally(() => setLoading(false));
    };

    /**
     * Trigger set recipient and emit to parent component
     *
     * @param {Object} connection
     * @returns {void}
     */
    const handleSelectedConnection = (connection) => {
      setLoading(true);
      emit('choose-target-connection', connection);
    };

    /**
     * Filter connected list
     *
     * @returns {void}
     */
    const handleClickSearch = () => {
      searchData.value = { ...searchFormInput.value };
      searchData.value.page = null;
      getConnectionList();
    };

    /**
     * Update list on page change
     *
     * @returns {void}
     */
    const changePage = (page) => {
      searchData.value.page = page;
      getConnectionList();
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        connections.value = {};
        searchFormInput.value = {};
        searchData.value = {};
        setLoading(false);
      });
      modalRef.value.addEventListener('shown.bs.modal', () => {
        getConnectionList();
      });
    });

    return {
      ServiceSelectionTypesEnum,
      modalRef,
      modal,
      modalLoading,
      show,
      hide,
      setLoading,
      getConnectionList,
      connections,
      handleSelectedConnection,
      handleClickSearch,
      applicationData,
      paginationData,
      changePage,
      searchFormInput,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
