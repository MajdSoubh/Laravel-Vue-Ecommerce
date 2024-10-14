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
import { computed, onMounted, ref, toRaw } from "vue";
import { useStore } from "vuex";
import RemoveDialog from "../../components/Dialogs/RemoveDialog.vue";
import Table from "../../components/Table/Main/Table.vue";
import Pagination from "../../components/Pagination.vue";
import Card from "../../components/Card.vue";
import ActionCell from "../../components/Table/Utilities/Action.vue";
import ImageFrameCell from "../../components/Table/Utilities/ImageFrame.vue";
import DescriptionCell from "../../components/Table/Utilities/Description.vue";

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
    component: { key: DescriptionCell },
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
    component: { key: ImageFrameCell },
  },
  {
    title: "Action",
    name: "action",
    component: {
      key: ActionCell,
      props: {
        route: "admin.product",
        item: product,
        showRemoveDialog,
      },
    },
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
