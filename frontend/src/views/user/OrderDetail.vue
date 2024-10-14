<template>
  <Spinner size="16" v-if="loading" color="#a956cf" />
  <div v-else class="mt-4 mx-1 p-2">
    <div class="shadow-gray rounded-md flex flex-col gap-4 w-full h-full p-4">
      <!-- Header -->
      <div class="flex flex-col gap-2 w-full">
        <!-- Order number -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Order #</h2>
          <span>{{ order.id }}</span>
        </div>
        <!-- Order date -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Order Date</h2>
          <span>{{ order.created_at }}</span>
        </div>
        <!-- Order status -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Order Status</h2>
          <OrderStatus :data="order" />
        </div>
        <!-- Order total -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Total Price</h2>
          <span>{{ "$" + order.total_price }}</span>
        </div>
      </div>
      <!-- Items -->
      <div class="mt-7 p-3">
        <div>
          <ul class="-my-6 divide-y-2 divide-gray-200">
            <li v-for="item in order.items" :key="item.id" class="flex py-3">
              <!-- image -->
              <div
                class="h-[120px] w-[120px] flex-shrink-0 overflow-hidden rounded-md border border-gray-200"
              >
                <img
                  :src="item.product?.images[0]?.path"
                  class="h-full w-full object-contain"
                />
              </div>
              <!-- description -->
              <div class="ml-4 flex flex-1 flex-col">
                <div>
                  <div
                    class="flex justify-between text-base font-medium text-gray-900"
                  >
                    <h3 class="overflow-hidden text-ellipsis line-clamp-3">
                      {{ item.product.title }}
                    </h3>
                    <p class="ml-4 font-bold">
                      {{ "$" + item.unit_price }}
                    </p>
                  </div>
                </div>
                <div class="mt-5 text-sm">
                  <p class="text-gray-700">Qty {{ item.quantity }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <!-- Go back -->
      <router-link
        :to="{ name: 'order.index' }"
        class="transition-all mt-auto w-full flex items-center justify-center rounded-md border border-transparent bg-emerald-500 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-emerald-600"
      >
        Go to Orders
      </router-link>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import Card from "../../components/Card.vue";
import Spinner from "../../components/Spinner.vue";
import OrderStatus from "../../components/OrderStatus.vue";

const route = useRoute();
const store = useStore();
const order = ref({
  id: null,
  status: null,
  total_price: null,
  created_at: null,
});
const loading = ref(true);
const id = route.params.id;

onMounted(() => {
  fetchOrder();
});

function fetchOrder() {
  loading.value = true;
  store.dispatch("fetchOrder", id).then((response) => {
    order.value = response.data;
    loading.value = false;
  });
}
</script>
