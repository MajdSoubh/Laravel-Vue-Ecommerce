<template>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2
                class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
                Create New Account
            </h2>
        </div>
        <Alert
            v-if="errorMsg || Object.keys(errors).length"
            class="flex-col items-stretch text-sm mt-2"
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
        <form class="mt-3 space-y-6" method="POST" @submit.prevent="register">
            <div>
                <label
                    for="fullname"
                    class="block text-sm font-medium leading-6 text-gray-900"
                    >Full name</label
                >
                <div class="mt-2">
                    <input
                        v-model="model.name"
                        id="fullname"
                        name="name"
                        type="text"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>
            <div>
                <label
                    for="email"
                    class="block text-sm font-medium leading-6 text-gray-900"
                    >Email address</label
                >
                <div class="mt-2">
                    <input
                        v-model="model.email"
                        id="email"
                        name="email"
                        type="text"
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
                </div>
                <div class="mt-2">
                    <input
                        v-model="model.password"
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>
            <div>
                <label
                    for="password_confirmation"
                    class="block text-sm font-medium leading-6 text-gray-900"
                    >Password Confirmation</label
                >
                <div class="mt-2">
                    <input
                        v-model="model['password_confirmation']"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>
            <div>
                <button
                    :disabled="loading"
                    type="submit"
                    class="flex w-full justify-center items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    <svg
                        v-if="loading"
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                    Sign up
                </button>
            </div>
        </form>
        <p class="mt-10 text-center text-sm text-gray-500">
            Already member?
            {{ " " }}
            <router-link
                :to="{ name: 'login' }"
                class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
                >Login to your account</router-link
            >
        </p>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import Alert from "../../../components/Alert.vue";

const store = useStore();
const errorMsg = ref("");
const errors = ref({});
const router = useRouter();
const model = ref({
    name: "",
    email: "",
    password: "",
    password_Confirmation: "",
});

const loading = ref(false);

function register() {
    loading.value = true;
    store
        .dispatch("register", { user: model.value, isAdmin: true })
        .then((response) => {
            loading.value = false;
            errorMsg.value = response.data?.message;
            router.push({ name: "app.dashboard" });
        })
        .catch((exception) => {
            loading.value = false;
            errorMsg.value = exception.response.data?.message;
            errors.value = exception.response.data?.errors || {};
        });
}
</script>
