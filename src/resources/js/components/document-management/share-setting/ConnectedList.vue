<template>
  <div>
    <p>■ {{ $t('labels.add_connections_to_share') }}</p>
    <div class="input-group">
      <input
        class="form-control text-center"
        type="text"
        v-model="searchFormInput.name"
      />
      <button
        class="btn btn-link border btn-icon"
        type="button"
        @click="clearSearch"
      >
        <i class="ai-x"></i>
      </button>
    </div>
    <p class="mt-2 mb-2">{{ $t('labels.connection_participation_group') }}</p>
    <div
      class="card p-4 shadow mt-2"
      id="connection-requests-list-items"
      v-if="connectedLists.length > 0"
    >
      <div class="sharing__container border">
        <section-loader :show="listLoading" />
        <ul class="list-group list-group-flush">
          <li
            class="list-group-item px-0 py-2 position-relative list--white px-2"
            v-for="(request, index) in connectedLists"
            :key="`${index}`"
            @click.stop="checkService(request.service, request.id)"
          >
            <img
              class="rounded-circle me-2 d-inline-block img--profile_image_sm"
              :src="`${request.profile_photo}`"
              :alt="request.name"
              width="40"
              @error="
                Common.handleNotFoundImageException(
                  $event,
                  DefaultImageCategory.RIO_NEO
                )
              "
            />
            <span class="fs-xs c-primary ms-2">
              {{ request.name }}
            </span>
            <div class="vertical-right">
              <button class="btn btn-link">
                <i
                  class="h2 m-0 ai-check"
                  :id="`${request.service}-${request.id}`"
                  style="display: none"
                ></i>
              </button>
            </div>
            <span
              v-if="['NEO', 'NEO_BELONG'].includes(request.service)"
              class="bg-gray fs-xs participating"
            >
              {{ $t('labels.participating_neo') }}
            </span>
            <span
              v-if="request.service === 'NEO_Group'"
              class="bg-gray fs-xs participating"
            >
              {{ $t('labels.neo_group') }}
            </span>
          </li>
        </ul>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-1 mb-3">
          <pagination :meta="paginationData" @changePage="changePage" />
        </div>
      </div>
      <div class="text-center mt-2">
        <button class="btn btn-primary" @click="handleShareSetting">
          {{ $t('buttons.share') }}
        </button>
      </div>
    </div>
    <div
      class="card p-4 shadow mt-2"
      id="connection-requests-list-items"
      v-else
    >
      <section-loader :show="listLoading" />
      {{ $t('messages.document_management.no_available_connected') }}
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import HttpResponse from '../../../enums/HttpResponse';
import i18n from '../../../i18n';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import ShareSettingRequest from '../../../api/document_management/share-setting';

