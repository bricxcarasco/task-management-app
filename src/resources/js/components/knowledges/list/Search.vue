<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Document List Modal -->
    <knowledge-document-list-modal ref="documentListModalRef" />

    <!-- Delete confirmation Modal -->
    <delete-confirmation-modal
      :type="knowledgeType"
      @reset-list="resetList"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
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
        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <div class="position-relative">
              <h3 class="py-1 mb-0 text-center">
                {{
                  $t('headers.service_owned_knowledge', { name: serviceName })
                }}
              </h3>
              <div>
                <div class="position-relative mb-4">
                  <a href="/knowledges">
                    {{ $t('links.return_to_home_folder') }}
                  </a>
                </div>
                <div class="text-center position-relative mb-2">
                  <h4
                    v-if="searchKeyword !== null"
                    class="py-3 px-5 mb-0 text-center"
                  >
                    {{
                      $t('labels.search_results_list', {
                        keyword: searchKeyword,
                      })
                    }}
                  </h4>
                  <h4 v-else class="py-3 px-5 mb-0 text-center">
                    {{ $t('labels.all_articles') }}
                  </h4>
                </div>
              </div>
            </div>
            <div class="input-group">
              <input
                name="keyword"
                class="form-control"
                type="text"
                v-model="formData.keyword"
              />
              <button
                class="btn btn-translucent-dark"
                type="button"
                @click="getSearchedArticles"
              >
                {{ $t('buttons.search') }}
              </button>
            </div>
            <div class="tab-content mt-2">
              <p class="mb-0 pe-2">
                {{ $t('labels.search_total', { total: articleList.length }) }}
              </p>
              <div class="tab-pane fade active show position-relative">
                <!-- Loader -->
                <loader :show="loading" />

                <ul class="list-group list-group-flush">
                  <div v-if="articleList.length > 0">
                    <li
                      v-for="(article, index) in articleList"
                      :key="index"
                      class="
                        list-group-item
                        py-2
                        position-relative
                        list--white
                        px-2
                        hoverable hoverable--background
                      "
                    >
                      <div
                        class="mr-40"
                        @click.stop="handleRedirectionToView(article.id)"
                      >
                        <i class="h2 m-0 ai-file-text"></i>
                        <span class="fs-xs c-primary ms-2">
                          {{ article.task_title }}
                        </span>
                      </div>
                      <div
                        v-if="article.is_owned"
                        class="vertical-right hasDropdown dropstart"
                      >
                        <button
                          class="btn btn-link"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="color-primary ai-more-vertical"></i>
                        </button>
                        <div class="dropdown-menu my-1">
                          <button
                            @click="handleDelete(article.id)"
                            class="dropdown-item"
                          >
                            {{ $t('buttons.delete') }}
                          </button>
                          <button
                            @click="handleMove(article.id)"
                            class="dropdown-item"
                          >
                            {{ $t('buttons.move') }}
                          </button>
                        </div>
                      </div>
                    </li>
                  </div>

                  <div v-else>
                    <li
                      class="
                        list-group-item
                        px-0
                        py-2
                        d-flex
                        justify-content-center
                        c-primary
                      "
                    >
                      {{ $t('paragraphs.there_are_no_articles') }}
                    </li>
                  </div>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import BaseAlert from '../../base/BaseAlert.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import Loader from '../../base/BaseSectionLoader.vue';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';
import KnowledgeTypes from '../../../enums/KnowledgeTypes';
import KnowledgeDocumentListModal from '../modals/KnowledgeDocumentListModal.vue';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal.vue';

export default defineComponent({
  name: 'SearchList',
  props: {
    keyword: {
      type: [String, Number, null],
      required: false,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAlert,
    Loader,
    KnowledgeDocumentListModal,
    DeleteConfirmationModal,
  },
  setup(props) {
    const alert = ref({
      success: false,
      failed: false,
    });
    const articleList = ref({});
    const formData = ref({});
    const knowledgeApiService = new KnowledgeApiService();
    const knowledgeType = ref(null);
    const loading = ref(false);
    const searchKeyword = ref(props.keyword);
    const service = ref(props.service);
    const documentListModalRef = ref(null);

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
     * Reload page
     *
     * @returns {void}
     */
    const resetList = () => {
      window.location.href = `/knowledges`;
    };

    /**
     * Get service name depending on selected subject
     *
     * @returns {string}
     */
    const serviceName = computed(() => {
      const { data } = service.value;

      switch (service.value.type) {
        case ServiceSelectionTypes.RIO:
          return data.full_name;
        case ServiceSelectionTypes.NEO:
          return data.organization_name;
        default:
          return `-`;
      }
    });

    /**
     * Set loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setLoading = (state) => {
      loading.value = state;
    };

    /**
     * Open specified modal
     *
     * @returns {void}
     */
    const openModal = (modalId) => {
      const targetModal = document.querySelector(modalId);
      /* eslint no-undef: 0 */
      const modal = bootstrap.Modal.getOrCreateInstance(targetModal);
      modal.show();
    };

    /**
     * Get all articles based on the search keyword
     *
     * @returns {void}
     */
    const getSearchedArticles = () => {
      setLoading(true);
      resetAlert();
      searchKeyword.value = formData.value.keyword;

      if (!formData.value.keyword || formData.value.keyword.trim() === '') {
        searchKeyword.value = null;
      }

      knowledgeApiService
        .searchArticles({ keyword: formData.value.keyword })
        .then((response) => {
          articleList.value = response.data.data;
        })
        .catch((error) => {
          articleList.value = [];
          setAlert('failed');
          throw error;
        })
        .finally(() => {
          setLoading(false);
        });
    };

    /**
     * Delete article handler
     *
     * @param {int} id
     * @returns {void}
     */
    const handleDelete = (id) => {
      const modalId = '#knowledge-delete-confirmation';
      const modal = document.querySelector(modalId);

      modal.querySelector('input[name=id]').value = id;
      knowledgeType.value = KnowledgeTypes.ARTICLE;

      openModal(modalId);
    };

    /**
     * Move article handler
     *
     * @param {int} id
     * @returns {void}
     */
    const handleMove = (id) => {
      document.querySelector('#movingKnowledgeSourceId').value = id;
      documentListModalRef.value.show();
    };

    /**
     * Redirect to Article view
     *
     * @param {int} articleId
     * @returns {void}
     */
    const handleRedirectionToView = (articleId) => {
      window.location.href = `/knowledges/articles/${articleId}`;
    };

    /**
     * On mounted property
     */
    onMounted(() => {
      formData.value.keyword = props.keyword;
      getSearchedArticles();
    });

    return {
      alert,
      articleList,
      formData,
      getSearchedArticles,
      handleRedirectionToView,
      knowledgeType,
      loading,
      openModal,
      resetAlert,
      resetList,
      searchKeyword,
      serviceName,
      setAlert,
      setLoading,
      handleDelete,
      handleMove,
      KnowledgeTypes,
      documentListModalRef,
    };
  },
});
</script>
