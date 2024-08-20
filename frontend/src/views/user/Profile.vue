<template>
    <Spinner size="16" v-if="loading" color="#a956cf"> </Spinner>
    <div v-else class="w-full mx-auto mt-4 p-3">
        <div
            class="flex flex-col md:flex-row justify-center items-center gap-6"
        >
            <!-- Profile Details -->
            <div class="bg-white w-full md:w-max p-4 rounded-lg shadow-gray">
                <div class="mb-6">
                    <div class="mb-4">
                        <Input
                            type="text"
                            id="name"
                            v-model="details.name"
                            placeholder="Your name"
                            :errors="errors.details['name']"
                        />
                    </div>
                    <div class="mb-4">
                        <Input
                            type="text"
                            id="email"
                            v-model="details.email"
                            placeholder="Your email"
                            :errors="errors.details['email']"
                        />
                    </div>
                    <div class="mb-4">
                        <Input
                            type="text"
                            id="phone"
                            v-model="details.phone"
                            placeholder="Your phone"
                            :errors="errors.details['phone']"
                        />
                    </div>
                </div>
                <!--/ Profile Details -->

                <!-- Billing Address -->
                <div class="mb-6">
                    <h2 class="text-xl mb-5">Address</h2>
                    <div class="flex gap-3">
                        <div class="mb-4 flex-1">
                            <Input
                                type="text"
                                id="address_1"
                                v-model="details.address_1"
                                placeholder="Address 1"
                                :errors="errors.details['address_1']"
                            />
                        </div>
                        <div class="mb-4 flex-1">
                            <Input
                                type="text"
                                id="address_2"
                                v-model="details.address_2"
                                placeholder="Address 2"
                                :errors="errors.details['address_2']"
                            />
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="mb-4 flex-1">
                            <Input
                                type="text"
                                id="city"
                                v-model="details.city"
                                placeholder="City"
                                :errors="errors.details['city']"
                            />
                        </div>
                        <div class="mb-4 flex-1">
                            <Input
                                type="text"
                                id="zipcode"
                                v-model="details.zip_code"
                                placeholder="Zip code"
                                :errors="errors.details['zip_code']"
                            />
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="mb-4 flex-1">
                            <Select
                                id="country"
                                @change="handleCountryChange($event)"
                                :options="countries"
                                v-model="details.country"
                                placeholder="Select Country"
                                :errors="errors.details['country']"
                            />
                        </div>
                        <div class="mb-4 flex-1">
                            <Select
                                id="state"
                                :options="states"
                                v-model="details.state"
                                placeholder="Select state"
                                :errors="errors.details['state']"
                            />
                        </div>
                    </div>
                </div>
                <!--/ Billing Address -->

                <button
                    @click="updateAccountDetails"
                    class="transition-all mt-6 w-full flex items-center justify-center rounded border border-transparent bg-[#a956cf] px-4 py-1 text-base font-medium text-white shadow-sm hover:bg-[#994ebb]"
                >
                    Update
                </button>
            </div>

            <div
                class="bg-white self-start w-full md:w-max p-4 rounded-lg shadow-gray"
            >
                <h2 class="text-xl mb-3">Your Account</h2>

                <div class="mb-4">
                    <Input
                        type="password"
                        id="old_password"
                        v-model="passwords.old_password"
                        placeholder="Your current password"
                        :errors="errors.passwords['old_password']"
                    />
                </div>
                <div class="mb-4">
                    <Input
                        type="password"
                        id="new_password"
                        v-model="passwords.new_password"
                        placeholder="New password"
                        :errors="errors.passwords['new_password']"
                    />
                </div>
                <div class="mb-4">
                    <Input
                        type="password"
                        id="new_password_confirmation"
                        v-model="passwords.new_password_confirmation"
                        placeholder="Re-type the password"
                        :errors="errors.passwords['new_password_confirmation']"
                    />
                </div>
                <div>
                    <button
                        @click="updatePassword"
                        class="transition-all mt-6 w-full flex items-center justify-center rounded border border-transparent bg-[#a956cf] px-4 py-1 text-base font-medium text-white shadow-sm hover:bg-[#994ebb]"
                    >
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, toRaw, watch } from "vue";
import { useStore } from "vuex";
import Input from "../../components/Inputs/Input.vue";
import Select from "../../components/Inputs/Select.vue";
import Alert from "../../components/Alert.vue";
import Spinner from "../../components/Spinner.vue";

const loading = ref(true);
const errorMsg = ref({ passwords: "", details: "" });
const errors = ref({ passwords: {}, details: {} });
const same_billing = ref(false);
const store = useStore();
const details = ref({
    name: "",
    phone: "",
    email: "",
    address_1: "",
    state: null,
    city: "",
    country: null,
    zip_code: "",
});
const passwords = ref({
    old_password: "",
    new_password: "",
    new_password_confirmation: "",
});
const countries = ref([]);
const states = ref([]);

function handleCountryChange(event) {
    const _ind = countries.value.findIndex(
        (c) => c.value == event.target.value
    );
    if (_ind != -1) {
        const _states = countries.value[_ind].states;
        if (_states) {
            states.value = _states.map((s) => ({
                value: s.code,
                label: s.name,
            }));
        } else {
            states.value = [];
        }
    }
}
onMounted(async () => {
    await fetchCountries();
    await fetchAccountDetails();
    loading.value = false;
});

function fetchCountries() {
    return store.dispatch("fetchCountries").then((response) => {
        countries.value = response.data.countries.map((c) => {
            return {
                value: c.code,
                label: c.name,
                states: c.states,
            };
        });
        return response;
    });
}
function fetchAccountDetails() {
    return store.dispatch("fetchAccountDetails").then((response) => {
        details.value.name = response.data.name;
        details.value.email = response.data.email;
        details.value.phone = response.data.phone;
        details.value.address_1 = response.data.address_1;
        // details.value.address_2 = response.data.address_2;
        details.value.city = response.data.city;
        details.value.zip_code = response.data.zipcode;
        details.value.country = response.data.country_code || null;
        details.value.state = response.data.state || null;
        return response;
    });
}

function updateAccountDetails() {
    store
        .dispatch("updateAccountDetails", details.value)
        .then((response) => {
            store.commit("notify", {
                type: "success",
                message: response.data.message,
            });
            errorMsg.value.details = "";
            errors.value.details = {};
        })
        .catch(({ response }) => {
            loading.value = false;
            errorMsg.value.details = response.data?.message;
            errors.value.details = response.data?.errors || {};
        });
}

function updatePassword() {
    store
        .dispatch("updatePassword", { passwords: passwords.value })
        .then((response) => {
            store.commit("notify", {
                type: "success",
                message: response.data.message,
            });
            errorMsg.value.passwords = "";
            errors.value.passwords = {};
            passwords.old_password = "";
            passwords.new_password = "";
            passwords.new_password_confirmation = "";
        })
        .catch(({ response }) => {
            loading.value = false;
            errorMsg.value.passwords = response.data?.message;
            errors.value.passwords = response.data?.errors || {};
        });
}
</script>
