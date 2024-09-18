import axios from "../axios";

const actions = {
  login({ commit, dispatch }, { user, isAdmin }) {
    let url = "/login";
    if (isAdmin) {
      url = "/admin/login";
    }
    return axios.post(url, user).then((response) => {
      commit("setToken", response.data.token);
      commit("setUser", { user: response.data.user, isAdmin });
      if (!isAdmin) dispatch("uploadCurrentCart");
      return response;
    });
  },
  logout({ commit }, isAdmin = false) {
    let url = "/logout";
    if (isAdmin) {
      url = "/admin/logout";
    }
    return axios.get(url).then((response) => {
      commit("setToken", null);
      commit("setUser", { user: null, isAdmin });
      return response;
    });
  },
  forgetPassword({ commit }, data) {
    commit("forgetPassword", data.email);
  },
  register({ commit, dispatch }, { user, isAdmin }) {
    let url = "/register";
    if (isAdmin) {
      url = "/admin/register";
    }
    return axios.post(url, user).then((response) => {
      commit("setToken", response.data.token);
      commit("setUser", { user: response.data.user, isAdmin });
      if (!isAdmin) dispatch("uploadCurrentCart");
      return response;
    });
  },
  updatePassword({ commit }, { passwords, isAdmin = false }) {
    let url = "/password";
    if (isAdmin) {
      url = "/admin/password";
    }
    return axios.post(url, passwords).then((response) => response);
  },
  updateAccountDetails({ commit }, details) {
    return axios.post("/customer/details", details).then((response) => {
      return response;
    });
  },
  fetchAccountDetails({ commit }) {
    return axios.get("/customer/details").then((response) => {
      return response;
    });
  },
  fetchCountries({ commit }) {
    return axios.get("/country").then((response) => {
      return response;
    });
  },
  storeProduct({ commit }, product) {
    const form = new FormData();
    for (let [key, value] of Object.entries(product)) {
      if (key == "images") {
        value.forEach((image) => {
          form.append("images[]", image);
        });
      } else if (key == "categories") {
        value.forEach((category) => {
          form.append("categories[]", category);
        });
      } else if (key == "published") {
        form.append("published", value ? 1 : 0);
      } else form.append(key, value);
    }

    return axios.post("/admin/product", form).then((response) => {
      return response;
    });
  },
  storeUser({ commit }, user) {
    const form = new FormData();
    for (let [key, value] of Object.entries(user)) {
      if (key == "images") {
        value.forEach((image) => {
          form.append("images[]", image);
        });
      } else if (key == "active") {
        form.append("active", value ? 1 : 0);
      } else form.append(key, value);
    }

    return axios.post("/admin/user", form).then((response) => {
      return response;
    });
  },
  updateUser({ commit }, user) {
    const form = new FormData();
    form.append("_method", "PUT");
    for (let [key, value] of Object.entries(user)) {
      if (key == "images") {
        value.forEach((image) => {
          form.append("images[]", image);
        });
      } else if (key == "active") {
        form.append("active", value ? 1 : 0);
      } else form.append(key, value);
    }

    return axios.post(`/admin/user/${user.id}`, form).then((response) => {
      return response;
    });
  },
  updateProduct({ commit }, product) {
    const form = new FormData();
    form.append("_method", "PUT");
    for (let [key, value] of Object.entries(product)) {
      if (key == "images") {
        value.forEach((image) => {
          form.append("images[]", image);
        });
      } else if (key == "categories") {
        value.forEach((category) => {
          form.append("categories[]", category);
        });
      } else if (key == "published") {
        form.append("published", value ? 1 : 0);
      } else form.append(key, value);
    }

    return axios.post(`/admin/product/${product.id}`, form).then((response) => {
      return response;
    });
  },
  fetchCategories({ commit }, options = {}) {
    return axios
      .get(options?.url || "/admin/category", {
        params: options.params,
      })
      .then((response) => {
        return response;
      });
  },
  fetchCategoriesForUser({ commit }) {
    return axios.get("/category").then((response) => {
      return response;
    });
  },
  fetchCategory({ commit }, id) {
    return axios.get(`/admin/category/${id}`).then((response) => {
      return response;
    });
  },
  storeCategory({ commit }, category) {
    return axios.post("/admin/category", category).then((response) => {
      return response;
    });
  },
  updateCategory({ commit }, category) {
    return axios
      .put(`/admin/category/${category.id}`, category)
      .then((response) => {
        return response;
      });
  },
  fetchCategoriesTree({ commit }) {
    return axios.get("/admin/categories/tree").then((response) => {
      return response;
    });
  },
  fetchProducts({ commit }, options = {}) {
    return axios
      .get(options?.url || "/admin/product", {
        params: options?.params,
      })
      .then((response) => {
        return response;
      });
  },

  fetchProduct({ commit }, id) {
    return axios.get(`/admin/product/${id}`).then((response) => {
      return response;
    });
  },
  fetchProductsForUser({ commit }, options = {}) {
    return axios
      .get(options?.url || "/product", {
        params: options?.params,
      })
      .then((response) => {
        return response;
      });
  },
  fetchProductForUser({ commit }, by) {
    let url;
    if (by?.id) {
      url = `/product/${by.id}`;
    } else if (by?.slug) {
      url = `/product/${by.slug}`;
    }
    return axios.get(url).then((response) => {
      return response;
    });
  },
  fetchUsers({ commit }, options) {
    return axios
      .get(options?.url || "/admin/user", {
        params: options.params,
      })
      .then((response) => {
        return response;
      });
  },
  fetchUser({ commit }, id) {
    return axios.get(`/admin/user/${id}`).then((response) => {
      return response;
    });
  },
  fetchDashboard({ commit }, period) {
    return axios
      .get("/admin/dashboard", {
        params: {
          period,
        },
      })
      .then((response) => {
        return response;
      });
  },
  fetchReports({ commit }, period) {
    return axios
      .get("/admin/report", {
        params: {
          period,
        },
      })
      .then((response) => {
        return response;
      });
  },

  removeProduct({ commit }, id) {
    return axios.delete(`/admin/product/${id}`).then((response) => {
      return response;
    });
  },
  removeCategory({ commit }, id) {
    return axios.delete(`/admin/category/${id}`).then((response) => {
      return response;
    });
  },
  removeUser({ commit }, id) {
    return axios.delete(`/admin/user/${id}`).then((response) => {
      return response;
    });
  },

  // Cart
  async uploadCurrentCart({ commit, getters }) {
    if (getters.cartItems.length > 0) {
      return axios
        .post("/cart", { items: await getters.cartItems })
        .then((response) => {
          return response;
        });
    }
  },
  fetchCartItems({ commit }) {
    return axios.get("/cart").then((response) => {
      commit("setCart", response.data);
      return response;
    });
  },
  async updateCart({ commit, getters, dispatch }, { product_id, quantity }) {
    const obj = await getters.cartItems;
    let newQuantity = quantity;

    const item = (await getters.cartItems).find(
      (item) => item.product_id == product_id
    );

    // Update the new quantity from the cart's related item if existed.
    if (item) {
      newQuantity += +item.quantity;
    }
    // If the new quantity is lower than 1 remove it
    if (newQuantity <= 0) {
      return dispatch("removeItemFromCart", product_id);
    } else {
      return axios
        .put(`/cart`, { product_id, quantity: newQuantity })
        .then((response) => {
          commit("updateCart", { product_id, quantity: newQuantity });
          return response;
        });
    }
  },

  async removeItemFromCart({ commit }, item_id) {
    return axios
      .delete(`/cart/${item_id}`)
      .then((response) => {
        commit("removeItemFromCart", item_id);
        return response;
      })
      .catch(({ response }) => {
        if (response.status == 401) {
          commit("removeItemFromCart", item_id);
        }
      });
  },

  // Orders (user)
  fetchOrders({ commit }, options = {}) {
    return axios
      .get(options?.url || "/order", {
        params: options.params,
      })
      .then((response) => {
        return response;
      });
  },

  fetchOrder({ commit }, id) {
    return axios.get(`/order/${id}`).then((response) => {
      return response;
    });
  },
  orderFulfilment({ commit }, session_id) {
    return axios
      .post(
        `/payment/success`,
        {},
        {
          params: { session_id },
        }
      )
      .then((response) => {
        return response;
      });
  },

  // Orders (admin)
  fetchOrdersForAdmin({ commit }, options = {}) {
    return axios
      .get(options?.url || "/admin/order", {
        params: options.params,
      })
      .then((response) => {
        return response;
      });
  },
  checkoutOrder({ commit }, order) {
    const host = import.meta.env.VITE_BASE_URL;
    const success_url = host + "/order";
    const cancel_url = host + "/cancel";
    return axios
      .post(`/checkout/${order.id}`, {
        success_url,
        cancel_url,
      })
      .then((response) => {
        return response;
      });
  },

  checkout({ commit, state }) {
    const host = import.meta.env.VITE_BASE_URL;
    const success_url = host + "/order";
    const cancel_url = host + "/cancel";
    return axios
      .post(`/checkout`, {
        items: state.cartItems,
        success_url,
        cancel_url,
      })
      .then((response) => {
        return response;
      });
  },
};

export default actions;
