<template>
  <div
    v-html="data.description"
    class="text-ellipsis overflow-hidden line-clamp-3"
  ></div>
</template>

<script setup>
const { data } = defineProps(["data"]);
</script>

<style scoped></style>
