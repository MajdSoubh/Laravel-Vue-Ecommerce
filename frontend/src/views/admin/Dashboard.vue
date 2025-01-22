<template>
  <div class="flex md:h-full flex-col gap-3">
    <div class="flex justify-between items-center md:h-[7%]">
      <h2 class="text-2xl font">Dashboard</h2>
      <div class="flex gap-2 items-center">
        <label class="hidden sm:block" for="period">Change Date Period</label>
        <select
          v-model="period"
          @change="getData"
          id="period"
          class="rounded-md py-1 pl-8 bg-[#fff] text-gray-900 border-[1px] border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
        >
          <option value="1d">Last Day</option>
          <option value="1w">Last Week</option>
          <option value="2w">Last 2 Weeks</option>
          <option value="1m">Last Month</option>
          <option value="3m">Last 3 Month</option>
          <option value="6m">Last 6 Month</option>
          <option selected value="all">All Time</option>
        </select>
      </div>
    </div>
    <!-- First Row -->
    <div class="flex flex-col sm:flex-row gap-3 md:h-[21%]">
      <Card class="!p-3"
        ><div class="flex flex-col gap-2 items-center justify-center h-full">
          <p class="text-xl font-medium">Active Customer</p>
          <div class="text-2xl font-bold">
            <!-- Loading -->
            <Spinner v-if="customersCount.loading" size="10" />
            <div v-else>
              {{ customersCount.data }}
            </div>
          </div>
        </div>
      </Card>
      <Card class="!p-3"
        ><div class="flex flex-col gap-2 items-center justify-center h-full">
          <p class="text-xl font-medium">Active Product</p>
          <div class="text-2xl font-bold">
            <!-- Loading -->
            <Spinner v-if="productsCount.loading" size="10" />
            <div v-else>
              {{ productsCount.data }}
            </div>
          </div>
        </div></Card
      >
      <Card class="!p-3"
        ><div class="flex flex-col gap-2 items-center justify-center h-full">
          <p class="text-xl font-medium">Paid Orders</p>
          <div class="text-2xl font-bold">
            <!-- Loading -->
            <Spinner v-if="paidOrders.loading" size="10" />
            <div v-else>
              {{ paidOrders.data }}
            </div>
          </div>
        </div></Card
      >
      <Card class="!p-3"
        ><div class="flex flex-col gap-2 items-center justify-center h-full">
          <p class="text-xl font-medium">Total Income</p>
          <div class="text-2xl font-bold">
            <!-- Loading -->
            <Spinner v-if="latestOrders.loading" size="10" />
            <div v-else>
              {{ totalIncome.data + " $" }}
            </div>
          </div>
        </div></Card
      >
    </div>
    <!-- Second Row -->
    <div class="flex flex-col sm:flex-row gap-3 md:h-[71%]">
      <Card class="!p-3"
        ><div class="h-full flex flex-col gap-3 items-center overflow-hidden">
          <p class="text-xl font-medium">Latest Orders</p>
          <div class="w-full h-full flex flex-col items-center gap-2">
            <!-- Loading -->
            <Spinner v-if="latestOrders.loading" />

            <div
              v-else
              v-for="(order, ind) in latestOrders.data"
              :key="ind"
              class="w-full flex justify-between gap-2 rounded-md p-2 cursor-pointer hover:bg-blue-100"
            >
              <div>
                <!-- Order -->
                <div>
                  <a href="" class="text-indigo-700">
                    Order #{{ order.id + " " }}</a
                  >
                  <p class="inline-block">
                    Created at {{ order.created_at }}.<span> items </span>
                    {{ order.items_count }}
                  </p>
                </div>
                <!-- Customer -->
                <p>
                  {{ order.client.name }}
                </p>
              </div>
              <!-- Price -->
              <div class="font-bold">${{ order.total_price }}</div>
            </div>
          </div>
        </div>
      </Card>
      <div class="flex flex-col gap-3 h-full">
        <Card class="md:h-[50%] !p-3"
          ><div class="flex flex-col gap-2 items-center justify-center grow">
            <p class="text-base font-medium">Most Requested Products</p>
            <Spinner v-if="mostRequestedProducts.loading" />
            <Pie
              v-else
              :width="200"
              :height="200"
              class="text-center max-w-[150px] max-h-[150px]"
              :data="mostRequestedProducts.data"
            /></div
        ></Card>
        <Card class="md:h-[50%] !p-3"
          ><div class="w-full h-full flex flex-col gap-2 items-center">
            <p class="text-base font-medium">Latest Customer</p>
            <div class="w-full h-full flex flex-col items-center gap-[6px]">
              <!-- Loading -->
              <Spinner v-if="latestCustomers.loading" size="10" />

              <div
                v-else
                v-for="(customer, ind) in latestCustomers.data"
                :key="ind"
                class="w-full p-1 rounded-md flex gap-[6px] items-center cursor-pointer hover:bg-blue-100"
              >
                <!-- Avatar -->
                <div>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-10 text-gray-700"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <!-- Details -->
                <div>
                  <!-- name -->
                  <div class="font-bold text-base">
                    {{ customer.name }}
                  </div>
                  <div class="text-gray-700 text-sm">
                    {{ customer.email }}
                  </div>
                </div>
              </div>
            </div>
          </div></Card
        >
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useStore } from "vuex";
import Card from "../../components/Card.vue";
import Pie from "../../components/Charts/Doughnut.vue";
import Spinner from "../../components/Spinner.vue";

const store = useStore();
const customersCount = ref({ loading: true, data: 0 });
const mostRequestedProducts = ref({ loading: true, data: 0 });
const latestCustomers = ref({ loading: true, data: 0 });
const latestOrders = ref({ loading: true, data: 0 });
const productsCount = ref({ loading: true, data: 0 });
const paidOrders = ref({ loading: true, data: 0 });
const totalIncome = ref({ loading: true, data: 0 });
const period = ref("all");

async function getData() {
  customersCount.value.loading = true;
  productsCount.value.loading = true;
  latestCustomers.value.loading = true;
  latestOrders.value.loading = true;
  paidOrders.value.loading = true;
  totalIncome.value.loading = true;
  mostRequestedProducts.value.loading = true;
  await store.dispatch("fetchDashboard", period.value).then((response) => {
    customersCount.value.data = response.data.activeCustomer;
    productsCount.value.data = response.data.activeProducts;
    latestCustomers.value.data = response.data.latestCustomer;
    latestOrders.value.data = response.data.latestOrders;
    paidOrders.value.data = response.data.ordersCount;
    totalIncome.value.data = response.data.totalIncome;

    mostRequestedProducts.value.data = {
      labels: [],
      datasets: [
        {
          label: "Most requested prodcuts",
          data: [],
          backgroundColor: [
            "rgb(255, 99, 132)",
            "rgb(54, 162, 235)",
            "rgb(255, 205, 86)",
            "rgb(86, 205, 86)",
          ],
        },
      ],
    };
    mostRequestedProducts.value.data.labels =
      response.data.mostRequestedProducts.titles;
    mostRequestedProducts.value.data.datasets[0].data =
      response.data.mostRequestedProducts.quantity;
  });
  customersCount.value.loading = false;
  productsCount.value.loading = false;
  latestCustomers.value.loading = false;
  latestOrders.value.loading = false;
  paidOrders.value.loading = false;
  totalIncome.value.loading = false;
  mostRequestedProducts.value.loading = false;
}
onMounted(() => {
  getData();
});
</script>
