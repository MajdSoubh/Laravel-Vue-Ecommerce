<template>
  <div v-html="data.description"></div>
</template>

<script setup>
const { data } = defineProps(["data"]);
</script>

<style scoped></style>
