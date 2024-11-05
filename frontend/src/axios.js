import axios from "axios";
import store from "./store";

const host = import.meta.env.VITE_API_BASE_URL;

const client = axios.create({
  baseURL: `${host}/api`,
  withCredentials: true,
  withXSRFToken: true,
});

client.interceptors.request.use((config) => {
  config.headers.Authorization = `Bearer ${store.state.user.token}`;

  if (store.state.user.uniqueID) {
    config.headers["X-Unique-ID"] = store.state.user.uniqueID;
  }

  return config;
});

client.interceptors.response.use(
  (config) => {
    // Capture the uniqueId from the response headers
    if (!store.state.user.uniqueID) {
      store.commit("setUniqueID", config.headers["x-unique-id"]);
    }
    return config;
  },
  (config) => {
    if (!config?.response || config.response.status >= 500) {
      store.commit("notify", {
        type: "error",
        message: "Unexpected error happen",
      });
    } else if (!config?.response?.status === 401) {
      store.commit("setToken", null);
      store.commit("setUser", { user: null, isAdmin: false });
    }
    return Promise.reject(config);
  }
);

export default client;
