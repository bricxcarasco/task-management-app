import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import KnowledgeList from './list/Index.vue';
import CreateArticle from './components/CreateArticle.vue';
import CreateCommentModal from './components/CreateCommentModal.vue';
import CreateMenuModal from './components/CreateMenuModal.vue';
import DocumentListModal from './modals/DocumentListModal.vue';
import DocumentListModalItem from './modals/DocumentListModalItem.vue';
import ArticleDetail from './article/index.vue';
import ArticleSearchList from './list/Search.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('knowledge-list', KnowledgeList);
app.component('create-article', CreateArticle);
app.component('create-comment-modal', CreateCommentModal);
app.component('create-menu-modal', CreateMenuModal);
app.component('document-list-modal', DocumentListModal);
app.component('document-list-modal-item', DocumentListModalItem);
app.component('article-detail', ArticleDetail);
app.component('article-search-list', ArticleSearchList);

app.mount('#app');
