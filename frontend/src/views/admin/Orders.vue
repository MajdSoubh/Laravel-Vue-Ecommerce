<template>
    <div class="flex flex-col gap-3 min-w-max">
        <div class="h-[7%]">
            <h2 class="text-2xl font">Orders</h2>
        </div>
        <card>
            <!-- Header -->
            <div class="flex justify-between gap-4 items-center">
                <div class="flex items-center gap-2">
                    <label for="perPage">Per Page</label>
                    <input
                        type="number"
                        id="perPage"
                        v-model="params.per_page"
                        @change="() => getOrders()"
                        class="w-16 rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
                    />

                    <div>found {{ orders.length }} orders</div>
                </div>
            </div>

            <!-- Table -->
            <div class="flex mt-4 justify-center items-center">
                <Table
                    :attributes="tableHeader"
                    :data="orders"
                    :sortField="params.sort_field"
                    :sortDirection="params.sort_direction"
                    :loading="loading"
                />
            </div>

            <!-- Footer -->
            <div class="flex justify-end">
                <!-- Pagination -->
                <pagination :links="links" :changePage="handlePageChange" />
            </div>
        </card>
        <!-- Dialogs -->
        <OrderDialog v-model:show="showOrderDialog" :data="order"></OrderDialog>
    </div>
</template>
<script setup>
import { computed, defineComponent, onMounted, ref, toRaw } from "vue";
import { useStore } from "vuex";
import RemoveDialog from "../../components/Dialogs/RemoveDialog.vue";
import Table from "../../components/Table.vue";
import Pagination from "../../components/Pagination.vue";
import OrderStatus from "../../components/OrderStatus.vue";
import OrderDialog from "../../components/Dialogs/OrderDialog.vue";
import Card from "../../components/Card.vue";

const store = useStore();
const loading = ref(true);
const showOrderDialog = ref(false);
const links = ref({});
const order = ref({});
const orders = ref([]);
const params = ref({
    sort_field: "",
    sort_direction: "",
    search: "",
    per_page: "5",
});

function sort() {
    params.value.sort_field = this.name;
    params.value.sort_direction =
        params.value.sort_direction == "desc" ? "asc" : "desc";
    getOrders();
}
const tableHeader = [
    {
        title: "Order",
        name: "id",
        sort: function () {
            sort.call(this);
        },
        handle: (value) => "#" + value,
    },
    {
        title: "Date",
        name: "created_at",
        sort: function () {
            sort.call(this);
        },
    },
    {
        title: "Created By",
        name: "created_by.name",
        sort: function () {
            const sortO = { name: "created_by" };
            sort.call(sortO);
        },
    },
    {
        title: "Status",
        name: "status",
        component: defineComponent({
            template: `<div :class="['flex justify-center items-center  w-full ']"><div :class="['px-2 capitalize text-white rounded bg-green-500'
            ,{'bg-green-500':data.status=='paid'||data.status=='completed'}
            ,{'bg-red-500':data.status=='failed'||data.status=='cancelled'}
            ,{'bg-gray-500':data.status=='shipped'||data.status=='unpaid'}
            ,{'bg-yellow-500':data.status=='pending'}]">
            {{data.status}}
            </div>
            </div>`,
            props: ["data"],
        }),

        sort: function () {
            sort.call(this);
        },
    },
    {
        title: "Total Price",
        name: "total_price",
        sort: function () {
            sort.call(this);
        },
        handle: (value) => "$" + value,
    },
    {
        title: "Actions",
        name: "published",
        component: defineComponent({
            template: `<div :class="['flex gap-2 justify-center items-center  w-full ']">
                            <div @click="toggleOrderDisplay($event)" :class="['px-2 py-1 flex items-center justify-center text-white rounded bg-indigo-500 py-2']">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>

                            </div>

                        </div>`,
            props: ["data"],
            methods: {
                toggleOrderDisplay: function ({ target }) {
                    order.value = structuredClone(toRaw(this.data));
                    showOrderDialog.value = !showOrderDialog.value;
                },
            },
        }),
    },
];

function handlePageChange(link) {
    if (!link.url || link.active) {
        return;
    }
    getOrders(link.url);
}
function getOrders(url = null) {
    loading.value = true;
    store
        .dispatch("fetchOrdersForAdmin", { params: params.value, url })
        .then((response) => {
            orders.value = response.data.data;
            links.value = response.data.meta.links;
            loading.value = false;
        });
}

onMounted(() => {
    getOrders();
});
</script>
