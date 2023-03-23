<template>
  <div>
    <p class="mt-2">■ {{ $t('labels.shared') }}</p>
    <div class="card p-4 shadow mt-2" id="connection-requests-list-items">
      <section-loader :show="listLoading" />
      <div class="sharing__container border" v-if="permittedLists.length > 0">
        <ul class="list-group list-group-flush border">
          <li
            class="list-group-item px-0 py-2 position-relative list--white px-2"
            v-for="(request, index) in permittedLists"
            :key="`${index}`"
          >
            <img
              class="rounded-circle me-2 d-inline-block img--profile_image_sm"
              :src="`${request.profile_photo}`"
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
            <div class="vertical-right">
              <p class="fs-xs m-0 pe-2" v-if="request.ownership === 'OWNER'">
                {{ $t('labels.owner') }}
              </p>
              <button
                type="button"
                class="btn btn-link fs-xs pe-2"
                v-else
                @click="handleUnshare(request)"
              >
                {{ $t('buttons.unshare') }}
              </button>
            </div>
          </li>
        </ul>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-1 mb-3">
          <pagination :meta="paginationData" @changePage="changePage" />
        </div>
      </div>
      <div v-else>
        {{ $t('messages.document_management.no_shared_services') }}
      </div>
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
  name: 'PermittedList',
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
    const permittedLists = ref([]);
    const listLoading = ref(false);
    const paginationData = ref([]);
    const totalResults = ref(0);
    const fileURL = `${window.location.protocol}//${window.location.host}/`;
    const errors = ref(null);
    const responseCode = HttpResponse;

    /**
     * Get list of connected RIOs, NEOs, and NEO Groups with share access
     *
     * @returns {void}
     */
    const getPermittedListItems = async () => {
      listLoading.value = true;
      const data = {
        id: props.document_id,
        page: applicationData.value.page,
      };

      try {
        emit('reset-alert');
        const getPermittedListApi =
          await shareSettingRequestApi.getPermittedList(data.id, data);
        const getUserResponseData = getPermittedListApi.data;

        permittedLists.value = getUserResponseData?.data || [];
        paginationData.value = getUserResponseData?.meta || [];
        totalResults.value = getUserResponseData?.meta?.total || 0;

        // Handle out of bounds page
        if (permittedLists.value.length === 0 && totalResults.value > 0) {
          applicationData.value.page = null;
          getPermittedListItems();
        }
      } catch (error) {
        emit('reset-alert');
        emit('set-alert', 'failed');
        throw error;
      } finally {
        listLoading.value = false;
      }
    };

    /**
     * Soft delete selected item
     */
    const handleUnshare = async (request) => {
      const data = {
        id: request.access_id,
      };

      // Begin loading
      emit('update-modal-loading', true);

      await shareSettingRequestApi
        .unshare(data.id)
        .then(() => {
          emit('reset-alert');
          emit(
            'set-alert',
            'success',
            i18n.global.t('messages.document_management.folder_unshared')
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
          emit('reset-modal');
        });
    };

    /**
     * Watch on document id change
     */
    watch(
      () => props.document_id,
      () => {
        if (props.document_id !== 0) {
          getPermittedListItems();
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
      getPermittedListItems();
    };

    return {
      applicationData,
      changePage,
      Common,
      DefaultImageCategory,
      fileURL,
      getPermittedListItems,
      handleUnshare,
      listLoading,
      paginationData,
      permittedLists,
      totalResults,
    };
  },
};
</script>
