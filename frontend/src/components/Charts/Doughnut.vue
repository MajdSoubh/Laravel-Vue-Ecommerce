<template>
    <Doughnut
        :data="data"
        :options="chartOptions"
        :width="width"
        :height="height"
        :cssClasses="cssClasses"
        :chartId="chartId"
        :styles="styles"
        :plugins="plugins"
    />
</template>

<script setup>
import { Doughnut } from "vue-chartjs";
import { Chart as ChartJS, Tooltip, Legend, ArcElement } from "chart.js";
ChartJS.register(Tooltip, Legend, ArcElement);

const {
    data,
    chartOptions,
    chartId,
    width,
    height,
    cssClasses,
    styles,
    plugins,
} = defineProps([
    "data",
    "chartOptions",
    "chartId",
    "width",
    "height",
    "cssClasses",
    "styles",
    "plugins",
]);
</script>

<style scoped>
#doughnut-chart {
    width: 100px;
    height: 100px;
}
</style>
