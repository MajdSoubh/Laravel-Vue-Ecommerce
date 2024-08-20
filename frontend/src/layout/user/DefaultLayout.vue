<template>
    <div class="flex flex-row h-screen w-full">
        <Sidebar @toggle="toggleMenu" :show="showMenu" class="z-40" />
        <div class="flex flex-col relative grow">
            <Nav
                @toggleMenu="toggleMenu"
                :menu-expanded="showMenu"
                @toggleCart="toggleCart"
                :cart-expanded="showCart"
                class="z-30"
            />
            <Toast class="z-[70]" />
            <Cart :show="showCart" @toggle="toggleCart" class="z-40" />
            <!-- User login alert -->
            <div
                v-if="
                    !store.state.user.token &&
                    route.name != 'login' &&
                    route.name != 'signup'
                "
                class="w-full bg-yellow-50 border border-b-gray-200 flex justify-between items-center gap-2 px-2 py-2 text-sm"
            >
                <p class="text-gray-600">
                    Purchase is not allowed until you do sign in !
                </p>
                <router-link
                    :to="{ name: 'login' }"
                    class="py-2 px-2 rounded bg-emerald-500 hover:bg-green-500 hover:shadow-green text-white transition-all"
                    >Sign in</router-link
                >
            </div>

            <router-view class="grow"></router-view>
        </div>
        <!-- Overlay -->
        <div
            :class="[
                'fixed z-30 top-0 left-0   bg-black opacity-30 transition-all',
                { ' bottom-0 right-0': showMenu || showCart },
            ]"
        ></div>
    </div>
</template>

<script setup>
import Nav from "../../components/user/Navbar.vue";
import Sidebar from "../../components/user/Sidebar.vue";
import Cart from "../../components/user/Cart.vue";
import Toast from "../../components/Toast.vue";
import { provide, ref } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";

let showMenu = ref(false);
let showCart = ref(false);
const cart = ref(null);
const sidebar = ref(null);
const store = useStore();
const route = useRoute();

provide("updateCart", updateCart);
provide("removeItemFromCart", removeItemFromCart);

function updateCart(product_id, quantity) {
    return store
        .dispatch("updateCart", { product_id, quantity })
        .then((response) => {
            store.commit("notify", {
                type: "success",
                message: "The Product has been added to your cart",
            });
        })
        .catch(({ response }) => {
            store.commit("notify", {
                type: "error",
                message: response.data.message,
            });
        });
}

function removeItemFromCart(product_id) {
    store.dispatch("removeItemFromCart", product_id).then((response) => {});
}

function toggleCart() {
    showCart.value = !showCart.value;
}
function toggleMenu() {
    showMenu.value = !showMenu.value;
}
</script>
<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap");

html {
    font-family: "Roboto";
}
</style>
