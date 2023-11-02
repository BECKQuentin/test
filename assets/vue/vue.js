import axios from '../js/custom/axios';
import routes from "../js/custom/routing";

import {createApp} from "vue";
import App from "./App";
import Message from "./user/Message.vue";
import Maps from "./Maps.vue";

const app = createApp({})

app.config.globalProperties.$http = axios
app.config.globalProperties.$path = routes

app
    .component('app', App)
    .component('message', Message)
    .component('maps', Maps)

app.mount('#app')