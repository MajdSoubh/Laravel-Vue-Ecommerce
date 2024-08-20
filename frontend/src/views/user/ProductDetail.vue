<template>
    <Spinner size="16" v-if="loading" color="#a956cf"> </Spinner>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4">
        <!-- Gallery -->
        <div class="flex-col gap-2">
            <!-- Image Display -->
            <div class="flex items-center gap-1">
                <!-- Arrow left -->
                <div @click="previousImage" :class="['cursor-pointer w-[5%]']">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        :class="['size-6', { hidden: currentImageIndex == 0 }]"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 19.5 8.25 12l7.5-7.5"
                        />
                    </svg>
                </div>
                <!-- Image view -->
                <div class="h-[400px] w-[92%] flex items-center justify-center">
                    <img
                        :src="currentImage"
                        alt=""
                        class="block object-contain w-full h-full"
                    />
                </div>
                <!-- Arrow right -->
                <div @click="nextImage" class="cursor-pointer w-[5%]">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        :class="[
                            'size-6',
                            {
                                hidden:
                                    currentImageIndex ==
                                    product.images.length - 1,
                            },
                        ]"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                        />
                    </svg>
                </div>
            </div>

            <!-- Gallery -->
            <div class="flex justify-between h-[120px] gap-2 items-center">
                <!-- Arrow left -->
                <div
                    @click="swapLeft"
                    class="cursor-pointer h-full place-content-center items-stretch text-slate-500 bg-slate-100 rounded-s-xl hover:bg-slate-200 hover:text-white transition-colors"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        :class="['size-5', { hidden: false }]"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <!-- Image -->
                <div class="flex gap-4 overflow-hidden" ref="galleryContainer">
                    <div
                        class="w-[100px] h-[100px]"
                        v-for="(image, index) in product.images"
                        :key="image.id"
                    >
                        <img
                            @click="setCurrentImage(index)"
                            class="min-h-[100px] min-w-[100px] object-contain"
                            :src="image.path"
                            alt=""
                        />
                    </div>
                </div>
                <!-- Arrow right -->
                <div
                    @click="swapRight"
                    class="cursor-pointer h-full place-content-center items-stretch text-slate-500 bg-slate-100 rounded-e-xl hover:bg-slate-200 hover:text-white transition-colors"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Description -->
        <div class="flex flex-col gap-3">
            <!-- Title & price -->
            <div>
                <!-- Title -->
                <p class="font-medium">{{ product.title }}</p>
                <span class="block font-bold">{{ "$" + product.price }}</span>
            </div>
            <!-- Quantity -->
            <div class="flex justify-between">
                <span>Quantity :</span>
                <input
                    v-model="quantity"
                    type="number"
                    class="block w-[100px] placeholder:text-gray-400 text-sm rounded-xl ring-0 border border-gray-400 text-gray-500 py-0.5 my-0 focus:border-[#a956cf] focus:shadow-purple focus:ring-0 transition-all"
                />
            </div>
            <!-- Add to Cart -->
            <button
                @click="updateCart(product.id, quantity)"
                class="px-2 py-[0.3rem] place-content-center text-white flex items-center gap-1 border border-[#a956cf] rounded bg-[#a956cf] hover:bg-[#984abc] hover:shadow-purple transition-all"
            >
                Add to Cart
            </button>
            <!-- Description -->
            <div class="text-gray-600" v-html="product.description"></div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import Spinner from "../../components/Spinner.vue";

const store = useStore();
const route = useRoute();
const slug = route.params.slug;
const product = ref({ images: [] });
const currentImage = ref(null);
const galleryContainer = ref(null);
const updateCart = inject("updateCart");
const quantity = ref(1);
const loading = ref(true);
let currentImageIndex = null;

onMounted(() => {
    fetchProduct();
});

function fetchProduct() {
    loading.value = true;
    store.dispatch("fetchProductForUser", { slug }).then((response) => {
        product.value = response.data;
        currentImage.value = response.data?.images[0]?.path;
        currentImageIndex = 0;
        loading.value = false;
    });
}

function previousImage() {
    if (currentImageIndex > 0) {
        currentImageIndex--;
        currentImage.value = product.value.images[currentImageIndex]?.path;
    }
}

function nextImage() {
    if (currentImageIndex < product.value.images.length - 1) {
        currentImageIndex++;
        currentImage.value = product.value.images[currentImageIndex]?.path;
    }
}
function swapLeft() {
    galleryContainer.value.scrollBy({
        top: 0,
        left: -108,
        behavior: "smooth",
    });
    // console.log(galleryContainer.value.offsetWidth);
}

function swapRight() {
    galleryContainer.value.scrollBy({
        top: 0,
        left: 108,
        behavior: "smooth",
    });
    // console.log(galleryContainer.value.offsetWidth);
}

function setCurrentImage(imageIndex) {
    currentImageIndex = imageIndex;
    currentImage.value = product.value.images[currentImageIndex].path;
}
</script>
