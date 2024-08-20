<template>
    <div>
        <Bar
            :data="data"
            :chartOptions="chartOptions"
            :chartId="chartId"
            :width="width"
            :height="height"
            :cssClasses="cssClasses"
            :styles="styles"
            :plugins="plugins"
        />
    </div>
</template>
<script setup>
import { Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);
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
