<template>
  <div
    id="cartMenu"
    :class="[
      'w-0  h-screen  py-6 overflow-hidden fixed top-0 right-0 bg-white   flex  flex-col gap-5  transition-all rounded-br-xl ',
      { ' w-screen md:w-[24rem] max-w-[100vw] px-4 active ': show },
    ]"
  >
    <!-- Logo + Header -->
    <div class="flex items-start justify-between">
      <div class="text-lg font-medium text-gray-900">Shopping cart</div>
      <div class="ml-3 flex h-7 items-center">
        <button
          type="button"
          class="relative -m-2 p-2 text-purple-500 hover:text-purple-400 transition-all"
          @click="toggle"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="size-5"
          >
            <path
              d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
            />
          </svg>
        </button>
      </div>
    </div>
    <Spinner size="16" v-if="loading" color="#a956cf" />

    <div v-else class="grow">
      <!-- No items -->
      <div
        v-if="items.length == 0"
        class="h-[90vh] grow items-stretch flex place-content-center my-auto"
      >
        <p class="self-center text-gray-500">You don't have items yet</p>
      </div>
      <!-- Cart -->
      <div v-else class="flex flex-col h-full">
        <div class="mt-5 overflow-auto grow">
          <ul class="-my-6 divide-y divide-gray-200">
            <li v-for="item in items" :key="item.id" class="flex py-6">
              <div
                class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200"
              >
                <img
                  :src="item?.images[0]?.path"
                  class="h-full w-full object-contain"
                />
              </div>

              <div class="ml-4 flex flex-1 flex-col">
                <div>
                  <div
                    class="flex justify-between text-base font-medium text-gray-900"
                  >
                    <h3>
                      {{ item.title }}
                    </h3>
                    <p class="ml-4">{{ item.price }}</p>
                  </div>
                </div>
                <div class="flex flex-1 items-end justify-between text-sm">
                  <p class="text-gray-500">Qty {{ item.quantity }}</p>

                  <div class="flex gap-2">
                    <button
                      @click="updateCart(item.product_id, 1)"
                      type="button"
                      class="transition-all font-medium text-purple-500 border border-purple-400 rounded-lg hover:bg-purple-500 hover:text-white"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5"
                      >
                        <path
                          d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"
                        />
                      </svg>
                    </button>
                    <button
                      @click="updateCart(item.product_id, -1)"
                      type="button"
                      class="transition-all font-medium text-purple-500 border border-purple-400 rounded-lg hover:bg-purple-500 hover:text-white"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                    <button
                      @click="removeItemFromCart(item.product_id)"
                      type="button"
                      class="transition-all font-medium text-purple-500 border border-purple-400 rounded-lg hover:bg-purple-500 hover:text-white"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5"
                      >
                        <path
                          d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="border-t border-gray-200 px-4 py-4 mt-auto sm:px-6">
          <div class="flex justify-between text-base font-medium text-gray-900">
            <p>Subtotal</p>
            <p>${{ totalPrice }}</p>
          </div>
          <p class="mt-0.5 text-sm text-gray-500">
            Shipping and taxes calculated at checkout.
          </p>
          <button
            @click="handleCheckout"
            class="transition-all mt-6 w-full flex items-center justify-center rounded-md border border-transparent bg-[#a956cf] px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-[#994ebb]"
          >
            Checkout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, onMounted, ref, watch, computed } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import Spinner from "../../components/Spinner.vue";

const emits = defineEmits(["toggle"]);
const router = useRouter();
const { show } = defineProps(["show"]);
const items = computed(() => store.getters.cartItems);
const totalPrice = ref(0);
const store = useStore();
const updateCart = inject("updateCart");
const removeItemFromCart = inject("removeItemFromCart");
const loading = ref(true);

onMounted(async () => {
  loading.value = true;

  await fetchCartItems();

  loading.value = false;
});

function fetchCartItems() {
  return store.dispatch("fetchCartItems");
}
store.watch(
  (state) => state.cartItems,
  (cartItems) => {
    calculateTotalPrice(cartItems);
  },
  { deep: true }
);

function calculateTotalPrice(cartItems) {
  let _totalPrice = 0;
  cartItems.map((item) => {
    _totalPrice += item.price;
  });
  totalPrice.value = Math.round(_totalPrice);
}

function handleCheckout() {
  // Open the popup immediately on user action
  const popup = window.open("about:blank", "_blank");

  // Close Cart
  toggle();
  store
    .dispatch("checkout")
    .then((response) => {
      // Update the popup URL after the request succeeds
      if (popup) {
        popup.location.href = response.data;
        popup.focus();
      }
    })
    .catch(({ response }) => {
      // Close the popup if the request fails
      if (popup) {
        popup.close();
      }

      // Show an error message
      store.commit("notify", {
        type: "error",
        message: response.data.message,
      });
    });
}

// Close the Cart menu when a click happened outside the Cart container
document.body.addEventListener("click", (event) => {
  const cartElement = document.getElementById("cartMenu");
  if (cartElement.classList.contains("active")) {
    if (!cartElement.contains(event.target)) {
      toggle();
    }
  }
});

function toggle() {
  emits("toggle");
}
</script>
