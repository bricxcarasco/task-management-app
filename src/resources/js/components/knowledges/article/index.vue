<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <!-- Create Comment Modal -->
    <create-comment-modal
      ref="createCommentModalRef"
      :article="article"
      @refresh="refresh"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Delete Comment Modal -->
    <delete-comment-modal
      @refresh="refresh"
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
              <div class="text-center position-relative p-2 mb-2">
                <a href="/knowledges">
                  <i class="ai-arrow-left message__back"></i>
                </a>
                <h4 class="py-3 px-5 mb-0 text-center">
                  {{ article.task_title }}
                </h4>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <div>
                <i class="me-2 ai-wechat"></i>
                <button
                  class="border-0 p-0 ms-1"
                  style="background: transparent; color: #4a4b65"
                  @click="scrollToComments"
                >
                  Comments
                  <span class="badge bg-danger">{{ commentsCount }}</span>
                </button>
              </div>
              <div class="text-end">
                <button
                  v-if="isOwned(article)"
                  class="border-0 p-0 me-4 text-primary"
                  style="background: transparent; color: #4a4b65"
                  @click="editRedirect($event, article.id)"
                >
                  編集
                </button>
                <button
                  class="border-0 p-0"
                  style="background: transparent; color: #4a4b65"
                  @click="downloadPDF"
                >
                  <i class="me-2 ai-download"></i>
                </button>
              </div>
            </div>
            <div class="tab-content mt-2">
              <div class="tab-pane fade active show">
                <editor
                  v-model="articles.contents"
                  :api-key="BpheroConfig.TINYMCE_API_KEY"
                  :init="TinymceConfig"
                  :disabled="true"
                />
                <p class="p-2 mb-0 mt-2">
                  - {{ $t('labels.reference_url') }} -
                </p>
                <ul class="list-group list-group-flush">
                  <li
                    v-for="(url, index) in article.list_url"
                    :key="index"
                    class="
                      list-group-item
                      px-0
                      py-2
                      position-relative
                      list--white
                      px-2
                      bg-transparent
                      border-0
                    "
                  >
                    <div class="d-flex align-items-center">
                      <span
                        >{{ ++index }}.
                        <a :href="url" target="_blank">{{ url }}</a></span
                      >
                    </div>
                  </li>
                </ul>
                <div id="comments">
                  <hr />
                  <div
                    class="
                      d-flex
                      justify-content-between
                      align-items-center
                      mt-2
                    "
                  >
                    <div>- Comments -</div>
                    <div class="text-end">
                      <button
                        type="button"
                        class="btn btn-link"
                        @click="openCreateCommentModal"
                      >
                        + {{ $t('buttons.to_comment') }}
                      </button>
                    </div>
                  </div>
                  <div class="scrolling-component" ref="scrollComponent">
                    <li
                      v-for="(article, index) in listComments"
                      :key="index"
                      class="list-item-hover comments__list"
                    >
                      <div class="container mt-3">
                        <div class="row">
                          <div
                            class="
                              col-3
                              bg-transparent
                              d-flex
                              align-items-center
                              comments-profile-img-wrapper
                            "
                          >
                            <img
                              class="rounded-circle img--profile_image_smd"
                              :src="article.user_image ?? ''"
                              @error="handleImageLoadError"
                              width="60"
                            />
                          </div>
                          <div class="col-9 list-group-item">
                            <div
                              class="
                                d-flex
                                justify-content-between
                                align-items-center
                              "
                            >
                              <div style="flex: 1">
                                <span class="chat-list-text">
                                  <strong>
                                    {{ article.name }}
                                  </strong>
                                </span>
                              </div>
                              <span
                                v-bind:class="classCondition(article.rio_id)"
                              >
                                {{ article.date }}
                              </span>
                              <button
                                v-if="isRio(article.rio_id)"
                                type="button"
                                class="btn btn-link p-0"
                                style="width: 25px"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="ai-more-vertical c-primary"></i>
                              </button>
                              <div class="dropdown-menu my-1">
                                <button
                                  @click="
                                    handleEditComment(
                                      article.id,
                                      article.comment
                                    )
                                  "
                                  class="dropdown-item"
                                >
                                  {{ $t('buttons.article_edit') }}
                                </button>
                                <button
                                  @click="handleDeleteComment(article.id)"
                                  class="dropdown-item"
                                >
                                  {{ $t('buttons.article_delete') }}
                                </button>
                              </div>
                            </div>
                            <div class="d-flex align-items-center wrap">
                              <p class="mb-0 fs-xs chat-list-text">
                                {{ article.comment }}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <div
                      class="
                        d-flex
                        align-items-center
                        justify-content-center
                        mt-3
                      "
                    >
                      <InfiniteLoading
                        @infinite="loadComments"
                        :identifier="resetData"
                        :slots="{
                          complete: $t('messages.knowledges.there_is_no_more'),
                        }"
                        ref="infiniteLoading"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import Editor from '@tinymce/tinymce-vue';
