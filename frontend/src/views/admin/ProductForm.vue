<template>
    <div class="flex flex-col gap-3">
        <div class="h-[7%]">
            <h2 class="text-2xl font">
                {{ updateForm ? "Edit product" : "Create Product" }}
            </h2>
        </div>
        <Card>
            <Spinner
                v-if="loading"
                size="14"
                :title="updateForm ? 'Fetching Product' : null"
            />
            <div v-else>
                <Alert
                    v-if="errorMsg"
                    :handleClose="() => (errorMsg = '')"
                    class="flex-col items-stretch text-sm my-2"
                >
                    <span v-if="errorMsg" class="block">
                        {{ errorMsg }}
                    </span>
                </Alert>
                <form @submit.prevent="submit">
                    <div class="flex flex-col gap-2">
                        <tree-select
                            v-model="product.categories"
                            id="categories"
                            :options="categories"
                            label="Category"
                            :errors="errors['categories']"
                        />
                        <Input
                            type="text"
                            id="title"
                            label="Title"
                            v-model="product.title"
                            placeholder="Product Title"
                            :errors="errors['title']"
                        />

                        <TextEditor
                            v-model="product.description"
                            label="Description"
                            placeholder="Product Description"
                            id="description"
                            :errors="errors['description']"
                        />

                        <Input
                            type="text"
                            id="price"
                            label="Price"
                            v-model="product.price"
                            placeholder="Product Price"
                            :errors="errors['price']"
                        />
                        <Input
                            type="text"
                            id="quantity"
                            label="Quantity"
                            v-model="product.quantity"
                            placeholder="Product Quantity"
                            :errors="errors['quantity']"
                        />
                        <Input
                            type="checkbox"
                            id="published"
                            label="Published"
                            v-model="product.published"
                            :errors="errors['published']"
                        />
                        <!-- Image -->

                        <image-upload
                            v-model="product.images"
                            :errors="errors['images']"
                        />
                    </div>
                    <!-- Buttons -->
                    <div class="justify-end flex gap-2 mt-3">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-500 border-2 border-indigo-300 text-sm rounded-md text-white hover:shadow hover:shadow-indigo-300"
                        >
                            Submit
                        </button>
                        <router-link
                            :to="{ name: 'admin.product.index' }"
                            class="px-4 py-2 bg-white text-sm rounded-md text-gray-500 border-[2px] border-gray-200 hover:shadow"
                        >
                            Cancel
                        </router-link>
                    </div>
                </form>
            </div>
        </Card>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import { useStore } from "vuex";
import { onMounted, ref } from "vue";
import Spinner from "../../components/Spinner.vue";
import ImageUpload from "../../components/Inputs/ImageUpload.vue";
import Card from "../../components/Card.vue";
import Alert from "../../components/Alert.vue";
import TreeSelect from "../../components/Inputs/TreeSelect.vue";
import Input from "../../components/Inputs/Input.vue";
import TextEditor from "../../components/Inputs/TextEditor.vue";

const route = useRoute();
const router = useRouter();
const store = useStore();
const updateForm = ref(false);
const categories = ref([]);
const loading = ref(true);
const errorMsg = ref("");
const errors = ref({});
const product = ref({
    title: "",
    description: "",
    images: [],
    categories: [],
    price: null,
    quantity: null,
    published: false,
});

function submit() {
    let action = "storeProduct";
    if (updateForm.value) {
        action = "updateProduct";
    }
    store
        .dispatch(action, product.value)
        .then((response) => {
            let message = response.data?.message;
            store.commit("notify", {
                type: "success",
                message,
            });
            router.push({ name: "admin.product.index" });
        })
        .catch((exception) => {
            errorMsg.value = exception.response.data?.message;
            errors.value = exception.response.data?.errors || {};
        });
}

function fetchCategoriesTree() {
    return store.dispatch("fetchCategoriesTree").then((response) => {
        categories.value = response.data;
    });
}

function fetchProduct() {
    return store.dispatch("fetchProduct", route.params.id).then((response) => {
        product.value = response.data;
        product.value.categories = product.value.categories.map(
            (category) => category.id
        );
    });
}
onMounted(async () => {
    try {
        await fetchCategoriesTree();

        // fetch the product if the route is for update
        if (route.params.id) {
            updateForm.value = true;
            await fetchProduct();
        }
    } catch (exception) {
        store.commit("notify", {
            type: "error",
            message: "Unexpected error happen",
        });
    }
    loading.value = false;
});
</script>
