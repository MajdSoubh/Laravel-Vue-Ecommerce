<script setup></script>

<template>
  <Spinner size="16" v-if="loading" color="#a956cf" />

  <div v-else class="flex flex-col justify-between gap-4">
    <!-- No items yet -->
    <div
      class="flex justify-center my-auto place-content-center"
      v-if="products.length == 0"
    >
      No items found in the Store
    </div>
    <div v-else>
      <div class="flex flex-col gap-3 items-center justify-center">
        <!-- Categories -->
        <Categories v-model="params.categories" @change="fetchProducts" />
        <!-- Search -->
        <div class="relative w-[90%] flex items-center justify-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-gray-400 absolute left-3 top-[50%] translate-y-[-50%]"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
            />
          </svg>

          <input
            type="text"
            placeholder="What are you looking for ?"
            @change="() => fetchProducts()"
            v-model="params.search"
            class="block w-[100%] placeholder:text-gray-400 text-sm rounded-xl ring-0 border border-gray-400 text-gray-500 py-1 pl-12 my-0 focus:border-indigo-400 focus:shadow-indigo focus:ring-0 transition-all"
          />
        </div>
      </div>
      <div
        class="p-5 grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <Product
          v-for="product in products"
          :key="product.id"
          :data="product"
        />
      </div>
    </div>
    <!-- Footer -->
    <div v-if="!loading" class="flex justify-center mb-4">
      <Pagination :links="links" :changePage="handlePageChange" />
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useStore } from "vuex";
import Categories from "../../components/user/Categories.vue";
import Product from "../../components/user/Product.vue";
import Pagination from "../../components/Pagination.vue";
import Spinner from "../../components/Spinner.vue";

const store = useStore();
const products = ref([]);
const params = ref({ search: null, per_page: 4, categories: [] });
const loading = ref(true);
const links = ref([]);

onMounted(() => {
  fetchProducts();
});

function fetchProducts(url = null) {
  loading.value = true;
  store
    .dispatch("fetchProductsForUser", { params: params.value, url })
    .then((response) => {
      products.value = response.data.data;
      links.value = response.data.meta.links;
      loading.value = false;
    });
}
function handlePageChange(link) {
  if (!link.url || link.active) {
    return;
  }
  fetchProducts(link.url);
}
</script>
