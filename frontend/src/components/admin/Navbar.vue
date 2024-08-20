<template>
    <nav class="bg-[#fefefe] relative shadow-md w-full rounded-br-xl">
        <div class="flex justify-between items-center h-full">
            <div class="p-2" @click="toggleMenu('toggle')">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    :class="[
                        'size-6 transition-transform',
                        menuExpanded ? 'hover:rotate-90' : 'hover:-rotate-90',
                    ]"
                >
                    <path
                        fill-rule="evenodd"
                        d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <!-- Account -->
            <div
                @click="toggleAccountMenu"
                class="p-4 h-full flex flex-row gap-2 items-center rounded-br-md relative after:absolute after:w-[1px] after:h-3/6 after:bg-black after:left-0 hover:text-white hover:bg-indigo-500 hover:after:bg-indigo-500 cursor-pointer"
            >
                <div>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-5"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="text-sm font-medium">
                    {{ store.state?.user?.data?.name }}
                </div>
                <div>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="font-bold size-3"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Account menu -->
        <div
            id="account"
            v-show="showAccount"
            class="absolute right-1 top-14 animate-fade-in-down"
            :class="showAccount ? 'active' : ''"
        >
            <ul
                class="bg-white flex flex-col items-start w-40 shadow-gray rounded"
            >
                <li
                    class="flex gap-2 px-2 py-2 cursor-pointer w-full rounded-t border-b border-b-gray-300 hover:text-white hover:bg-indigo-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6"
                    >
                        <path
                            d="M18.75 12.75h1.5a.75.75 0 0 0 0-1.5h-1.5a.75.75 0 0 0 0 1.5ZM12 6a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 12 6ZM12 18a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 12 18ZM3.75 6.75h1.5a.75.75 0 1 0 0-1.5h-1.5a.75.75 0 0 0 0 1.5ZM5.25 18.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 0 1.5ZM3 12a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 3 12ZM9 3.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM12.75 12a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0ZM9 15.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                        />
                    </svg>

                    <span> Settings </span>
                </li>
                <li
                    @click="logout"
                    class="flex gap-2 px-2 py-2 cursor-pointer w-full rounded-b hover:text-white hover:bg-indigo-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-6"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm10.72 4.72a.75.75 0 0 1 1.06 0l3 3a.75.75 0 0 1 0 1.06l-3 3a.75.75 0 1 1-1.06-1.06l1.72-1.72H9a.75.75 0 0 1 0-1.5h10.94l-1.72-1.72a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span> Logout </span>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import { ref } from "vue";

const store = useStore();
const router = useRouter();
const showAccount = ref(false);

const emits = defineEmits(["toggle"]);

const { menuExpanded } = defineProps(["menuExpanded"]);

function toggleMenu() {
    emits("toggle");
}

function logout() {
    store.dispatch("logout", true).then((response) => {
        router.push({ name: "admin.login" });
    });
}

function toggleAccountMenu() {
    setTimeout(() => {
        showAccount.value = !showAccount.value;
    }, 100);
}

// Close account details
document.body.addEventListener("click", (event) => {
    const accountElement = document.getElementById("account");

    if (!accountElement.contains(event.target)) {
        if (accountElement.classList.contains("active")) {
            showAccount.value = false;
        }
    }
});
</script>

<style></style>
