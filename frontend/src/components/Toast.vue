<template>
    <!-- Toast -->
    <div
        v-if="notification.show"
        class="absolute top-[10%] right-[1px] z-40 animate-fade-in-left"
    >
        <div
            class="min-h-12 bg-white flex items-stretch justify-between gap-1 relative rounded-s-md shadow border border-gray-400"
        >
            <!-- Icon -->
            <div
                :class="[
                    notification.type == 'success'
                        ? 'bg-green-500'
                        : notification.type == 'error'
                        ? 'bg-red-500'
                        : 'bg-blue-500',
                    'w-12 text-white p-2 flex items-center justify-center',
                ]"
            >
                <!-- Success -->
                <svg
                    v-if="notification.type == 'success'"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-7 min-w-30"
                >
                    <path
                        fill-rule="evenodd"
                        d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z"
                        clip-rule="evenodd"
                    />
                </svg>
                <!-- Error -->
                <svg
                    v-else-if="notification.type == 'error'"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-7 min-w-30"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>
                <!-- Info -->
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-7 min-w-40"
                >
                    <path
                        fill-rule="evenodd"
                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <!-- Message -->
            <div class="flex items-center justify-center text-sm p-2">
                <p>
                    {{ notification.message }}
                </p>
            </div>
            <!-- Close Icon -->
            <div
                @click="close"
                class="w-6 cursor-pointer hover:bg-red-500 hover:text-white flex items-center justify-center p-1"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-4"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
const store = useStore();
const notification = computed(() => store.state.notification);
function close() {
    store.state.notification.show = false;
}
</script>
