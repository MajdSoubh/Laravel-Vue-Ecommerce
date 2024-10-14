<template>
  <div :class="['flex justify-center items-center  w-full ']">
    <div
      :class="[
        'px-2 capitalize text-white rounded bg-green-500',
        {
          'bg-green-500': data.status == 'paid' || data.status == 'completed',
        },
        {
          'bg-red-500': data.status == 'failed' || data.status == 'cancelled',
        },
        {
          'bg-gray-500': data.status == 'shipped' || data.status == 'unpaid',
        },
        { 'bg-yellow-500': data.status == 'pending' },
      ]"
    >
      {{ data.status }}
    </div>
  </div>
</template>
<script setup>
const { data } = defineProps(["data"]);
</script>
