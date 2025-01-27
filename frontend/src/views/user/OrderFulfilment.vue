<template>
  <div class="flex justify-center items-center min-h-screen w-full min-w-full">
    <Spinner size="20" v-if="loading" color="#a956cf" />
    <div v-else class="flex">
      <div
        class="self-middle place-content-center text-5xl px-6 font-bold border-r leading-10"
        :class="[
          { 'text-emerald-600 ': type == 'success' },
          { 'text-red-600': type == 'failure' },
        ]"
      >
        <!-- Success -->
        <svg
          v-if="type == 'success'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="currentColor"
          class="size-16 min-w-30"
        >
          <path
            fill-rule="evenodd"
            d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z"
            clip-rule="evenodd"
          />
        </svg>
        <!-- failure -->
        <svg
          v-else-if="type == 'failure'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="currentColor"
          class="size-16 min-w-30"
        >
          <path
            fill-rule="evenodd"
            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <div class="px-8">
        <p class="text-gray-500 mb-10 text-lg">
          {{ message }}
        </p>
        <div>
          <button
            @click="handleClose"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Go back home
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import Spinner from "../../components/Spinner.vue";

const store = useStore();
const loading = ref(true);
const message = ref("");
const type = ref("");
const route = useRoute();
const session_id = route.query.session_id;
const handleClose = () => {
  window.close();
};
onMounted(async () => {
  await store
    .dispatch("orderFulfilment", session_id)
    .then((response) => {
      message.value = response.data.message;
      type.value = "success";
    })
    .catch(({ response }) => {
      message.value = response.data.message;
      type.value = "failure";
    });

  loading.value = false;
});
</script>
