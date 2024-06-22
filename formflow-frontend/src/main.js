import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia';
import Vue3ConfirmDialog from 'vue3-confirm-dialog'
import 'vue3-confirm-dialog/style'
import VueClickAway from "vue3-click-away";

const pinia = createPinia();

createApp(App).use(pinia).use(router).use(Vue3ConfirmDialog).use(VueClickAway).mount('#app')
