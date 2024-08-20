<template>
    <div class="flex flex-row h-screen w-full" @click="closeMenus">
        <Sidebar
            ref="sidebar"
            @toggle="toggleMenu"
            :show="showMenu"
            class="z-40"
        />
        <div class="grow">
            <Nav
                @toggleMenu="toggleMenu"
                :menu-expanded="showMenu"
                @toggleCart="toggleCart"
                :cart-expanded="showCart"
                class="z-30"
            />
            <Cart
                ref="cart"
                :show="showCart"
                @toggle="toggleCart"
                class="z-40"
            />

            <router-view></router-view>
        </div>
        <!-- Overlay -->
        <div
            :class="{
                'absolute z-30 top-0 left-0 bottom-0 right-0 bg-black opacity-30':
                    showMenu || showCart,
            }"
        ></div>
    </div>
</template>

<script setup>
import Nav from "../../components/user/Navbar.vue";
import Sidebar from "../../components/user/Sidebar.vue";
import Cart from "../../components/user/Cart.vue";
import { ref } from "vue";

let showMenu = ref(false);
let showCart = ref(false);
const cart = ref(null);
const sidebar = ref(null);

function toggleCart() {
    showCart.value = !showCart.value;
}
function toggleMenu() {
    showMenu.value = !showMenu.value;
}

function closeMenus(event) {
    if (!sidebar.value.contains(event.target) && showMenu.value) {
        showMenu.value = false;
    } else if (!cart.value.contains(event.target) && showCart.value) {
        showCart.value = false;
    }
}
</script>
<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap");

html {
    font-family: "Roboto";
}
</style>