import BaseAlert from '../../base/BaseAlert.vue';
import CreateCommentModal from '../components/CreateCommentModal.vue';
import TinymceConfig from '../../../utils/tinymce-config';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';
import BpheroConfig from '../../../config/bphero';
import ArticlesApiService from '../../../api/knowledges/articles';
import 'v3-infinite-loading/lib/style.css';
import DeleteCommentModal from '../components/DeleteCommentModal.vue';

export default defineComponent({
  name: 'ArticleDetailIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    article: {
      type: [Array, Object],
      required: true,
    },
    comments: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAlert,
    CreateCommentModal,
    InfiniteLoading,
    Editor,
    DeleteCommentModal,
  },
  setup(props) {
    const articleApiService = new ArticlesApiService();
    const service = ref(props.service);
    const articles = ref(props.article);
    const listComments = ref([]);
    const commentsCount = ref(props.comments.length);
    const resetData = ref(false);
    const createCommentModalRef = ref(null);
    let page = 0;
    const alert = ref({
      success: false,
      failed: false,
    });
    const rio = ref(props.rio);

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
     * Redirect to edit page
     *
     * @parameter event event
     * @parameter integer id
     */
    const editRedirect = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.replace(`/knowledges/articles/create/${id}`);
    };

    /**
     * Get comments
     *
     * @returns {void}
     */
    const loadComments = async ($state) => {
      const data = {
        page: (page += 1),
      };

      await articleApiService
        .loadComments(articles.value.id, data)
        .then((response) => {
          if (response.data.data.length < BpheroConfig.PAGINATE_COUNT) {
            $state.complete();
          } else {
            $state?.loaded();
          }
          listComments.value.push(...response.data.data);
          commentsCount.value = response.data.meta.total;
        })
        .catch((error) => {
          throw error;
        });
    };

    /**
     * Refresh lists
     *
     * @returns {void}
     */
    const refresh = () => {
      listComments.value.length = 0;
      resetData.value = !resetData.value;
      page = 0;
    };

    /**
     * Check if article is owned
     *
     * @parameter Object article
     * @returns {boolean}
     */
    const isOwned = (article) => {
      switch (props.service.type) {
        case ServiceSelectionTypes.RIO:
          return props.service.data.id === article.owner_rio_id;
        case ServiceSelectionTypes.NEO:
          return props.service.data.id === article.owner_neo_id;
        default:
          return false;
      }
    };

    /**
     * Redirect to main list page
     *
     * @returns {void}
     */
    const scrollToComments = () => {
      const element = document.getElementById('comments').offsetTop;
      window.scrollTo({
        top: element,
        left: 0,
        behavior: 'smooth',
      });
    };

    /**
     * PDF download handler
     *
     * @returns {void}
     */
    const downloadPDF = () => {
      window.location.href = `/knowledges/articles/pdf-download/${articles.value.id}`;
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

    const openCreateCommentModal = () => {
      createCommentModalRef.value.show();
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
     * Check if owned comment
     *
     * @returns {string}
     */
    const isRio = (article) => rio.value.id === article;

    /**
     * class condition base on comment
     *
     * @returns {string}
     */
    const classCondition = (article) => {
      if (isRio(article)) {
        return 'fs-md me-2 text-gray chat-list-text';
      }

      return 'me-4 text-gray pe-2';
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
     * Delete comment
     *
     * @returns {void}
     */
    const handleDeleteComment = (id) => {
      const modalId = '#comment-delete-confirmation';
      const modal = document.querySelector(modalId);

      modal.querySelector('input[name=id]').value = id;

      openModal(modalId);
    };

    /**
     * Edit comment
     *
     * @returns {void}
     */
    const handleEditComment = (id, comment) => {
      const modalId = '#create-comment-modal';
      const modal = document.querySelector(modalId);

      modal.querySelector('input[name=id]').value = id;
      modal.querySelector('textarea[name=comment]').value = comment;

      openModal(modalId);
    };

    return {
      articles,
      listComments,
      isRio,
      page,
      loadComments,
      serviceName,
      handleImageLoadError,
      isOwned,
      scrollToComments,
      downloadPDF,
      resetData,
      refresh,
      commentsCount,
      classCondition,
      TinymceConfig,
      BpheroConfig,
      createCommentModalRef,
      openCreateCommentModal,
      handleDeleteComment,
      setAlert,
      resetAlert,
      alert,
      handleEditComment,
      editRedirect,
    };
  },
});
</script>
