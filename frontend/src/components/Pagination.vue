<template>
    <div v-if="links.length > 3">
        <ul class="mt-2 flex items-center justify-center">
            <!-- First -->
            <a
                v-for="(link, ind) in links"
                :key="ind"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap cursor-pointer"
                :class="[
                    link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    ind === 0 ? 'rounded-l-md' : '',
                    ind === links.length - 1 ? 'rounded-r-md' : '',
                    !link.url ? ' bg-gray-100 text-gray-700' : '',
                ]"
                :disabled="link.active"
                @click.prevent="changePage(link)"
            >
                <div v-html="link.label"></div>
            </a>
        </ul>
    </div>
</template>
<script setup>
const { links, changePage } = defineProps(["links", "changePage"]);
</script>
