import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import RioProfileEdit from './profile/edit/Index.vue';
import RioInformationEdit from './information/edit/Index.vue';
import RioConnections from './connections/Index.vue';
import RioPendingInvitation from './inviting-approval/index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('rio-profile-edit', RioProfileEdit);
app.component('rio-information-edit', RioInformationEdit);
app.component('rio-connections', RioConnections);
app.component('rio-pending-invitation', RioPendingInvitation);

app.mount('#app');
