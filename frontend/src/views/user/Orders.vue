<template>
    <div class="m-3">
        <div class="shadow-gray rounded-md">
            <!-- Table -->
            <div class="flex mt-4 pt-4 justify-center items-center px-4">
                <Table
                    :attributes="tableHeader"
                    :data="orders"
                    :sortField="params.sort_field"
                    :sortDirection="params.sort_direction"
                    :loading="loading"
                />
            </div>
        </div>
        <!-- Footer -->
        <div v-if="!loading" class="flex justify-center my-4">
            <Pagination :links="links" :changePage="handlePageChange" />
        </div>
    </div>
</template>
<script setup>
import { computed, onMounted, defineComponent, ref } from "vue";
import { useStore } from "vuex";
import Table from "../../components/user/OrdersTable.vue";
import Pagination from "../../components/Pagination.vue";

// const showRemoveDialog = ref(false);
const store = useStore();
const loading = ref(true);
const links = ref({});
const order = ref({});
const orders = ref([]);
const params = ref({
    sort_field: "",
    sort_direction: "",
    search: "",
    per_page: "6",
});

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
        title: "Status",
        name: "status",
        component: defineComponent({
            template: `<div :class="['flex justify-center items-center  w-full ']"><div :class="['px-2 py-0.5 capitalize text-white rounded-md bg-green-500'
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
                            <router-link :to="{name:'order',params:{id:data.id}}" :class="['px-2 py-1 flex items-center justify-center text-white rounded bg-emerald-500 hover:bg-emerald-400 transition-all']">
                                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>

                            </router-link>
                            <div @click.prevent="pay" v-if="data.status=='unpaid'||data.status=='pending'" :class="['px-2 py-1 text-white rounded bg-purple-600 hover:bg-purple-500 transition-all']">
                                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
</svg>

                            </div>

                        </div>`,
            props: ["data"],
            methods: {
                pay: function () {
                    store
                        .dispatch("checkoutOrder", this.data)
                        .then((response) => {
                            window.open(response.data, "_blank").focus();
                        })
                        .catch(({ response }) => {
                            store.commit("notify", {
                                type: "error",
                                message: response.data.message,
                            });
                        });
                },
            },
        }),
    },
];

function sort() {
    params.value.sort_field = this.name;
    params.value.sort_direction =
        params.value.sort_direction == "desc" ? "asc" : "desc";
    fetchOrders();
}

function fetchOrders(url = null) {
    loading.value = true;
    store
        .dispatch("fetchOrders", { params: params.value, url })
        .then((response) => {
            orders.value = response.data.data;
            links.value = response.data.meta.links;
            loading.value = false;
        });
}

function handlePageChange(link) {
    if (!link.url || link.active) {
        return;
    }
    fetchOrders(link.url);
}

onMounted(() => {
    fetchOrders();
});
</script>
