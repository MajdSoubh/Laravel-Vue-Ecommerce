<template>
  <div class="flex justify-center items-center">
    <img width="100px" :src="data?.images[0]?.path" />
  </div>
</template>

<script setup>
const { data } = defineProps(["data"]);
</script>

<style scoped></style>
