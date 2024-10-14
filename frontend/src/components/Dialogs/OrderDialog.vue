<template>
  <Dialog v-model="show">
    <div>
      <!-- Header -->
      <div class="flex flex-col gap-2 w-full">
        <!-- Order number -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Order #</h2>
          <span>{{ data.id }}</span>
        </div>
        <!-- Order date -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Order Date</h2>
          <span>{{ data.created_at }}</span>
        </div>
        <!-- Order status -->
        <div class="flex gap-5">
          <h2 class="font-bold w-44">Order Status</h2>
          <OrderStatus :data="data" class="!justify-start" />
        </div>
        <!-- Order total -->
        <div class="flex gap-5">
          <h2 class="font-bold w-24">Total Price</h2>
          <span>{{ "$" + data.total_price }}</span>
        </div>
      </div>
      <!-- Items -->
      <div class="mt-7 p-3">
        <div>
          <ul class="-my-6 divide-y-2 divide-gray-200">
            <li v-for="item in data.items" :key="item.id" class="flex py-3">
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
      <button
        @click="
          () => {
            show = false;
          }
        "
        class="transition-all mt-5 w-full flex items-center justify-center rounded-md border border-transparent bg-emerald-500 px-6 py-2 text-base font-medium text-white shadow-sm hover:bg-emerald-600"
      >
        Close
      </button>
    </div>
  </Dialog>
</template>
<script setup>
import Dialog from "./Dialog.vue";
import OrderStatus from "../OrderStatus.vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { defineModel } from "vue";

const show = defineModel("show");
const { data } = defineProps(["data"]);
</script>
