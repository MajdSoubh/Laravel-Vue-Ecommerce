<template>
    <aside
        id="sideMenu"
        :class="[
            'w-0  h-screen py-6 overflow-hidden fixed top-0 left-0 bg-white   flex flex-col gap-5  transition-all rounded-br-xl',
            ,
            { ' w-[24rem] px-2 active': show },
        ]"
    >
        <div class="p-4">
            <!-- Home -->
            <router-link
                :to="{ name: 'home' }"
                @click="toggle"
                class="group relative flex gap-x-6 rounded-lg p-4 hover:bg-gray-50 hover:text-purple-secondary transition-all"
                active-class="bg-gray-100 "
            >
                <div
                    class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                        />
                    </svg>
                </div>
                <div>
                    <a href="#" class="font-semibold text-gray-900">
                        Home
                        <span class="absolute inset-0"></span>
                    </a>
                    <p class="mt-1 text-gray-600">Get a list of our products</p>
                </div>
            </router-link>

            <!-- Orders -->
            <router-link
                :to="{ name: 'order.index' }"
                @click="toggle"
                class="group relative flex gap-x-6 rounded-lg p-4 hover:bg-gray-50 hover:text-purple-secondary transition-all"
                active-class="bg-gray-100 "
            >
                <div
                    class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3"
                        />
                    </svg>
                </div>
                <div>
                    <a href="#" class="font-semibold text-gray-900">
                        Orders
                        <span class="absolute inset-0"></span>
                    </a>
                    <p class="mt-1 text-gray-600">
                        Watch and manage your orders
                    </p>
                </div>
            </router-link>

            <!-- Account -->
            <router-link
                :to="{ name: 'profile' }"
                @click="toggle"
                class="group relative flex gap-x-6 rounded-lg p-4 hover:bg-gray-50 hover:text-purple-secondary transition-all"
                active-class="bg-gray-100 "
            >
                <div
                    class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                        />
                    </svg>
                </div>
                <div>
                    <a href="#" class="font-semibold text-gray-900">
                        Profile
                        <span class="absolute inset-0"></span>
                    </a>
                    <p class="mt-1 text-gray-600">Update your profile</p>
                </div>
            </router-link>
        </div>
        <div class="mt-auto divide-x divide-gray-900/5 bg-gray-50">
            <button
                @click="
                    () => {
                        toggle();
                        logout();
                    }
                "
                class="w-full flex items-center justify-center gap-x-2.5 p-3 font-semibold text-gray-900 hover:bg-gray-100"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"
                    />
                </svg>

                Logout
            </button>
        </div>
    </aside>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";

const { show } = defineProps(["show"]);
const emits = defineEmits(["toggle"]);
const store = useStore();

// Close the SideMenu when a click happened outside the SideMenu container
document.body.addEventListener("click", (event) => {
    const sideMenuElement = document.getElementById("sideMenu");
    if (sideMenuElement.classList.contains("active")) {
        if (!sideMenuElement.contains(event.target)) {
            toggle();
        }
    }
});

function toggle() {
    emits("toggle");
}
function logout() {
    store.dispatch("logout").then((response) => {
        console.log(response);
    });
}
</script>

<style></style>
