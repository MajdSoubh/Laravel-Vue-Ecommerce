<template>
  <div class="flex flex-col gap-3 min-w-max">
    <div class="h-[7%] flex justify-between items-center">
      <h2 class="text-2xl font">Users</h2>
      <router-link
        :to="{ name: 'admin.user' }"
        class="px-4 py-2 bg-indigo-500 border border-indigo-300 text-sm rounded-md text-white hover:shadow-indigo transition-shadow"
      >
        Add new User
      </router-link>
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
            @change="() => getUsers()"
            class="w-16 rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
          />

          <div>found {{ users.length }} users</div>
        </div>
        <div>
          <input
            type="text"
            placeholder="Type to search users"
            @change="() => getUsers()"
            v-model="params.search"
            id="search"
            class="w-full rounded-md py-0.5 bg-[#fff] text-gray-900 border border-gray-300 placeholder:text-[#bdbdbd] focus:shadow-indigo focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-shadow"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="flex mt-4 justify-center items-center">
        <Table
          :attributes="tableHeader"
          :data="users"
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
    <RemoveDialog
      v-model:show="showRemoveDialog"
      :id="user.id"
      :remove="removeUser"
    ></RemoveDialog>
  </div>
</template>
<script setup>
import { computed, onMounted, ref, toRaw } from "vue";
import { useStore } from "vuex";
import RemoveDialog from "../../components/Dialogs/RemoveDialog.vue";
import Table from "../../components/Table/Main/Table.vue";
import Pagination from "../../components/Pagination.vue";
import Card from "../../components/Card.vue";
import ActionCell from "../../components/Table/Utilities/Action.vue";

const showRemoveDialog = ref(false);
const store = useStore();
const loading = ref(true);
const links = ref({});
const user = ref({});
const users = ref([]);
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
  getUsers();
}
const tableHeader = [
  {
    title: "#",
    name: "id",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Name",
    name: "name",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Email",
    name: "email",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Type",
    name: "type",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Active",
    name: "active",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Phone",
    name: "phone",
    sort: function () {
      sort.call(this);
    },
  },
  {
    title: "Action",
    name: "action",
    component: {
      key: ActionCell,
      props: {
        route: "admin.user",
        item: user,
        showRemoveDialog: showRemoveDialog,
      },
    },
  },
];

function handlePageChange(link) {
  if (!link.url || link.active) {
    return;
  }
  getUsers(link.url);
}
function getUsers(url = null) {
  loading.value = true;
  store
    .dispatch("fetchUsers", { params: params.value, url })
    .then((response) => {
      users.value = response.data.data;
      links.value = response.data.meta.links;
      loading.value = false;
    });
}
function removeUser() {
  store.dispatch("removeUser", user.value.id).then((response) => {
    store.commit("notify", {
      type: "success",
      message: "The User has been deleted successfully",
    });
    getUsers();
  });
  showRemoveDialog.value = !showRemoveDialog.value;
}
onMounted(() => {
  getUsers();
});
</script>
