<template>
  <div class="flex flex-col gap-3 min-w-max">
    <div class="h-[7%] flex justify-between items-center">
      <h2 class="text-2xl font">Products</h2>
      <router-link
        :to="{ name: 'admin.product' }"
        class="px-4 py-2 bg-indigo-500 border border-indigo-300 text-sm rounded-md text-white hover:shadow-indigo transition-shadow"
      >
        Add new Product
      </router-link>
    </div>
    <card>
      <!-- Header -->
      <div class="flex justify-between gap-4 items-center">
        <div class="flex items-center gap-2">
          <label for="perPage">Per Page</label>
          <input
            type="number"
            id="perPage"
            v-model="params.per_page"
            @change="() => getProducts()"
            class="w-16 rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
          />

          <div>found {{ products.length }} products</div>
        </div>
        <div>
          <input
            type="text"
            placeholder="Type to search products"
            @change="() => getProducts()"
            v-model="params.search"
            id="search"
            class="w-full rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="flex mt-4 justify-center items-center">
        <Table
          :attributes="tableHeader"
          :data="products"
          :sortField="params.sort_field"
          :sortDirection="params.sort_direction"
          :loading="loading"
        />
      </div>

      <!-- Footer -->
      <div class="flex justify-end">
        <!-- Pagination -->
        <Pagination :links="links" :changePage="handlePageChange" />
      </div>
    </card>
    <RemoveDialog
      v-model:show="showRemoveDialog"
      :id="product.id"
      :remove="removeProduct"
    ></RemoveDialog>
  </div>
</template>
<script setup>
import { computed, defineComponent, onMounted, ref, toRaw } from "vue";
import { useStore } from "vuex";
import RemoveDialog from "../../components/Dialogs/RemoveDialog.vue";
import Table from "../../components/Table.vue";
import Pagination from "../../components/Pagination.vue";
import Card from "../../components/Card.vue";

const showRemoveDialog = ref(false);
const store = useStore();
const loading = ref(true);
const links = ref({});
const product = ref({});
const products = ref([]);
const params = ref({
  sort_field: "",
  sort_direction: "",
  search: "",
  per_page: "5",
});

function sort() {
  params.value.sort_field = this.name;
  params.value.sort_direction =
    params.value.sort_direction == "desc" ? "asc" : "desc";
  getProducts();
}
const tableHeader = [
  {
    title: "#",
    name: "id",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Title",
    name: "title",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Price",
    name: "price",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Description",
    name: "description",
    sort: function () {
      sort.call(this);
    },
    component: defineComponent({
      template: `<div v-html="data.description">

            </div>`,
      props: ["data"],
    }),
  },
  {
    title: "Published",
    name: "published",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Images",
    name: "images",
    component: defineComponent({
      template: `<div class="flex justify-center items-center">
            <img width='100px' :src="data?.images[0]?.path" \>
            </div>`,
      props: ["data"],
    }),
    // handle: (image) => {
    //     return image.length;
    // },
  },
  {
    title: "Action",
    name: "action",
    component: defineComponent({
      template: `     <div class="flex justify-center items-center gap-2">
                            <router-link :to="{name:'admin.product',params:{id:data.id}}"   class="px-3 py-2 bg-indigo-500 border border-indigo-300 text-sm rounded-md text-white hover:shadow-indigo transition-shadow"
              ><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z"
                                    />
                                </svg>
                            </router-link>
                            <div @click="toggleRemoveDisplay($event)" class="px-3 py-2 bg-red-700 border border-red-300 text-sm rounded-md text-white hover:shadow transition-shadow"
              >
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
  <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
</svg>

                            </div>

                            </div
                    >`,
      props: ["data"],

      methods: {
        toggleRemoveDisplay: function ({ target }) {
          product.value = structuredClone(toRaw(this.data));
          showRemoveDialog.value = !showRemoveDialog.value;
        },
      },
    }),
  },
];

function handlePageChange(link) {
  if (!link.url || link.active) {
    return;
  }
  getProducts(link.url);
}
function getProducts(url = null) {
  loading.value = true;
  store
    .dispatch("fetchProducts", { params: params.value, url })
    .then((response) => {
      products.value = response.data.data;
      links.value = response.data.meta.links;
      loading.value = false;
    });
}
function removeProduct() {
  store.dispatch("removeProduct", product.value.id).then((response) => {
    store.commit("notify", {
      type: "success",
      message: "The Product has been deleted successfully",
    });
    getProducts();
  });
  showRemoveDialog.value = !showRemoveDialog.value;
}
onMounted(() => {
  getProducts();
});
</script>
