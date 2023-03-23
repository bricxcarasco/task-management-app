<template>
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
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />
    <div class="col-12 offset-md-3 col-md-9">
      <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
        <div class="border-bottom position-relative">
          <a
            :href="'/neo/profile/application_management/invitation/' + neoId"
            class="btn btn-secondary btn--back"
          >
            <i class="ai-arrow-left"></i>
          </a>
          <h3 class="p-3 mb-0 text-center">
            {{ $t('headers.invite_rio') }}
          </h3>
        </div>
        <div class="btn-group">
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
        <section-loader :show="listLoading" />
        <div class="mt-2" id="participation-invitation">
          <ul
            v-if="connectedLists.data.length > 0"
            class="list-group list-group-flush mt-4"
          >
            <p class="mb-2">
              {{ connectedLists.data.length }}{{ $t('links.total_suffix') }}
            </p>
            <li
              v-for="(list, key) in connectedLists.data"
              :key="key"
              class="
                list-group-item
                px-0
                py-2
                position-relative
                list--white
                px-2
              "
            >
              <img
                class="rounded-circle me-2 d-inline-block img--profile_image_sm"
                @error="handleImageLoadError"
                style="cursor: pointer"
                @click="handleRedirectToProfilePage(list.rio_id)"
                :src="list.profile_photo ?? ''"
                alt="profile photo"
                width="40"
              />
              <span
                class="fs-xs c-primary ms-2"
                style="cursor: pointer"
                @click="handleRedirectToProfilePage(list.rio_id)"
                >{{ list.name }}</span
              >
              <button
                v-if="
                  list.invitation_status === NeoBelongStatusType.INVITING ||
                  list.invitation_status === NeoBelongStatusType.DECLINED
                "
                class="fs-xs vertical-right btn"
                disabled
              >
                {{ $t('links.pending_invite') }}
              </button>
              <button
                v-else-if="
                  list.invitation_status === NeoBelongStatusType.AFFILIATED ||
                  list.invitation_status === NeoBelongStatusType.INVITE
                "
                class="fs-xs vertical-right btn"
                disabled
              >
                {{ $t('links.participating') }}
              </button>
              <button
                v-else
                type="button"
                @click="handleClickInvite(list.rio_id, list.name)"
                id="invite-to-participation"
                class="btn btn-link fs-xs vertical-right"
              >
                {{ $t('links.invitation') }}
              </button>
            </li>
          </ul>
          <div v-else class="d-flex justify-content-center mt-3">
            {{ $t('labels.no_search_result') }}
          </div>
          <div class="d-flex justify-content-center mt-1 mb-3">
            <pagination
              :meta="connectedLists"
              @changePage="handleClickChangePage"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import BpheroConfig from '../../../config/bphero';
import BaseAlert from '../../base/BaseAlert.vue';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import NeoManageInvitationApiService from '../../../api/neo/invite-connection';
import NeoBelongStatusType from '../../../enums/NeoBelongStatusType';

export default {
  name: 'NeoParticipationInvitation',
  props: {
    neo: {
      type: [Array, Object],
      required: true,
    },
    connected_lists: {
      type: [Array, Object],
      required: true,
    },
    neo_id: {
      type: Number,
      required: true,
    },
  },
  components: {
    Pagination,
    BaseAlert,
    SectionLoader,
  },
  setup(props, { emit }) {
    const neoManageInvitationApiService = new NeoManageInvitationApiService();
    const connectedLists = ref(props.connected_lists);
    const listLoading = ref(false);
    const search = ref('');
    const neoId = ref(props.neo_id);
    const errors = ref(null);
    const searchResults = ref([]);
    const paginationData = ref([]);
    const savedKeyword = ref(null);
    const savedPage = ref(null);
    const totalResults = ref(0);
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

    const getConnectionList = async () => {
      listLoading.value = true;
      await neoManageInvitationApiService
        .getConnectionList(neoId.value)
        .then((response) => {
          connectedLists.value = response.data.data;
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Inject validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }

          emit('set-alert', 'failed');
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    const handleRedirectToProfilePage = (id) => {
      window.location.href = `/rio/profile/introduction/${id}`;
    };

    /**
     * Handle invalid or empty images
     *
     * @param {Event} event
     * @returns {void}
     */
    const handleImageLoadError = (event) => {
      /* eslint-disable no-param-reassign */
      event.target.src = BpheroConfig.DEFAULT_IMG;
    };

    /**
     * Redirect back to messages list
     */
    const handleRedirectToManagement = () => {
      window.location.href = `/neo/profile/application_management/invitation/${neoId.value}`;
    };

    const handleClickSearch = async () => {
      listLoading.value = true;
      const data = {
        keyword: search.value,
      };
      if (search.value) {
        await neoManageInvitationApiService
          .searchKeyword(data, neoId.value)
          .then((response) => {
            if (response.data.data.data.length > 0) {
              savedKeyword.value = data.keyword;
            } else {
              savedKeyword.value = null;
            }
            connectedLists.value = response.data.data;
          })
          .catch((error) => {
            const responseData = error.response.data;

            if (responseData.status_code === 422) {
              errors.value = responseData.data;
            }

            emit('set-alert', 'failed');
          })
          .finally(() => {
            listLoading.value = false;
          });
      } else {
        savedKeyword.value = null;
        getConnectionList();
      }
    };

    const handleClickChangePage = async (page) => {
      listLoading.value = true;
      const data = {
        pageNo: page,
        prevSearch: savedKeyword.value,
      };

      try {
        const getResultsApi =
          await neoManageInvitationApiService.getSelectedPage(
            data,
            neoId.value
          );
        const getUserResponseData = getResultsApi.data;

        searchResults.value = getUserResponseData?.data || [];
        connectedLists.value = getUserResponseData.data || [];
        totalResults.value = 1 || 0;
        savedPage.value = page;
      } catch (error) {
        emit('set-alert', 'failed');
      } finally {
        listLoading.value = false;
      }
    };

    const handleClickInvite = async (id, name) => {
      listLoading.value = true;
      const data = {
        userId: id,
      };
      await neoManageInvitationApiService
        .inviteConnection(data, neoId.value)
        .then(() => {
          setAlert('success', `${name}  ${i18n.t('alerts.invited')}`);
          if (savedPage.value) {
            handleClickChangePage(savedPage.value);
          } else {
            handleClickChangePage();
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
          setAlert('failed');
          window.location.reload();
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    return {
      handleImageLoadError,
      handleClickInvite,
      getConnectionList,
      handleClickSearch,
      handleRedirectToProfilePage,
      handleRedirectToManagement,
      handleClickChangePage,
      NeoBelongStatusType,
      searchResults,
      paginationData,
      totalResults,
      savedKeyword,
      savedPage,
      listLoading,
      neoId,
      search,
      alert,
      resetAlert,
      connectedLists,
    };
  },
};
</script>
