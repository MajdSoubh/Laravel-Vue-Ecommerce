<template>
  <main class="p-5">
    <form
      @submit.prevent="submit"
      class="w-full sm:w-[400px] mx-auto p-6 my-16"
    >
      <h2 class="text-2xl font-semibold text-center mb-5">
        Create new Account
      </h2>
      <Alert
        :handleClose="
          () => {
            errorMsg = '';
          }
        "
        v-if="errorMsg"
        class="flex justify-start items-center text-sm my-2"
      >
        <span v-if="errorMsg" class="block">
          {{ errorMsg }}
        </span>
      </Alert>
      <Input
        classes="py-2"
        type="text"
        id="name"
        v-model="model.name"
        placeholder="Enter your name"
        :errors="errors['name']"
      />
      <Input
        classes="py-2"
        type="text"
        id="email"
        v-model="model.email"
        placeholder="Enter your email"
        :errors="errors['email']"
      />

      <Input
        classes="py-2"
        type="password"
        id="password"
        v-model="model.password"
        placeholder="Your password"
        :errors="errors['password']"
      />
      <Input
        classes="py-2"
        type="password"
        id="password_confirmation"
        v-model="model.password_confirmation"
        placeholder="Re-type the password"
        :errors="errors['password_confirmation']"
      />

      <div class="mt-3 mx-2 flex justify-between items-center mb-3">
        <p class="text-center text-sm text-gray-500">
          Already member?
          {{ "   " }}
        </p>
        <router-link
          :to="{ name: 'login' }"
          class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
        >
          Login to your account</router-link
        >
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full px-2 py-1 flex justify-center items-center rounded-md bg-emerald-600 text-white hover:bg-emerald-500 hover:shadow-green"
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
        Sign up
      </button>
    </form>
  </main>
</template>
<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import Input from "../../../components/Inputs/Input.vue";
import Alert from "../../../components/Alert.vue";

const loading = ref(false);
const errorMsg = ref("");
const errors = ref({});
const store = useStore();
const router = useRouter();

const model = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  remember: false,
});

function submit() {
  loading.value = true;
  store
    .dispatch("register", { user: model.value, isAdmin: false })
    .then((response) => {
      loading.value = false;
      errorMsg.value = response?.data?.message;
      router.push({ name: "home" });
    })
    .catch((exception) => {
      loading.value = false;
      errorMsg.value = exception.response.data?.message;
      errors.value = exception.response.data?.errors || {};
    });
}
</script>
