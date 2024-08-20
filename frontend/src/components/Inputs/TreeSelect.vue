<template>
    <div class="flex gap-2 flex-col">
        <label :for="id">{{ label }}</label>
        <div>
            <treeselect
                :id="id"
                v-model="model"
                :multiple="true"
                :options="options"
                :class="[
                    {
                        'rounded-md border border-red-300 shadow-red ': errors,
                    },
                    'placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0',
                ]"
            />
            <div
                v-if="errors"
                class="flex flex-col items-start justify-center mt-1"
            >
                <div v-for="(error, ind) in errors" :key="ind">
                    <span class="text-red-500 text-sm block">
                        * {{ error }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineModel } from "vue";
// import the component
import Treeselect from "@cholakovdev/vue3-treeselect";
// import the styles
import "@cholakovdev/vue3-treeselect/dist/vue3-treeselect.css";

const { label, errors, options, id } = defineProps([
    "label",
    "errors",
    "options",
    "id",
]);

const model = defineModel();
</script>
<style>
.vue-treeselect__control {
    width: initial;
}

.vue-treeselect__control:focus,
.vue-treeselect__control:focus-within {
    /* box-shadow: 0 0 0 3px rgb(68 57 202 /0.1) !important;
    border: 1px solid rgb(129 140 240) !important; */
}
</style>
