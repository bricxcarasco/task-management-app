import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import ElectronicContract from './registration/Index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('electronic-contract', ElectronicContract);

app.mount('#app');
