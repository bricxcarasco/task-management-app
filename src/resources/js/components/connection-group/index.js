import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import InviteMembersSection from './invite-members/Section.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

app.component('invite-members-section', InviteMembersSection);

app.mount('#app');
