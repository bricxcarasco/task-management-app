import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import SharedSection from './SharedSection.vue';
import DefaultSection from './DefaultSection.vue';
import FilePreview from './previews/FilePreview.vue';
import DefaultFolderList from './lists/DefaultFolderList.vue';
import DefaultFileList from './lists/DefaultFileList.vue';
import ShareSetting from './share-setting/ShareSettingModal.vue';
import ConnectedList from './share-setting/ConnectedList.vue';
import PermittedList from './share-setting/PermittedList.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('shared-section', SharedSection);
app.component('default-section', DefaultSection);
app.component('file-preview', FilePreview);
app.component('default-folder-list', DefaultFolderList);
app.component('default-file-list', DefaultFileList);
app.component('share-setting-modal', ShareSetting);
app.component('connected-list', ConnectedList);
app.component('permitted-list', PermittedList);

app.mount('#app');
