<template>
    <div class="w-full flex flex-col justify-center items-center">
        <div
            :class="[
                { 'border-red-300': errors?.length },
                'p-2 w-full min-h-32 sm:w-3/4 md:w-3/4 relative flex items-center justify-center border-2 border-dashed text-gray-400 border-gray-300 bg-white rounded-xl',
            ]"
        >
            <!-- Header if images not available -->
            <div
                v-if="images_URIs.length == 0"
                class="flex flex-col items-center justify-center"
            >
                <input
                    type="file"
                    multiple
                    class="cursor-pointer z-20 block w-full absolute top-0 bottom-0 opacity-0"
                    @change="onImageChoose"
                />
                <!-- Plus Button -->
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-9 text-slate-300"
                >
                    <path
                        fill-rule="evenodd"
                        d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                        clip-rule="evenodd"
                    />
                </svg>

                <div class="text-sm font-normal text-black">
                    <span class="text-indigo-600 font-medium"
                        >Upload a file</span
                    >
                    or drag and drop
                </div>
            </div>
            <!-- Header if images available -->
            <div
                v-if="images_URIs.length != 0"
                class="flex flex-col items-center"
            >
                <div>Ready to Upload</div>
                <!-- Chosen images Gallery -->
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-2">
                    <div
                        v-for="(image, ind) in images_URIs"
                        :key="ind"
                        class="relative flex flex-col items-center justify-start"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="block cursor-pointer size-5 z-40 absolute top-0 right-0 text-red-500 rounded-full shadow-sm"
                            @click="deleteImage(ind)"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <img
                            :src="image.src"
                            :alt="`uploaded_image_{$ind}`"
                            class="w-20 h-20 object-cover rounded-md"
                        />
                        <div
                            class="w-20 text-xs mt-2 flex flex-col items-center"
                        >
                            <p
                                class="line-clamp-2 overflow-hidden text-ellipsis"
                            >
                                {{ image.name }}
                            </p>

                            <p>{{ ` (${image.size} kB)` }}</p>
                        </div>
                    </div>
                    <div
                        class="relative flex flex-col items-center justify-start"
                    >
                        <div
                            class="w-20 h-20 flex items-center justify-center border rounded-md border-dashed border-gray-400"
                        >
                            <input
                                type="file"
                                multiple
                                class="cursor-pointer z-20 block w-full absolute top-0 bottom-0 opacity-0"
                                @change="onImageChoose"
                            />
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="size-10"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="errors"
            class="flex flex-col items-start justify-center mt-1"
        >
            <div v-for="(error, ind) in errors" :key="ind">
                <span class="text-red-500 text-sm block"> * {{ error }} </span>
            </div>
        </div>
    </div>
</template>
<script setup>
import { defineModel, ref, watch, onMounted } from "vue";
import axios from "../../axios";
const { errors } = defineProps({
    errors: [Array, Object, String],
});
const images = defineModel();
const images_URIs = ref([]);
let mounted = false;

onMounted(async () => {
    if (images.value.length) {
        images.value = await Promise.all(
            images.value.map(async (image) => {
                const response = await axios.get(image.path, {
                    responseType: "blob",
                });

                // Get the blob data
                const blob = response.data;

                // Create File
                const imageFile = new File([blob], "image.png", {
                    type: blob.type,
                });

                // Create a URL for the Blob
                const imageUrlObject = URL.createObjectURL(blob);

                // Push it to the images_URIs as encoded string so we could display it
                images_URIs.value.push({
                    name: image.title,
                    src: imageUrlObject,
                    size: Math.round(imageFile.size / 1024),
                });

                return imageFile;
            })
        );
    }

    mounted = true;
});

watch(images, () => {
    if (!mounted) return;

    images_URIs.value = [];

    for (let image of images.value) {
        const reader = new FileReader();
        reader.onload = () => {
            images_URIs.value.push({
                src: reader.result,
                name: image.name,
                size: Math.round(image.size / 1024),
            });
        };
        reader.readAsDataURL(image);
    }
});

function onImageChoose(ev) {
    const files = [...ev.target.files];
    images.value = [...images.value, ...files];
}
function deleteImage(index) {
    images.value.splice(index, 1);
    images_URIs.value.splice(index, 1);
}
</script>
<style scoped></style>
