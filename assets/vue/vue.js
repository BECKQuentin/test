import axios from '../js/custom/axios';
import routes from "../js/custom/routing";

import {createApp} from "vue";
import App from "./App";

const app = createApp({})

app.config.globalProperties.$http = axios
app.config.globalProperties.$path = routes

app
    .component('app', App)

app.mount('#app')