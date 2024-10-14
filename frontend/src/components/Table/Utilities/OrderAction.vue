<template>
  <div :class="['flex gap-2 justify-center items-center  w-full ']">
    <router-link
      :to="{ name: 'order', params: { id: data.id } }"
      :class="[
        'px-2 py-1 flex items-center justify-center text-white rounded bg-emerald-500 hover:bg-emerald-400 transition-all',
      ]"
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
          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
        />
      </svg>
    </router-link>
    <div
      @click.prevent="pay"
      v-if="data.status == 'unpaid' || data.status == 'pending'"
      :class="[
        'px-2 py-1 text-white rounded bg-purple-600 hover:bg-purple-500 transition-all',
      ]"
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
          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
        />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { toRaw } from "vue";
import { useStore } from "vuex";

const { data } = defineProps(["data"]);
const store = useStore();

function pay() {
  store
    .dispatch("checkoutOrder", data)
    .then((response) => {
      window.open(response.data, "_blank").focus();
    })
    .catch(({ response }) => {
      store.commit("notify", {
        type: "error",
        message: response.data.message,
      });
    });
}
</script>

<style scoped></style>
