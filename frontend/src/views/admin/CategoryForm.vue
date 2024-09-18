<template>
  <div class="flex flex-col gap-3">
    <div class="h-[7%]">
      <h2 class="text-2xl font">
        {{ updateForm ? "Edit category" : "Create category" }}
      </h2>
    </div>
    <Card>
      <Spinner
        v-if="loading"
        size="14"
        :title="updateForm ? 'Fetching category' : null"
      />
      <div v-else>
        <Alert
          v-if="errorMsg"
          :handleClose="() => (errorMsg = '')"
          class="flex-col items-stretch text-sm my-2"
        >
          <span v-if="errorMsg" class="block">
            {{ errorMsg }}
          </span>
        </Alert>
        <form @submit.prevent="submit">
          <div class="flex flex-col gap-2">
            <Input
              type="text"
              id="name"
              label="Name"
              v-model="category.name"
              placeholder="Category name"
              :errors="errors['name']"
            />
            <Select
              id="parent"
              label="Select Category Parent"
              :options="categories"
              v-model="category.parent"
              placeholder="Select category type"
              :errors="errors['parent']"
            />

            <Input
              type="checkbox"
              id="acitve"
              label="Active"
              v-model="category.active"
              name="active"
              :errors="errors['active']"
            />
          </div>
          <!-- Buttons -->
          <div class="justify-end flex gap-2 mt-3">
            <button
              type="submit"
              class="px-4 py-2 bg-indigo-500 border-2 border-indigo-300 text-sm rounded-md text-white hover:shadow hover:shadow-indigo-300"
            >
              Submit
            </button>
            <router-link
              :to="{ name: 'admin.category.index' }"
              class="px-4 py-2 bg-white text-sm rounded-md text-gray-500 border-[2px] border-gray-200 hover:shadow"
            >
              Cancel
            </router-link>
          </div>
        </form>
      </div>
    </Card>
  </div>
</template>

<script setup>
import { useRouter, useRoute } from "vue-router";
import { useStore } from "vuex";
import { onMounted, ref } from "vue";
import Spinner from "../../components/Spinner.vue";
import ImageUpload from "../../components/Inputs/ImageUpload.vue";
import Card from "../../components/Card.vue";
import Alert from "../../components/Alert.vue";
import Input from "../../components/Inputs/Input.vue";
import Select from "../../components/Inputs/Select.vue";

const route = useRoute();
const router = useRouter();
const store = useStore();
const updateForm = ref(false);

const loading = ref(true);
const errorMsg = ref("");
const errors = ref({});
const categories = ref([]);
const category = ref({
  name: "",
  parent: null,
  active: true,
});

function fetchCategories() {
  return store.dispatch("fetchCategories").then((response) => {
    categories.value = response.data.data.map((category) => ({
      label: category.name,
      value: category.id,
    }));
  });
}

function submit() {
  let action = "storeCategory";
  if (updateForm.value) {
    action = "updateCategory";
  }
  store
    .dispatch(action, category.value)
    .then((response) => {
      let message = response.data?.message;
      store.commit("notify", {
        type: "success",
        message,
      });
      router.push({ name: "admin.category.index" });
    })
    .catch((exception) => {
      errorMsg.value = exception.response.data?.message;
      errors.value = exception.response.data?.errors || {};
    });
}

function fetchCategory() {
  return store.dispatch("fetchCategory", route.params.id).then((response) => {
    category.value = response.data;
    category.value.parent = category.value.parent?.id || null;
  });
}
onMounted(async () => {
  try {
    await fetchCategories();
    // fetch the category if the route is for update
    if (route.params.id) {
      updateForm.value = true;
      await fetchCategory();
    }
  } catch (exception) {
    store.commit("notify", {
      type: "error",
      message: "Unexpected error happen",
    });
  }
  loading.value = false;
});
</script>
