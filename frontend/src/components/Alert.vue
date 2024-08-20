<template>
    <div
        class="relative flex items-center justify-between py-3 px-5 bg-red-500 text-white rounded"
    >
        <div class="mr-4">
            <slot></slot>
        </div>
        <span
            @click="handleClose"
            class="absolute top-0 right-0 mr-[5px] mt-2 w-6 h-6 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </span>
    </div>
</template>
<script setup>
const { handleClose } = defineProps(["handleClose"]);
</script>
<style></style>
