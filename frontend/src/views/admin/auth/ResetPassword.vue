<template>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2
                class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
                Reset Password
            </h2>
        </div>
        <form class="mt-5 space-y-6" @submit.prevent="resetPassword">
            <div>
                <div class="flex items-center justify-between">
                    <label
                        for="password"
                        class="block text-sm font-medium leading-6 text-gray-900"
                        >New Password</label
                    >
                </div>
                <div class="mt-2">
                    <input
                        v-model="payload.password"
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
                    >Retype the Password</label
                >
                <div class="mt-2">
                    <input
                        v-model="payload.passwordConfirmation"
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
                    Reset
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const loading = ref(false);
const store = useStore();
const payload = { password: "", passwordConfirmation: "" };
function resetPassword() {
    loading.value = true;
    store
        .dispatch("resetPassword", payload)
        .then(() => (loading.value = false))
        .catch(() => (loading.value = false));
}
</script>
