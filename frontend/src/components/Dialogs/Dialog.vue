<template>
    <Transition name="fade">
        <div class="z-30" v-if="show">
            <!-- Overlay -->
            <div
                class="absolute top-0 bottom-0 left-0 right-0 z-30 bg-[#6b7280bf]"
            ></div>
            <!-- Dialog -->
            <div
                class="absolute top-0 bottom-0 left-0 right-0 flex justify-center items-center"
            >
                <div
                    class="dialog p-4 rounded-md z-40 bg-white w-[95%] sm:w-2/4"
                >
                    <slot></slot>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script setup>
import { defineModel } from "vue";

const show = defineModel();
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-active .dialog,
.fade-leave-active .dialog {
    transition: all 0.3s ease;
}

.fade-enter-from .dialog {
    transform: translateY(50%);
}
.fade-enter-from .dialog {
    transform: translateY(0%);
}
.fade-leave-from .dialog {
    transform: translateY(0%);
}
.fade-leave-to .dialog {
    transform: translateY(50%);
}
</style>
