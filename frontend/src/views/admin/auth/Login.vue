<template>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2
                class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
                Sign in to your account
            </h2>
        </div>
        <div
            class="flex flex-col justify-start items-start p-3 px-5 text-sm my-2 shadow-gray"
        >
            <span class="block"> email: admin@admin </span>
            <span class="block"> password: mmmmmmmm </span>
        </div>
        <Alert
            v-if="errorMsg || Object.keys(errors).length"
            class="flex-col items-stretch text-sm mt-4"
        >
            <span v-if="errorMsg" class="block mb-2">
                {{ errorMsg }}
            </span>
            <div v-for="(errorKey, ind) in Object.keys(errors)" :key="ind">
                <div class="" v-for="(error, i) in errors[errorKey]" :key="i">
                    {{ "*  " + error }}
                </div>
            </div>
        </Alert>

        <form class="space-y-6 mt-5" @submit.prevent="login">
            <div>
                <label
                    for="email"
                    class="block text-sm font-medium leading-6 text-gray-900"
                    >Email address</label
                >
                <div class="mt-2">
                    <input
                        id="email"
                        name="email"
                        type="text"
                        v-model="model.email"
                        autocomplete="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label
                        for="password"
                        class="block text-sm font-medium leading-6 text-gray-900"
                        >Password</label
                    >
                    <div class="text-sm">
                        <router-link
                            :to="{ name: 'admin.forgetPassword' }"
                            class="font-semibold text-indigo-600 hover:text-indigo-500"
                            >Forgot password?</router-link
                        >
                    </div>
                </div>
                <div class="mt-2">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        v-model="model.password"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>

            <div class="flex justify-between gap-2">
                <div class="flex items-center justify-center gap-2">
                    <input
                        type="checkbox"
                        class="rounded-md"
                        v-model="model.remember"
                        id="remember"
                    />
                    <label for="remember">Remember me</label>
                </div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="flex w-32 p-2 justify-center items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    <svg
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        v-if="loading"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import Alert from "../../../components/Alert.vue";

const loading = ref(false);
const errorMsg = ref("");
const errors = ref({});
const store = useStore();
const router = useRouter();

const model = ref({ email: "", password: "", remember: false });

function login() {
    loading.value = true;
    store
        .dispatch("login", { user: model.value, isAdmin: true })
        .then((response) => {
            loading.value = false;
            errorMsg.value = response.data?.message;
            router.push({ name: "admin.dashboard" });
        })
        .catch((exception) => {
            loading.value = false;
            errorMsg.value = exception.response.data?.message;
            errors.value = exception.response.data?.errors || {};
        });
}
</script>
