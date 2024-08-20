<template>
    <div>
        <div
            :class="[
                shouldReverse() ? 'flex-row items-center' : 'flex-col',
                ,
                'flex   gap-2',
            ]"
        >
            <label
                :for="id"
                :class="[
                    shouldReverse() ? 'order-2' : '',
                    'block  text-gray-900',
                ]"
            >
                {{ label }}
            </label>
            <!-- Checkbox & Radio -->
            <div v-if="type == 'checkbox' || type == 'radio'" class="switch">
                <input
                    :id="id"
                    :type="type"
                    :name="name"
                    @change="$emit('update:modelValue', $event.target.checked)"
                    :checked="model"
                    :placeholder="placeholder"
                    :class="[
                        classes,
                        { 'shadow-red border border-red-400': errors?.length },
                        'rounded-md py-1 bg-[#fff] text-indigo-500 border border-indigo-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow',
                    ]"
                />
                <span class="slider round"></span>
            </div>
            <input
                v-else
                :id="id"
                v-model="model"
                :type="type"
                :placeholder="placeholder"
                :class="[
                    classes,
                    { 'shadow-red border border-red-400': errors?.length },
                    'block w-full rounded-md py-1 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow',
                ]"
            />
        </div>
        <div
            v-if="errors"
            class="flex flex-col items-start justify-center mt-1"
        >
            <div v-for="(error, ind) in errors" :key="ind">
                <span class="text-red-500 text-sm block font-normal">
                    * {{ error }}
                </span>
            </div>
        </div>
    </div>
</template>
<script setup>
import { defineModel } from "vue";
const model = defineModel();
const { label, type, id, classes, errors, name } = defineProps({
    label: String,
    id: String,
    type: String,
    classes: String,
    placeholder: String,
    name: String,
    errors: [Array, String],
});

function shouldReverse() {
    if (type === "radio" || type === "checkbox") {
        return true;
    }
    return false;
}
</script>
<style scoped>
/* The switch - the box around the slider */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 23px;
}

/* Hide default HTML checkbox */
.switch input {
    opacity: 0;
    position: absolute;
    z-index: 10;
    width: 100%;
    height: 100%;
}

/* The slider */
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: 0.4s;
    transition: 0.4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    top: 50%;
    left: 2px;
    transform: translate(0, -50%);
    background-color: white;
    -webkit-transition: 0.4s;
    transition: 0.4s;
}

input:checked + .slider {
    background-color: #2196f3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196f3;
}

input:checked + .slider:before {
    -webkit-transform: translate(26px, -50%);
    -ms-transform: translate(26px, -50%);
    transform: translate(26px, -50%);
}

/* Rounded sliders */
.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
