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
export function setGuestID(state, guestID) {
  state.user.guestID = guestID;
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
    price: item.product.price * item.quantity,
    title: item.product.title,
    images: item.product.images,
  }));

  // Update the  cart Items cookie
  document.cookie = "cartItems=" + JSON.stringify(state.cartItems) + ";";
}

export async function updateCart(state, updatedCartItem) {
  let cartItems = JSON.parse((await document.cookie.split("=")[1]) || "[]");
  let item = cartItems.find(
    (item) => item.product_id == updatedCartItem.product_id
  );

  // If item already exists increase it's quantity and price
  if (item) {
    item.quantity = updatedCartItem.quantity;
    item.price = updatedCartItem.product.price * updatedCartItem.quantity;
  }
  // If not exists add it
  else if (updatedCartItem.quantity > 0) {
    cartItems.push({
      product_id: updatedCartItem.product_id,
      quantity: updatedCartItem.quantity,
      price: updatedCartItem.product.price * updatedCartItem.quantity,
      title: updatedCartItem.product.title,
      images: updatedCartItem.product.images,
    });
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
