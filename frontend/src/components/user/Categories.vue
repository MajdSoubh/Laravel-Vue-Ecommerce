<template>
    <div
        class="relative w-full mt-3 px-3 flex gap-2 justify-center items-center"
    >
        <div
            ref="container"
            class="flex flex-wrap gap-2 place-content-center scroll-smooth w-full"
        >
            <div
                v-for="category in categories"
                :key="category.id"
                :class="[
                    ' relative cursor-pointer rounded-2xl border border-[#a956cf] text-sm text-[#a956cf] hover:text-white hover:bg-[#a956cf] px-3 py-1 transition-all',
                    ,
                    {
                        'text-[#fff] bg-[#a956cf]': isActive(category.name),
                    },
                ]"
            >
                <input
                    @change="emit('change')"
                    type="checkbox"
                    :value="category.name"
                    v-model="model"
                    class="absolute cursor-pointer opacity-0 top-0 left-0 w-full h-full"
                />
                {{ category.name }}
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useStore } from "vuex";

const model = defineModel();
const categories = ref([]);
const store = useStore();
const emit = defineEmits(["change"]);
const container = ref(null);

function swapLeft() {
    container.value.scrollBy({ left: -200, behavior: "smooth" });
}
function swapRight() {
    container.value.scrollBy({ left: 200, behavior: "smooth" });
}
function fetchCategories() {
    return store.dispatch("fetchCategoriesForUser").then((response) => {
        categories.value = response.data;
    });
}
function isActive(category) {
    const ind = model.value.findIndex((cat) => category == cat);
    if (ind >= 0) {
        return true;
    }
}
onMounted(() => {
    fetchCategories();
});
</script>