export default {
  name: 'ConnectedList',
  props: {
    document_id: {
      type: [Number, String],
      required: true,
    },
  },
  components: {
    Pagination,
    SectionLoader,
  },
  setup(props, { emit }) {
    const shareSettingRequestApi = new ShareSettingRequest();
    const applicationData = ref({});
    const connectedLists = ref([]);
    const listLoading = ref(false);
    const paginationData = ref([]);
    const totalResults = ref(0);
    const searchFormInput = ref({});
    const checkedRios = ref([]);
    const checkedNeos = ref([]);
    const checkedNeoGroups = ref([]);
    const search = ref(0);
    const index = ref(0);
    const errors = ref(null);
    const isChecked = ref(0);
    const fileURL = `${window.location.protocol}//${window.location.host}/`;
    const responseCode = HttpResponse;

    /**
     * Clear search bar
     *
     * @returns {void}
     */
    const clearSearch = () => {
      searchFormInput.value.name = null;
    };

    /**
     * Get list of connected services without share access to document
     *
     * @returns {void}
     */
    const getConnectedListItems = async (status = false) => {
      listLoading.value = true;
      const data = {
        id: props.document_id,
        search: searchFormInput.value.name,
        page: applicationData.value.page,
      };

      try {
        const getConnectedListApi =
          await shareSettingRequestApi.getConnectedList(data.id, data);
        const getUserResponseData = getConnectedListApi.data;

        connectedLists.value = getUserResponseData?.data || [];
        paginationData.value = getUserResponseData?.meta || [];
        totalResults.value = getUserResponseData?.meta?.total || 0;

        // Handle out of bounds page
        if (connectedLists.value.length === 0 && totalResults.value > 0) {
          applicationData.value.page = null;
          getConnectedListItems();
        }
      } catch (error) {
        emit('reset-alert');
        emit('set-alert', 'failed');
        throw error;
      } finally {
        listLoading.value = false;

        // Reset check marks every modal open
        if (status) {
          const checkMark = document.querySelectorAll('.ai-check');
          checkMark.forEach((value) => {
            const iconNode = value;
            iconNode.style.display = 'none';
          });
        }
      }
    };

    /**
     * Add/remove selected services to be given share access
     *
     * @param {string} service
     * @param {int} id
     * @returns {void}
     */
    const checkService = (service, id) => {
      search.value = (element) => element === id;
      isChecked.value = document.querySelector(`#${service}-${id}`);
      if (service === 'RIO' || service === 'NEO_BELONG') {
        index.value = checkedRios.value.findIndex(search.value);
        if (index.value === -1) {
          isChecked.value.style.display = 'block';
          checkedRios.value.push(id);
        } else {
          isChecked.value.style.display = 'none';
          checkedRios.value.splice(index.value, 1);
        }
      }
      if (service === 'NEO') {
        index.value = checkedNeos.value.findIndex(search.value);
        if (index.value === -1) {
          isChecked.value.style.display = 'block';
          checkedNeos.value.push(id);
        } else {
          isChecked.value.style.display = 'none';
          checkedNeos.value.splice(index.value, 1);
        }
      }
      if (service === 'NEO_Group') {
        index.value = checkedNeoGroups.value.findIndex(search.value);
        if (index.value === -1) {
          isChecked.value.style.display = 'block';
          checkedNeoGroups.value.push(id);
        } else {
          isChecked.value.style.display = 'none';
          checkedNeoGroups.value.splice(index.value, 1);
        }
      }
    };

    /**
     * Create records for selected RIOs, NEOs, and NEO Groups
     */
    const handleShareSetting = async () => {
      const data = {
        id: props.document_id,
        rio_id: checkedRios.value,
        neo_id: checkedNeos.value,
        neo_group_id: checkedNeoGroups.value,
      };

      // Begin loading
      emit('update-modal-loading', true);

      await shareSettingRequestApi
        .saveShareSetting(data)
        .then(() => {
          emit('reset-alert');
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.document_management.folder_shared')
          );
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === responseCode.INVALID_PARAMETERS) {
            errors.value = responseData;
            return;
          }

          // Handle forbidden errors
          if (responseData.status_code === responseCode.FORBIDDEN) {
            emit('reset-modal');
          }

          emit('set-alert', 'failed');
        })
        .finally(() => {
          emit('update-modal-loading', false);

          // Reset array containers
          checkedRios.value = [];
          checkedNeos.value = [];
          checkedNeoGroups.value = [];
          emit('reset-modal');
        });
    };

    /**
     * Watch on search input change
     */
    watch(
      () => searchFormInput.value.name,
      () => {
        getConnectedListItems();
      }
    );

    /**
     * Watch on document_id change
     */
    watch(
      () => props.document_id,
      () => {
        if (props.document_id !== 0) {
          // Reset on modal open
          searchFormInput.value.name = null;
          checkedRios.value = [];
          checkedNeos.value = [];
          checkedNeoGroups.value = [];
          getConnectedListItems(true);
        }
      }
    );

    /**
     * Update list on page change
     *
     * @returns {void}
     */
    const changePage = (page) => {
      applicationData.value.page = page;
      getConnectedListItems();
    };

    return {
      applicationData,
      changePage,
      checkedNeoGroups,
      checkedNeos,
      checkedRios,
      checkService,
      clearSearch,
      connectedLists,
      fileURL,
      getConnectedListItems,
      handleShareSetting,
      isChecked,
      listLoading,
      paginationData,
      searchFormInput,
      totalResults,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
