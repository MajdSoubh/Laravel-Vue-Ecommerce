<template>
    <div>
        <div
            :class="[
                'px-2 capitalize text-white rounded bg-green-500',
                {
                    'bg-green-500': status == 'paid' || status == 'completed',
                },
                {
                    'bg-red-500': status == 'failed' || status == 'cancelled',
                },
                {
                    'bg-gray-500': status == 'shipped' || status == 'unpaid',
                },
                { 'bg-yellow-500': status == 'pending' },
            ]"
        >
            {{ status }}
        </div>
    </div>
</template>
<script setup>
const { status } = defineProps(["status"]);
console.log(status);
</script>
