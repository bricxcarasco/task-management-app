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
            @click="hide"
          ></button>
        </div>
        <div class="modal-body">
          <div class="input-group">
            <input
              type="text"
              class="form-control"
              v-model="search"
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
import ConnectionListApiService from '../../../api/forms/connection-list';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
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
    const search = ref('');

    /* eslint no-undef: 0 */
    const modal = computed(() => new bootstrap.Modal(modalRef.value));

    /**
     * Show select connected RIO/NEO modal
     *
     * @returns {void}
     */
    const show = () => {
      modal.value.show();
    };

    /**
     * Hide select connected RIO/NEO modal
     *
     * @returns {void}
     */
    const hide = () => {
      modal.value.hide();
      search.value = '';
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
      const data = {
        page: applicationData.value.page,
      };
      await connectionListApi
        .getConnections(data)
        .then((response) => {
          connections.value = response.data.data;
          paginationData.value = response.data?.meta || [];
          totalResults.value = response.data?.meta?.total || 0;

          // Handle out of bounds page
          if (connections.value.length === 0 && totalResults.value > 0) {
            applicationData.value.page = null;
            getConnectionList();
          }
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
          hide();
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
     * Get search connected list
     *
     * @param {int} id
     * @returns {void}
     */
    const getSearchedConnectionList = async () => {
      setLoading(true);
      const searchData = ref({
        search: search.value,
        kana_search: search.value,
        page: applicationData.value.page,
      });
      await connectionListApi
        .getConnections(searchData.value)
        .then((response) => {
          connections.value = response.data.data;
          paginationData.value = response.data?.meta || [];
          totalResults.value = response.data?.meta?.total || 0;

          // Handle out of bounds page
          if (connections.value.length === 0 && totalResults.value > 0) {
            applicationData.value.page = null;
            getSearchedConnectionList();
          }
        })
        .catch((error) => {
          emit('set-alert', error.response.data);
          hide();
        })
        .finally(() => setLoading(false));
    };

    /**
     * Filter connected list
     *
     * @returns {void}
     */
    const handleClickSearch = () => {
      getSearchedConnectionList();
    };

    /**
     * Update list on page change
     *
     * @returns {void}
     */
    const changePage = (page) => {
      applicationData.value.page = page;
      getConnections();
    };

    /**
     * OnMounted - initialize modal eveent listener when modal closes
     */
    onMounted(() => {
      modalRef.value.addEventListener('hidden.bs.modal', () => {
        connections.value = {};
        setLoading(false);
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
      getSearchedConnectionList,
      connections,
      handleSelectedConnection,
      handleClickSearch,
      applicationData,
      paginationData,
      changePage,
      Common,
      DefaultImageCategory,
      search,
    };
  },
};
</script>
