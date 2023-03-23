import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import ScheduleList from './list/Index.vue';
import ScheduleCreate from './create/Index.vue';
import ScheduleEdit from './edit/index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('schedule-list', ScheduleList);
app.component('schedule-create', ScheduleCreate);
app.component('schedule-edit', ScheduleEdit);

app.mount('#app');
