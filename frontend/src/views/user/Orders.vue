<template>
  <div class="m-3">
    <div class="shadow-gray rounded-md">
      <!-- Table -->
      <div class="flex mt-4 pt-4 justify-center items-center px-4">
        <Table
          :attributes="tableHeader"
          :data="orders"
          :sortField="params.sort_field"
          :sortDirection="params.sort_direction"
          :loading="loading"
        />
      </div>
    </div>
    <!-- Footer -->
    <div v-if="!loading" class="flex justify-center my-4">
      <Pagination :links="links" :changePage="handlePageChange" />
    </div>
  </div>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
import Table from "../../components/user/OrdersTable.vue";
import Pagination from "../../components/Pagination.vue";
import OrderStatusCell from "../../components/OrderStatus.vue";
import OrderActionCell from "../../components/Table/Utilities/OrderAction.vue";

// const showRemoveDialog = ref(false);
const store = useStore();
const loading = ref(true);
const links = ref({});
const order = ref({});
const orders = ref([]);
const params = ref({
  sort_field: "",
  sort_direction: "",
  search: "",
  per_page: "6",
});

const tableHeader = [
  {
    title: "Order",
    name: "id",
    sort: function () {
      sort.call(this);
    },
    handle: (value) => "#" + value,
  },
  {
    title: "Date",
    name: "created_at",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Status",
    name: "status",
    component: {
      key: OrderStatusCell,
    },

    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Total Price",
    name: "total_price",
    sort: function () {
      sort.call(this);
    },
    handle: (value) => "$" + value,
  },
  {
    title: "Actions",
    name: "published",

    component: { key: OrderActionCell },
  },
];

function sort() {
  params.value.sort_field = this.name;
  params.value.sort_direction =
    params.value.sort_direction == "desc" ? "asc" : "desc";
  fetchOrders();
}

function fetchOrders(url = null) {
  loading.value = true;
  store
    .dispatch("fetchOrders", { params: params.value, url })
    .then((response) => {
      orders.value = response.data.data;
      links.value = response.data.meta.links;
      loading.value = false;
    });
}

function handlePageChange(link) {
  if (!link.url || link.active) {
    return;
  }
  fetchOrders(link.url);
}

onMounted(() => {
  fetchOrders();
});
</script>
