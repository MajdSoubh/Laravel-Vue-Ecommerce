<template>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2
                class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
                Forget Password
            </h2>
        </div>
        <form
            class="mt-5 space-y-6"
            method="POST"
            @submit.prevent="forgetPassword"
        >
            <div>
                <label
                    for="email"
                    class="block text-sm font-medium leading-6 text-gray-900"
                    >Email address</label
                >
                <div class="mt-2">
                    <input
                        v-model="user.email"
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    class="flex w-full justify-center items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    Send Mail
                </button>
            </div>
            <p class="mt-10 text-center text-sm text-gray-500">
                Remeber your password?
                {{ " " }}
                <router-link
                    :to="{ name: 'login' }"
                    class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
                    >Sign in</router-link
                >
            </p>
        </form>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const loading = ref(false);
const store = useStore();
const user = { email: "" };
function forgetPassword() {
    loading.value = true;
    store
        .dispatch("forgetPassword", user)
        .then(() => (loading.value = false))
        .catch(() => (loading.value = false));
}
</script>
