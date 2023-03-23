<template>
  <div>
    <p class="bg-dark c-white p-2 mb-0 mt-4">
      {{ $t('labels.article') }}
    </p>

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
          <div class="mr-40" @click.stop="handleRedirectionToView(article.id)">
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
                @click="handleDeleteArticle(article.id)"
                class="dropdown-item"
              >
                {{ $t('buttons.delete') }}
              </button>
              <a
                href="#"
                @click.stop="handleMoveArticle(article.id)"
                class="dropdown-item"
              >
                {{ $t('buttons.move') }}
              </a>
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
          <!-- Loader -->
          <loader :show="loading" />

          {{ $t('paragraphs.there_are_no_articles') }}
        </li>
      </div>
    </ul>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import Loader from '../../base/BaseSectionLoader.vue';
import KnowledgeApiService from '../../../api/knowledges/knowledges';
import KnowledgeTypes from '../../../enums/KnowledgeTypes';

export default defineComponent({
  name: 'ArticlesSection',
  props: {
    id: {
      type: [String, Number, null],
      required: false,
    },
  },
  components: {
    Loader,
  },
  setup(props, { emit }) {
    const knowledgeApiService = new KnowledgeApiService();
    const loading = ref(false);
    const articleList = ref({});
    const id = ref(props.id);

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
     * Get all articles within current directory
     *
     * @returns {void}
     */
    const getArticles = () => {
      setLoading(true);

      knowledgeApiService
        .getArticles(id.value)
        .then((response) => {
          articleList.value = response.data.data;
        })
        .catch((error) => {
          articleList.value = [];
          emit('set-alert', 'failed');

          throw error;
        })
        .finally(() => {
          setLoading(false);
        });
    };

    /**
     * Delete public article
     *
     * @param {int} articleId
     * @returns {void}
     */
    const handleDeleteArticle = (articleId) => {
      emit('handle-delete', articleId, KnowledgeTypes.ARTICLE);
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
     * Move article
     *
     * @param {int} articleId
     */
    const handleMoveArticle = (articleId) => {
      emit('move-article', articleId);
    };

    /**
     * On mounted property
     */
    onMounted(() => {
      getArticles();
    });

    return {
      loading,
      setLoading,
      articleList,
      handleMoveArticle,
      handleDeleteArticle,
      handleRedirectionToView,
    };
  },
});
</script>
