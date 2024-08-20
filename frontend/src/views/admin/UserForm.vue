<template>
    <div class="flex flex-col gap-3">
        <div class="h-[7%]">
            <h2 class="text-2xl font">
                {{ updateForm ? "Edit User" : "Create User" }}
            </h2>
        </div>
        <Card>
            <Spinner
                v-if="loading"
                size="14"
                :title="updateForm ? 'Fetching User' : null"
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
                        <Select
                            id="type"
                            label="User type"
                            :options="userTypes"
                            v-model="user.type"
                            placeholder="Select user type"
                            :errors="errors['type']"
                        />
                        <Input
                            type="text"
                            id="name"
                            label="Name"
                            v-model="user.name"
                            placeholder="User name"
                            :errors="errors['name']"
                        />

                        <Input
                            type="text"
                            id="email"
                            label="Email"
                            v-model="user.email"
                            placeholder="Email"
                            :errors="errors['email']"
                        />
                        <Input
                            type="password"
                            id="password"
                            label="Password"
                            v-model="user.password"
                            :errors="errors['password']"
                        />
                        <Input
                            type="password"
                            id="password_confirmation"
                            label="Retype Password"
                            v-model="user.password_confirmation"
                            :errors="errors['password_confirmation']"
                        />
                        <Input
                            type="text"
                            id="phone"
                            label="Phone number"
                            v-model="user.phone"
                            placeholder="Phone"
                            :errors="errors['phone']"
                        />
                        <Input
                            type="checkbox"
                            id="acitve"
                            label="Active"
                            v-model="user.active"
                            :errors="errors['active']"
                        />
                        <!-- Image -->

                        <!-- <image-upload
                            v-model="user.image"
                            :errors="errors['images']"
                        /> -->
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
                            :to="{ name: 'admin.user.index' }"
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
import { useRoute, useRouter } from "vue-router";
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
const userTypes = ref([
    {
        label: "Admin",
        value: "admin",
    },
    {
        label: "Client",
        value: "client",
    },
]);

const loading = ref(true);
const errorMsg = ref("");
const errors = ref({});
const user = ref({
    name: "",
    email: "",
    type: null,
    images: [],
    active: true,
    phone: " ",
});

function submit() {
    let action = "storeUser";
    if (updateForm.value) {
        action = "updateUser";
    }
    store
        .dispatch(action, user.value)
        .then((response) => {
            let message = response.data?.message;
            store.commit("notify", {
                type: "success",
                message,
            });
            router.push({ name: "admin.user.index" });
        })
        .catch((exception) => {
            errorMsg.value = exception.response.data?.message;
            errors.value = exception.response.data?.errors || {};
        });
}

function fetchUser() {
    return store.dispatch("fetchUser", route.params.id).then((response) => {
        user.value = response.data;
    });
}
onMounted(async () => {
    try {
        // fetch the user if the route is for update
        if (route.params.id) {
            updateForm.value = true;
            await fetchUser();
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
