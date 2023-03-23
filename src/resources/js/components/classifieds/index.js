import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import AllProductList from './all-product-list/Index.vue';
import RegisteredProductList from './registered-product-list/Index.vue';
import ViewProduct from './view-product/Index.vue';
import FavoriteList from './favorite-list/Index.vue';
import ContactMessagesList from './contact-messages-list/Index.vue';
import Conversation from './conversation/Index.vue';
import AccountTransfer from './account-transfer/Index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('all-product-list', AllProductList);
app.component('registered-product-list', RegisteredProductList);
app.component('view-product', ViewProduct);
app.component('favorites-list', FavoriteList);
app.component('contact-messages-list', ContactMessagesList);
app.component('view-conversation', Conversation);
app.component('account-transfer', AccountTransfer);

app.mount('#app');
