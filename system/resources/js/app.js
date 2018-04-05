// Include css
require('../css/app.css');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-Timezone'] = Intl.DateTimeFormat().resolvedOptions().timeZone;
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    window.csrf = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Vue from 'vue';
import VModal from 'vue-js-modal';

Vue.use(VModal, { dialog: true });

Vue.component('file-browser', () => import(/* webpackChunkName: "browser" */ './components/FileBrowser.vue'));
Vue.component('uploader', () => import(/* webpackChunkName: "browser" */ './components/Uploader.vue'));
Vue.component('alert', () => import(/* webpackChunkName: "browser" */ './components/Alert.vue'));

new Vue({
    el: '#app',
    mounted() {

    }
});
