import { createApp } from "vue";
import "./assets/css/style.css";
import App from "./App.vue";
import store from "./store";
import router from "./router";
import "./echo/main.js";

createApp(App).use(store).use(router).mount("#app");
