<template>
    <div
        class="flex flex-col h-[450px] justify-between p-2 pb-4 gap-3 border border-gray-200 rounded-md hover:border-purple-600 transition-colors animate-fade-in-down shadow bg-white"
    >
        <router-link
            :to="{ name: 'product', params: { slug: data.slug } }"
            class="flex flex-col h-[88%] justify-between gap-2"
        >
            <!-- Images -->
            <div class="w-[90%] h-[50%] self-center mt-2">
                <img
                    :src="data?.images[0]?.path"
                    :alt="data?.images?.title"
                    class="block object-contain w-full h-full rounded-lg hover:scale-105 transition-transform"
                />
            </div>
            <!-- Title & Description -->
            <div class="h-[50%] flex flex-col gap-1 px-[6px]">
                <h2 class="font-bold text-base self-center">
                    {{ data.title }}
                </h2>
                <div
                    v-html="data?.description"
                    class="text-base text-ellipsis overflow-hidden line-clamp-4"
                ></div>
                <h5 class="font-bold">${{ data.price }}</h5>
            </div>
        </router-link>
        <!-- Buttons -->
        <div class="h-[10%] flex justify-between mx-2">
            <button
                class="w-10 h-10 rounded-full border border-1 border-[#a956cf] flex items-center justify-center text-[#a956cf] hover:bg-[#a956cf] hover:text-white transition-all"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                    />
                </svg>
            </button>
            <button
                @click="updateCart(data.id, 1)"
                class="px-2 text-white flex items-center gap-1 border border-[#a956cf] rounded bg-[#a956cf] hover:bg-[#984abc] hover:shadow-purple transition-all"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.0"
                    stroke="currentColor"
                    class="size-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                    />
                </svg>
                Add to Cart
            </button>
        </div>
    </div>
</template>
<script setup>
import { inject } from "vue";
import { useStore } from "vuex";

const { data } = defineProps(["data"]);
const store = useStore();
const updateCart = inject("updateCart");
</script>
