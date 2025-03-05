import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

const app = createApp(App);

axios.defaults.baseURL = "http://localhost:8000/api"; // Set Laravel API base URL
axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem(
    "token"
)}`;

app.use(router);
app.config.globalProperties.$axios = axios;
app.mount("#app");
