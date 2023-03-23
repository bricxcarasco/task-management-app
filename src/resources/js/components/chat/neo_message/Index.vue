<template>
  <div>
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
      <!-- Content-->
      <div class="col-12 offset-md-3 col-md-9">
        <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
          <div class="position-relative">
            <h3 class="py-3 mb-0 text-center border-bottom">
              {{ session.data.organization_name }} {{ $t('labels.connection') }}
            </h3>
            <div>
              <div class="position-relative mb-4">
                <a :href="returnBack" class="btn btn-secondary">
                  <i class="ai-arrow-left"></i>
                  {{ $t('labels.return') }}
                </a>
              </div>
              <div class="mb-3">
                <label for="text-input" class="form-label"
                  >{{ $t('labels.message_title')
                  }}<sup class="text-danger ms-1">*</sup></label
                >
                <input
                  v-model="title"
                  :class="errors?.title != null ? 'is-invalid' : ''"
                  class="form-control"
                  type="text"
                  id="text-input"
                />
                <div
                  v-show="errors?.title"
                  v-for="(error, index) in errors?.title"
                  :key="index"
                  class="invalid-feedback"
                >
                  {{ error }}
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <label for="radio" class="form-label">{{
                  $t('labels.search_target')
                }}</label>
                <div>
                  <div class="form-check form-check-inline">
                    <input
                      v-model="target"
                      @change="filterList"
                      :value="ChatTargetTypes.ALL"
                      class="form-check-input"
                      type="radio"
                      id="target-all"
                      name="target"
                    />
                    <label class="form-check-label" for="target-all">{{
                      $t('labels.all')
                    }}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      v-model="target"
                      @change="filterList"
                      :value="ChatTargetTypes.RIO"
                      class="form-check-input"
                      type="radio"
                      id="target-RIO"
                      name="target"
                    />
                    <label class="form-check-label" for="target-RIO">RIO</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      v-model="target"
                      @change="filterList"
                      :value="ChatTargetTypes.NEO"
                      class="form-check-input"
                      type="radio"
                      id="target-NEO"
                      name="target"
                    />
                    <label class="form-check-label" for="target-NEO">NEO</label>
                  </div>
                </div>
              </div>
              <div class="input-group mb-4">
                <input
                  class="form-control"
                  type="text"
                  v-model="search"
                  id="text-input"
                  :placeholder="$t('placeholders.search')"
                />
                <button
                  class="btn btn-translucent-dark"
                  @click="searchByFilter"
                  type="submit"
                >
                  {{ $t('buttons.search') }}
                </button>
              </div>
              <section-loader :show="listLoading" />
              <div
                class="d-flex justify-content-between align-items-center mt-2"
              >
                <p class="mb-0 pe-2">
                  {{ connectedList.data.length }}{{ $t('labels.subject') }}
                </p>
              </div>
              <div class="card p-4 shadow mt-2">
                <div class="connection__wrapper">
                  <ul
                    v-if="connectedList.data.length > 0"
                    class="connection__lists list-group list-group-flush mt-2"
                  >
                    <li
                      :class="errors?.recipients != null ? 'is-invalid' : ''"
                      v-for="(list, key) in connectedList.data"
                      :key="key"
                      class="
                        d-flex
                        justify-content-between
                        align-items-center
                        list-group-item
                        px-0
                        py-2
                        position-relative
                        list--white
                        px-2
                      "
                    >
                      <div>
                        <img
                          :src="list.profile_photo ?? ''"
                          @error="handleImageLoadError"
                          class="
                            rounded-circle
                            me-2
                            d-inline-block
                            img--profile_image_sm
                          "
                          alt="profile photo"
                          width="40"
                        />
                        <span class="fs-xs c-primary ms-2">{{
                          list.name
                        }}</span>
                      </div>
                      <input
                        class="form-check-input"
                        v-bind:value="{
                          id: list.connection_rio_id
                            ? list.connection_rio_id
                            : list.connection_neo_id,
                          type: list.connection_rio_id
                            ? ServiceSelectionTypes.RIO
                            : ServiceSelectionTypes.NEO,
                        }"
                        v-model="recipients"
                        type="checkbox"
                      />
                    </li>
                    <div
                      v-show="errors?.recipients"
                      v-for="(error, index) in errors?.recipients"
                      :key="index"
                      class="invalid-feedback d-flex justify-content-center"
                    >
                      {{ error }}
                    </div>
                  </ul>
                  <div v-else class="d-flex justify-content-center mt-3">
                    {{ $t('labels.no_search_result') }}
                  </div>
                  <!-- Pagination -->
                  <div class="d-flex justify-content-center mt-1 mb-3">
                    <pagination
                      :meta="connectedList"
                      @changePage="changePage"
                    />
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-center mt-4">
                <button
                  type="submit"
                  class="btn btn-primary"
                  @click="createNeoMessage"
                >
                  {{ $t('buttons.register') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import NeoMessageApiService from '../../../api/chat/neo_message';
import BpheroConfig from '../../../config/bphero';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';
import ChatTargetTypes from '../../../enums/ChatTargetTypes';
import Pagination from '../../base/BasePagination.vue';
import SectionLoader from '../../base/BaseSectionLoader.vue';

export default {
  name: 'NeoMessageIndex',
  props: {
    lists: {
      type: [Array, Object],
      required: true,
    },
    session: {
      type: [Array, Object],
      required: true,
    },
    chatlist: [Array, Object],
  },
  components: {
    Pagination,
    SectionLoader,
  },
  setup(props, { emit }) {
    const search = ref('');
    const neoMessageApiService = new NeoMessageApiService();
    const connectedList = ref(props.lists);
    const listCount = ref(props.lists.length);
    const target = ref(ChatTargetTypes.ALL);
    const title = ref('');
    const recipients = ref([]);
    const errors = ref(null);
    const savedKeyword = ref(null);
    const searchResults = ref([]);
    const paginationData = ref([]);
    const totalResults = ref(0);
    const returnBack = ref('/messages');
    const listLoading = ref(false);

    const filterList = async () => {
      search.value = null;
      listLoading.value = true;
      const data = {
        target: target.value,
      };

      await neoMessageApiService
        .getFilteredList(data)
        .then((response) => {
          connectedList.value = response.data.data;
          listCount.value = response.data.data.length;
        })
        .catch(() => {
          emit('set-alert', 'failed');
        })
        .finally(() => {
          listLoading.value = false;
        });
    };

    const createNeoMessage = async () => {
      listLoading.value = true;
      const data = {
        title: title.value,
        recipients: JSON.parse(JSON.stringify(recipients.value)),
      };

      await neoMessageApiService
        .createNeoMessage(data)
        .then((response) => {
          window.location.replace(`/messages/${response.data.data}`);
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

    const searchByFilter = async () => {
      listLoading.value = true;
      const data = {
        search: search.value,
        target: target.value,
      };

      if (search.value) {
        await neoMessageApiService
          .updateList(data)
          .then((response) => {
            if (response.data.data.data.length > 0) {
              savedKeyword.value = data.search;
            } else {
              savedKeyword.value = null;
            }
            connectedList.value = response.data.data;
            connectedList.value = response.data.data;
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
      } else {
        savedKeyword.value = null;
        filterList();
      }
    };

    const changePage = async (page) => {
      listLoading.value = true;
      const data = {
        target: target.value,
        pageNo: page,
        search: savedKeyword.value,
      };

      try {
        const getResultsApi = await neoMessageApiService.getSelectedPage(data);
        const getUserResponseData = getResultsApi.data;

        searchResults.value = getUserResponseData?.data || [];
        connectedList.value = getUserResponseData.data || [];
        totalResults.value = 1 || 0;
      } catch (error) {
        emit('set-alert', 'failed');
      } finally {
        listLoading.value = false;
      }
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

    return {
      handleImageLoadError,
      listLoading,
      changePage,
      returnBack,
      searchResults,
      paginationData,
      totalResults,
      ServiceSelectionTypes,
      createNeoMessage,
      ChatTargetTypes,
      errors,
      title,
      recipients,
      NeoMessageApiService,
      filterList,
      connectedList,
      listCount,
      target,
      search,
      searchByFilter,
      savedKeyword,
    };
  },
};
</script>
