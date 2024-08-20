<template>
    <table class="w-full">
        <thead class="bg-gray-50 border border-gray-300">
            <tr>
                <th
                    class="cursor-pointer"
                    v-for="(key, ind) in attributes"
                    :key="ind"
                >
                    <div
                        class="flex justify-center items-center gap-2"
                        @click="
                            () => {
                                if (Object(key).hasOwnProperty('sort')) {
                                    key.sort();
                                }
                            }
                        "
                    >
                        <div class="inline-block">
                            {{ key.title }}
                        </div>
                        <div v-if="sortField == key.name">
                            <!-- Ascending -->

                            <svg
                                v-if="sortDirection == 'asc'"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2.25 4.5A.75.75 0 0 1 3 3.75h14.25a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75Zm14.47 3.97a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 1 1-1.06 1.06L18 10.81V21a.75.75 0 0 1-1.5 0V10.81l-2.47 2.47a.75.75 0 1 1-1.06-1.06l3.75-3.75ZM2.25 9A.75.75 0 0 1 3 8.25h9.75a.75.75 0 0 1 0 1.5H3A.75.75 0 0 1 2.25 9Zm0 4.5a.75.75 0 0 1 .75-.75h5.25a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <!-- Descending -->
                            <svg
                                v-if="sortDirection == 'desc'"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2.25 4.5A.75.75 0 0 1 3 3.75h14.25a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75Zm0 4.5A.75.75 0 0 1 3 8.25h9.75a.75.75 0 0 1 0 1.5H3A.75.75 0 0 1 2.25 9Zm15-.75A.75.75 0 0 1 18 9v10.19l2.47-2.47a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 1 1 1.06-1.06l2.47 2.47V9a.75.75 0 0 1 .75-.75Zm-15 5.25a.75.75 0 0 1 .75-.75h9.75a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </th>
            </tr>
        </thead>

        <tbody class="text-center">
            <!-- Loading  -->
            <tr v-if="loading">
                <td colspan="50">
                    <div class="flex justify-center mt-6">
                        <svg
                            version="1.1"
                            id="Layer_1"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px"
                            y="0px"
                            width="28px"
                            height="34px"
                            viewBox="0 0 24 30"
                            style="enable-background: new 0 0 50 50"
                            xml:space="preserve"
                        >
                            <rect
                                x="0"
                                y="10"
                                width="4"
                                height="10"
                                :fill="'#6366f1'"
                                opacity="0.2"
                            >
                                <animate
                                    attributeName="opacity"
                                    attributeType="XML"
                                    values="0.2; 1; .2"
                                    begin="0s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="height"
                                    attributeType="XML"
                                    values="10; 20; 10"
                                    begin="0s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="y"
                                    attributeType="XML"
                                    values="10; 5; 10"
                                    begin="0s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                            </rect>
                            <rect
                                x="8"
                                y="10"
                                width="4"
                                height="10"
                                :fill="'#6366f1'"
                                opacity="0.2"
                            >
                                <animate
                                    attributeName="opacity"
                                    attributeType="XML"
                                    values="0.2; 1; .2"
                                    begin="0.15s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="height"
                                    attributeType="XML"
                                    values="10; 20; 10"
                                    begin="0.15s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="y"
                                    attributeType="XML"
                                    values="10; 5; 10"
                                    begin="0.15s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                            </rect>
                            <rect
                                x="16"
                                y="10"
                                width="4"
                                height="10"
                                :fill="'#6366f1'"
                                opacity="0.2"
                            >
                                <animate
                                    attributeName="opacity"
                                    attributeType="XML"
                                    values="0.2; 1; .2"
                                    begin="0.3s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="height"
                                    attributeType="XML"
                                    values="10; 20; 10"
                                    begin="0.3s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                                <animate
                                    attributeName="y"
                                    attributeType="XML"
                                    values="10; 5; 10"
                                    begin="0.3s"
                                    dur="0.6s"
                                    repeatCount="indefinite"
                                />
                            </rect>
                        </svg>
                    </div>
                </td>
            </tr>
            <tr v-else-if="!loading && data.length == 0">
                <td colspan="50">No data available</td>
            </tr>
            <tr
                v-else
                v-for="(item, ind) in data"
                :key="ind"
                :class="[
                    ' t-row hover:bg-blue-50 cursor-pointer',
                    ind == data.length - 1
                        ? 'border-none'
                        : 'border-b-2 border-b-gray-200',
                ]"
                :data-id="item.id"
            >
                <td
                    v-for="(key, ind) in attributes"
                    :key="ind"
                    :class="['max-w-[200px]']"
                >
                    <div v-if="key.component">
                        <component :is="key.component" :data="item"></component>
                    </div>
                    <div
                        v-else
                        class="max-w-[300px] overflow-hidden text-ellipsis line-clamp-2"
                    >
                        {{
                            key.handle
                                ? key.handle(item[key.name])
                                : access(key.name, item)
                        }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<script setup>
import { onUpdated } from "vue";

const { attributes, data, sortField, sortDirection, loading } = defineProps([
    "attributes",
    "data",
    "sortField",
    "sortDirection",
    "loading",
]);

function access(path, object) {
    return path.split(".").reduce((o, key) => {
        if (!o) return null;
        return o[key];
    }, object);
}
</script>
<style scoped>
thead tr th {
    padding: 0.7rem; /* Add padding to tbody rows */
}
tbody tr td {
    padding: 1.2rem; /* Add padding to tbody rows */
}
</style>
