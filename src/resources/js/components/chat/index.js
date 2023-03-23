import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import ChatRooms from './room/Index.vue';
import ChatMessageRoom from './message/Index.vue';
import TestRoom from './test/Index.vue';
import NeoMessage from './neo_message/Index.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// Register components
app.component('chat-rooms', ChatRooms);
app.component('chat-message-room', ChatMessageRoom);
app.component('test-room', TestRoom);
app.component('neo-message', NeoMessage);

app.mount('#app');
