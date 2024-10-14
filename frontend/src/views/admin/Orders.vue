<template>
  <div class="flex flex-col gap-3 min-w-max">
    <div class="h-[7%]">
      <h2 class="text-2xl font">Orders</h2>
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
            @change="() => getOrders()"
            class="w-16 rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
          />

          <div>found {{ orders.length }} orders</div>
        </div>
      </div>

      <!-- Table -->
      <div class="flex mt-4 justify-center items-center">
        <Table
          :attributes="tableHeader"
          :data="orders"
          :sortField="params.sort_field"
          :sortDirection="params.sort_direction"
          :loading="loading"
        />
      </div>

      <!-- Footer -->
      <div class="flex justify-end">
        <!-- Pagination -->
        <pagination :links="links" :changePage="handlePageChange" />
      </div>
    </card>
    <!-- Dialogs -->
    <OrderDialog v-model:show="showOrderDialog" :data="order"></OrderDialog>
  </div>
</template>
<script setup>
import { computed, onMounted, ref, toRaw } from "vue";
import { useStore } from "vuex";
import RemoveDialog from "../../components/Dialogs/RemoveDialog.vue";
import Table from "../../components/Table/Main/Table.vue";
import Pagination from "../../components/Pagination.vue";
import OrderStatus from "../../components/OrderStatus.vue";
import OrderDialog from "../../components/Dialogs/OrderDialog.vue";
import Card from "../../components/Card.vue";
import OrderViewPanelCell from "../../components/Table/Utilities/OrderViewPanel.vue";
import OrderStatusCell from "../../components/OrderStatus.vue";

const store = useStore();
const loading = ref(true);
const showOrderDialog = ref(false);
const links = ref({});
const order = ref({});
const orders = ref([]);
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
  getOrders();
}
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
    title: "Created By",
    name: "created_by.name",
    sort: function () {
      const sortO = { name: "created_by" };
      sort.call(sortO);
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
    component: {
      key: OrderViewPanelCell,
      props: { order, showOrderDialog },
    },
  },
];

function handlePageChange(link) {
  if (!link.url || link.active) {
    return;
  }
  getOrders(link.url);
}
function getOrders(url = null) {
  loading.value = true;
  store
    .dispatch("fetchOrdersForAdmin", { params: params.value, url })
    .then((response) => {
      orders.value = response.data.data;
      links.value = response.data.meta.links;
      loading.value = false;
    });
}

onMounted(() => {
  getOrders();
});
</script>
