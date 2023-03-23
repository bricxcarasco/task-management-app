import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import Details from './detail/Index.vue';
import WorkflowList from './list/Index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('workflow-details', Details);
app.component('workflow-list', WorkflowList);

app.mount('#app');
