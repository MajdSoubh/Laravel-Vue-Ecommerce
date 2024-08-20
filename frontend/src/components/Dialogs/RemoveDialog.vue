<template>
    <Dialog v-model="show">
        <div>
            <!-- Header -->
            <div class="sm:flex sm:items-center">
                <div
                    class="mx-auto sm:mt-2 flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                >
                    <ExclamationTriangleIcon
                        class="h-6 w-6 text-red-600"
                        aria-hidden="true"
                    />
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to remove this resource ?
                        </p>
                    </div>
                </div>
            </div>
            <!-- Buttons -->
            <div class="sm:flex sm:flex-row-reverse mt-4">
                <button
                    type="button"
                    @click.prevent="remove"
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                >
                    Remove
                </button>
                <button
                    type="button"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                    @click="
                        () => {
                            show = false;
                        }
                    "
                    ref="cancelButtonRef"
                >
                    Cancel
                </button>
            </div>
        </div>
    </Dialog>
</template>
<script setup>
import Dialog from "./Dialog.vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { defineModel } from "vue";

const show = defineModel("show");
const { id, remove } = defineProps(["id", "remove"]);
</script>
