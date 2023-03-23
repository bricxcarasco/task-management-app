import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';

/* eslint import/no-unresolved: 2 */
import BasicSettings from './basic_settings/Index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('basic-settings', BasicSettings);

app.mount('#app');
