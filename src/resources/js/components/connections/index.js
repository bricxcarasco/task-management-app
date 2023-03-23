import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import ConnectionApplicationRequest from './lists/ApplicationRequest.vue';
import SearchResultsSection from './search/Section.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('connection-application-request', ConnectionApplicationRequest);
app.component('search-results-section', SearchResultsSection);

app.mount('#app');
