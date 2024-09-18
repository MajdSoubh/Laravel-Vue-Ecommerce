import Echo from "laravel-echo";
import Pusher from "pusher-js";
import store from "../store";
import axios from "../axios";

const host = import.meta.env.VITE_API_BASE_URL;
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],

  authorizer: (channel, options) => {
    return {
      authorize: (socketId, callback) => {
        axios
          .post("broadcasting/auth", {
            socket_id: socketId,
            channel_name: channel.name,
          })
          .then((response) => {
            callback(false, response.data);
          })
          .catch((error) => {
            callback(true, error);
          });
      },
    };
  },
});
// Subscribe to user channel and listen to all events related to it.
store.watch(
  (state) => state.user,
  (user) => {
    if (user.token) {
      window.Echo.private(`user.${user.data.id}`).listen(
        ".notification",
        (event) => {
          store.commit("notify", {
            type: event.type,
            message: event.message,
          });
        }
      );
    } else if (user.uniqueID) {
      window.Echo.channel(`user.${user.uniqueID}`).listen(
        ".notification",
        (event) => {
          store.commit("notify", {
            type: event.type,
            message: event.message,
          });
        }
      );
    }
  },
  { deep: true }
);
