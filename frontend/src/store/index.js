import { createStore } from "vuex";
import actions from "./actions";
import * as mutations from "./mutations";

const store = createStore({
  state: {
    user: {
      data: JSON.parse(sessionStorage.getItem("USER_DATA")),
      token: sessionStorage.getItem("TOKEN"),
      isAdmin: false,
      uniqueID: null,
    },
    notification: {
      show: false,
      type: null,
      message: null,
      period: 10000,
    },
    cartItems: [],
  },
  getters: {
    async cartItems(state) {
      return state.cartItems.length
        ? state.cartItems
        : JSON.parse(document.cookie.split("=")[1] || "[]");
    },
  },
  actions,
  mutations,
});

export default store;
