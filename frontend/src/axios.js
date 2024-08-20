import axios from "axios";
import store from "./store";

const host = import.meta.env.VITE_API_BASE_URL;

const client = axios.create({
    baseURL: `${host}/api`,
});

client.interceptors.request.use((config) => {
    config.headers.Authorization = `Bearer ${store.state.user.token}`;
    return config;
});

export default client;
