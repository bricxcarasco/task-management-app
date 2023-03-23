import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import NeoProfileEdit from './profile/edit/Index.vue';
import NeoAdministrator from './administrator/Index.vue';
import NeoTransferOwnership from './administrator/TransferOwnership.vue';
import NeoParticipationInvitation from './inviting/index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('neo-profile-edit', NeoProfileEdit);
app.component('neo-administrator', NeoAdministrator);
app.component('neo-transfer-ownership', NeoTransferOwnership);
app.component('neo-participation-invitation', NeoParticipationInvitation);

app.mount('#app');
