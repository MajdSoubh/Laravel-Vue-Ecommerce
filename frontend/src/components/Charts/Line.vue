<template>
    <div>
        <Line
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
import { Line } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
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
