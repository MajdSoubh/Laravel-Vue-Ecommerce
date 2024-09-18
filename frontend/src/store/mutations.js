export function setUser(state, { user, isAdmin = false }) {
  state.user.data = user;
  state.user.isAdmin = isAdmin;
  if (user) {
    sessionStorage.setItem("USER_DATA", JSON.stringify(user));
  } else {
    sessionStorage.removeItem("USER_DATA");
  }
}

export function setToken(state, token) {
  state.user.token = token;
  if (token) {
    sessionStorage.setItem("TOKEN", token);
  } else {
    sessionStorage.removeItem("TOKEN");
  }
}
export function setUniqueID(state, uniqueID) {
  state.user.uniqueID = uniqueID;
}

export function forgetPassword(state, email) {}

export function notify(state, { type, message }) {
  state.notification.show = true;
  state.notification.type = type;
  state.notification.message = message;
  setTimeout(() => {
    state.notification.show = false;
  }, state.notification.period);
}

// Cart
export function setCart(state, data = []) {
  state.cartItems = data.map((item) => ({
    product_id: item.product_id,
    quantity: item.quantity,
  }));

  // Update the  cart Items cookie
  document.cookie = "cartItems=" + JSON.stringify(state.cartItems) + ";";
}

export async function updateCart(state, { product_id, quantity }) {
  let cartItems = JSON.parse((await document.cookie.split("=")[1]) || "[]");
  let item = cartItems.find((item) => item.product_id == product_id);

  // If item already exists increase it's quantity
  if (item) {
    item.quantity = quantity;
  }
  // If not exists add it
  else if (quantity > 0) {
    cartItems.push({ product_id, quantity });
  }

  // Update the  cart Items cookie
  document.cookie = "cartItems=" + JSON.stringify(cartItems) + ";";

  state.cartItems = cartItems;
}

export async function removeItemFromCart(state, product_id) {
  let cartItems = JSON.parse((await document.cookie.split("=")[1]) || "[]");

  cartItems = cartItems.filter((item) => item.product_id != product_id);

  document.cookie = "cartItems=" + JSON.stringify(cartItems) + ";";
  state.cartItems = cartItems;
}
