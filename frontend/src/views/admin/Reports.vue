<template>
  <Spinner size="16" v-if="loading"> </Spinner>
  <div v-else class="flex w-full md:min-h-full flex-col gap-3">
    <div class="flex justify-between items-center h-[7%]">
      <h2 class="text-2xl font">Reports</h2>
      <!-- Period -->
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
    <div
      class="flex items-center h-[93%] w-full justify-center flex-col sm:flex-row gap-2"
    >
      <Card class="flex h-full items-center justify-center"
        ><Bar
          :data="mostProfitableProducts"
          :height="350"
          :width="250"
          class="h-[400px] w-fit"
        />
      </Card>
      <Card class="flex h-full items-center justify-center"
        ><Line
          :data="totalOrders"
          :height="350"
          :width="250"
          class="h-[400px]"
        />
      </Card>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useStore } from "vuex";
import Card from "../../components/Card.vue";
import Bar from "../../components/Charts/Bar.vue";
import Line from "../../components/Charts/Line.vue";
import Spinner from "../../components/Spinner.vue";

const store = useStore();
const period = ref("all");
const loading = ref(true);
const totalOrders = ref({
  labels: [],
  datasets: {
    label: "customer by days",
    data: [],
    backgroundColor: "#f87979",
  },
});
const mostProfitableProducts = ref({
  labels: [],
  datasets: {
    label: "customer by days",
    data: [],
    backgroundColor: "#f87979",
  },
});
async function getData() {
  loading.value = true;
  await store.dispatch("fetchReports", period.value).then((response) => {
    totalOrders.value.labels = response.data.orders.dates;
    totalOrders.value.datasets = [];
    totalOrders.value.datasets[0] = {
      data: response.data.orders.data,
      label: "Paid Orders by days",
      backgroundColor: [
        "rgb(255, 99, 132)",
        "rgb(54, 162, 235)",
        "rgb(255, 205, 86)",
        "rgb(86, 205, 86)",
      ],
    };
    mostProfitableProducts.value.labels = response.data.products.titles;
    mostProfitableProducts.value.datasets = [];
    mostProfitableProducts.value.datasets[0] = {
      data: response.data.products.amounts,
      label: "Most Profitable products",
      backgroundColor: [
        "rgb(255, 99, 132)",
        "rgb(54, 162, 235)",
        "rgb(255, 205, 86)",
        "rgb(86, 205, 86)",
      ],
    };
  });
  loading.value = false;
}
onMounted(() => {
  getData();
});
</script>
