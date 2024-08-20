<template>
    <div class="flex gap-2 flex-col">
        <label :for="id">{{ label }}</label>
        <div>
            <select
                :id="id"
                v-model="model"
                @change="handleChange"
                :class="[
                    {
                        'border border-red-400 shadow-red': errors,
                    },
                    {
                        'text-slate-300': !model,
                    },
                    'block w-full rounded-md py-1 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow',
                ]"
            >
                <option selected :value="null">
                    {{ placeholder }}
                </option>
                <option
                    v-for="option in options"
                    :value="option.value"
                    :key="option.value"
                    class="text-black"
                >
                    {{ option.label }}
                </option>
            </select>
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

const model = defineModel();

const emits = defineEmits(["change"]);
const { label, options, errors, placeholder, id } = defineProps([
    "label",
    "options",
    "errors",
    "placeholder",
    "id",
]);

function handleChange(event) {
    emits("change", event);
}
</script>
