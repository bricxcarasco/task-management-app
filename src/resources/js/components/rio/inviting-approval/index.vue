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
    <div class="row">
      <div class="col-12 col-md-9 offset-md-3">
        <div
          class="
            d-flex
            align-items-center
            justify-content-center
            mb-0 mb-md-2
            position-relative
          "
        >
          <a
            :href="'/rio/profile/introduction/'"
            class="btn btn-secondary btn--back"
          >
            <i class="ai-arrow-left"></i>
          </a>
          <h3>{{ $t('labels.invitation_management') }}</h3>
        </div>
        <div class="border-bottom mt-4 pb-2">
          {{ $t('labels.invitation_list') }}
        </div>
        <div
          class="d-flex flex-column h-100 rounded-3 mt-4"
          id="pending-invite-list"
        >
          <p class="m-0">
            {{ invitationLists.data.length }} {{ $t('labels.cases') }}
          </p>
          <div class="card p-4 shadow mt-2">
            <div class="connection__wrapper">
              <section-loader :show="listLoading" />
              <ul class="connection__lists list-group list-group-flush mt-2">
                <li
                  v-for="(list, key) in invitationLists.data"
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
                    @click="handleRedirectToProfilePage(list.neo_profile_id)"
                    style="cursor: pointer"
                    class="
                      rounded-circle
                      me-2
                      d-inline-block
                      img--profile_image_sm
                    "
                    @error="handleImageLoadError"
                    :src="list.profile_photo ?? ''"
                    alt="profile photo"
                    width="40"
                  />
                  <span
                    @click="handleRedirectToProfilePage(list.neo_profile_id)"
                    class="fs-xs c-primary ms-2"
                    style="cursor: pointer"
                  >
                    {{ list.organization_name }}
                  </span>
                  <div
                    class="
                      vertical-right
                      d-flex
                      align-items-center
                      justify-content-center
                    "
                  >
                    <button
                      class="fs-xs btn btn-link manage-invitation"
                      @click="
                        handleClickAccept(
                          list.neo_belong_id,
                          list.organization_name
                        )
                      "
                    >
                      {{ $t('links.accept') }}
                    </button>
                    <button
                      class="fs-xs btn btn-link manage-invitation"
                      @click="
                        handleClickDecline(
                          list.neo_belong_id,
                          list.organization_name
                        )
                      "
                    >
                      {{ $t('links.decline') }}
                    </button>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="d-flex justify-content-center mt-1 mb-3">
            <pagination
              :meta="invitationLists"
              @changePage="handleClickChangePage"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
@endsection
<script>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import BpheroConfig from '../../../config/bphero';
import BaseAlert from '../../base/BaseAlert.vue';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';
import RioManageInvitationApiService from '../../../api/rio/invite-connection';

export default {
  name: 'NeoPendingInvitation',
  props: {
    invitation_lists: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    Pagination,
    BaseAlert,
    SectionLoader,
  },
  setup(props) {
    const rioManageInvitationApiService = new RioManageInvitationApiService();
    const invitationLists = ref(props.invitation_lists);
    const listLoading = ref(false);
    const totalResults = ref(0);
    const errors = ref(null);
    const prevPage = ref(null);
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

    const handleRedirectToProfilePage = (id) => {
      window.location.href = `/neo/profile/introduction/${id}`;
    };

    const getInviteLists = async (id) => {
      listLoading.value = true;
      const data = {
        userId: id,
        page: prevPage.value,
      };
      await rioManageInvitationApiService
        .getInvitationList(data)
        .then((response) => {
          invitationLists.value = response.data.data;
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
          setAlert('failed');
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    const handleClickAccept = async (neoId, orgName) => {
      listLoading.value = true;

      await rioManageInvitationApiService
        .acceptInvitation(neoId)
        .then(() => {
          getInviteLists();
          setAlert(
            'success',
            `${orgName}  ${i18n.t('alerts.accept_invitation')}`
          );
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
          setAlert('failed');
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    const handleClickDecline = async (neoId, orgName) => {
      listLoading.value = true;

      await rioManageInvitationApiService
        .declineInvitation(neoId)
        .then(() => {
          getInviteLists();
          setAlert(
            'success',
            `${orgName}  ${i18n.t('alerts.decline_invitation')}`
          );
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = responseData.data;
          }
          setAlert('failed');
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    const handleClickChangePage = async (page) => {
      listLoading.value = true;
      const data = {
        pageNo: page,
      };

      try {
        const getResultsApi =
          await rioManageInvitationApiService.getInvitationListPage(data);
        const getUserResponseData = getResultsApi.data;

        prevPage.value = page;
        invitationLists.value = getUserResponseData.data || [];
        totalResults.value = 1 || 0;
      } catch (error) {
        setAlert('failed');
      } finally {
        listLoading.value = false;
      }
    };

    return {
      handleImageLoadError,
      handleClickAccept,
      handleClickDecline,
      invitationLists,
      listLoading,
      alert,
      handleClickChangePage,
      resetAlert,
      getInviteLists,
      handleRedirectToProfilePage,
    };
  },
};
</script>
